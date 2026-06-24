<?php

namespace App\Jobs;

use App\Mail\FeeReminderMail;
use App\Models\EmailLog;
use App\Models\SchoolProfile;
use App\Models\SmsLog;
use App\Models\Student;
use App\Models\Term;
use App\Services\TermiiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AbsenceAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public array $absentStudentIds,
        public string $attendanceDate,
        public int $termId,
        public int $markedByUserId,
    ) {}

    public function handle(TermiiService $termii): void
    {
        $term = Term::find($this->termId);
        $school = SchoolProfile::first();
        $schoolName = $school?->short_name ?? $school?->name ?? 'School';
        $dateFormatted = \Carbon\Carbon::parse($this->attendanceDate)->format('d M Y');

        $students = Student::with(['user', 'currentEnrollment.classArm.classLevel'])
            ->whereIn('id', $this->absentStudentIds)
            ->get();

        foreach ($students as $student) {
            $parent = $student->primaryParent();
            $className = $student->currentEnrollment?->classArm?->full_name ?? 'N/A';

            // ── SMS Alert ──
            if ($school?->sms_on_absence && $parent?->phone) {
                try {
                    $smsMessage = "Dear Parent, your ward {$student->user->full_name} ({$className}) was marked ABSENT on {$dateFormatted} for {$term?->name}. Please contact the school if this is an error. — {$schoolName}";

                    $response = $termii->sendSms(
                        phone: $parent->phone,
                        message: $smsMessage,
                        purpose: 'absence_alert'
                    );

                    SmsLog::create([
                        'sent_by' => $this->markedByUserId,
                        'recipient_phone' => $parent->phone,
                        'recipient_user_id' => $parent->id,
                        'message' => $smsMessage,
                        'termii_message_id' => $response['message_id'] ?? null,
                        'status' => $response['status'] === 'sent' ? 'Sent' : 'Pending',
                        'purpose' => 'absence_alert',
                        'cost' => $response['cost'] ?? 0,
                        'sent_at' => now(),
                    ]);

                } catch (\Exception $e) {
                    Log::error('Absence SMS failed', [
                        'student_id' => $student->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                usleep(200000); // 200ms rate limit
            }

            // ── Email Alert ──
            if ($school?->email_on_absence && $parent?->email) {
                try {
                    $subject = "Absence Alert — {$student->user->full_name} — {$dateFormatted}";
                    $body = "<p>Dear Parent/Guardian,</p>
                        <p>Your ward <strong>{$student->user->full_name}</strong> ({$className}) was marked <strong style='color:#dc3545;'>ABSENT</strong> on <strong>{$dateFormatted}</strong> for <strong>{$term?->name}</strong>.</p>
                        <p>Please contact the school if this is an error or if you have already submitted an excuse note.</p>
                        <p>Thank you,<br>{$schoolName}</p>";

                    Mail::to($parent->email)->queue(
                        new FeeReminderMail($subject, $body)
                    );

                    EmailLog::create([
                        'student_id' => $student->id,
                        'email' => $parent->email,
                        'subject' => $subject,
                        'body' => $body,
                        'status' => 'queued',
                        'sent_by_user_id' => $this->markedByUserId,
                        'sent_at' => now(),
                    ]);

                } catch (\Exception $e) {
                    Log::error('Absence email failed', [
                        'student_id' => $student->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        Log::info('Absence alert job completed', [
            'count' => count($this->absentStudentIds),
            'date' => $this->attendanceDate,
            'sms_enabled' => $school?->sms_on_absence ?? false,
            'email_enabled' => $school?->email_on_absence ?? false,
        ]);
    }
}
