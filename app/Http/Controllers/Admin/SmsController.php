<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBulkSmsJob;
use App\Models\ClassArm;
use App\Models\SmsLog;
use App\Models\Student;
use App\Models\StudentFeeLedger;
use App\Models\Term;
use App\Services\TermiiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class SmsController extends Controller
{
    public function __construct(
        private TermiiService $termii,
    ) {}

    /* ── Compose SMS Form ───────────────────────────────── */
    public function compose(Request $request)
    {
        $term = Term::getCurrent();
        $classArms = ClassArm::with('classLevel')->orderBy('arm')->get();

        // Pre-select defaulters if coming from defaulters page
        $preselected = [];
        if ($request->query('target') === 'defaulters') {
            $students = Student::with('user')
                ->where('status', 'Active')
                ->whereHas('feeLedger', function ($q) use ($term) {
                    $q->where('term_id', $term->id)
                      ->whereRaw('net_amount - amount_paid > 0');
                })
                ->get();

            foreach ($students as $student) {
                $balance = StudentFeeLedger::where('student_id', $student->id)
                    ->where('term_id', $term->id)
                    ->sum(DB::raw('net_amount - amount_paid'));
                if ($balance > 0) {
                    $preselected[] = [
                        'id'           => $student->id,
                        'name'         => $student->user->full_name,
                        'phone'        => $student->user->phone,
                        'user_id'      => $student->user_id,
                        'parent_phone' => $student->primaryParent()?->phone,
                        'parent_user_id' => $student->primaryParent()?->id,
                        'class'        => $student->currentEnrollment?->classArm?->full_name ?? 'N/A',
                        'balance'      => $balance,
                    ];
                }
            }
        }

        $templates = [
            'fee_reminder' => "Dear Parent, your ward {{student_name}} ({{class}}) has an outstanding fee balance of ₦{{balance}} for {{term}}. Please make payment at the bursary or via the parent portal. Thank you. — {{school}}",
            'payment_due'  => "Reminder: {{term}} fees for {{student_name}} are due. Outstanding balance: ₦{{balance}}. Kindly settle to avoid penalties. — {{school}}",
            'general'      => "",
        ];

        $balance = $this->termii->checkBalance();

        return view('admin.sms.compose', compact('classArms', 'term', 'preselected', 'templates', 'balance'));
    }

    /* ── Send SMS ───────────────────────────────────────── */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'target_type' => 'required|in:all_defaulters,class_arm,specific_students',
            'class_arm_id' => 'nullable|required_if:target_type,class_arm|exists:class_arms,id',
            'student_ids' => 'nullable|required_if:target_type,specific_students|array',
            'student_ids.*' => 'exists:students,id',
            'message' => 'required|string|max:320',
            'use_parent_phone' => 'nullable|boolean',
        ]);

        $term = Term::getCurrent();
        if (!$term) {
            Alert::error('Error', 'No active term found.');
            return back();
        }

        // Build recipient list
        $recipients = [];
        $school = \App\Models\SchoolProfile::first();
        $schoolName = $school?->short_name ?? $school?->name ?? 'School';

        switch ($validated['target_type']) {
            case 'all_defaulters':
                $students = Student::with(['user', 'currentEnrollment.classArm.classLevel'])
                    ->where('status', 'Active')
                    ->whereHas('feeLedger', function ($q) use ($term) {
                        $q->where('term_id', $term->id)
                          ->whereRaw('net_amount - amount_paid > 0');
                    })
                    ->get();

                foreach ($students as $student) {
                    $balance = StudentFeeLedger::where('student_id', $student->id)
                        ->where('term_id', $term->id)
                        ->sum(DB::raw('net_amount - amount_paid'));
                    if ($balance <= 0) continue;

                    $phone = $validated['use_parent_phone']
                        ? ($student->primaryParent()?->phone ?? $student->user->phone)
                        : $student->user->phone;

                    $userId = $validated['use_parent_phone']
                        ? ($student->primaryParent()?->id ?? $student->user_id)
                        : $student->user_id;

                    if ($phone) {
                        $recipients[] = [
                            'phone'   => $phone,
                            'user_id' => $userId,
                            'student_name' => $student->user->full_name,
                            'balance' => $balance,
                            'class'   => $student->currentEnrollment?->classArm?->full_name ?? 'N/A',
                        ];
                    }
                }
                break;

            case 'class_arm':
                $students = Student::with(['user', 'currentEnrollment.classArm.classLevel'])
                    ->where('status', 'Active')
                    ->whereHas('currentEnrollment', fn($q) => $q->where('class_arm_id', $validated['class_arm_id'])->where('is_active', true))
                    ->get();

                foreach ($students as $student) {
                    $balance = StudentFeeLedger::where('student_id', $student->id)
                        ->where('term_id', $term->id)
                        ->sum(DB::raw('net_amount - amount_paid'));

                    $phone = $validated['use_parent_phone']
                        ? ($student->primaryParent()?->phone ?? $student->user->phone)
                        : $student->user->phone;

                    $userId = $validated['use_parent_phone']
                        ? ($student->primaryParent()?->id ?? $student->user_id)
                        : $student->user_id;

                    if ($phone) {
                        $recipients[] = [
                            'phone'   => $phone,
                            'user_id' => $userId,
                            'student_name' => $student->user->full_name,
                            'balance' => $balance,
                            'class'   => $student->currentEnrollment?->classArm?->full_name ?? 'N/A',
                        ];
                    }
                }
                break;

            case 'specific_students':
                $students = Student::with(['user', 'currentEnrollment.classArm.classLevel'])
                    ->whereIn('id', $validated['student_ids'])
                    ->get();

                foreach ($students as $student) {
                    $balance = StudentFeeLedger::where('student_id', $student->id)
                        ->where('term_id', $term->id)
                        ->sum(DB::raw('net_amount - amount_paid'));

                    $phone = $validated['use_parent_phone']
                        ? ($student->primaryParent()?->phone ?? $student->user->phone)
                        : $student->user->phone;

                    $userId = $validated['use_parent_phone']
                        ? ($student->primaryParent()?->id ?? $student->user_id)
                        : $student->user_id;

                    if ($phone) {
                        $recipients[] = [
                            'phone'   => $phone,
                            'user_id' => $userId,
                            'student_name' => $student->user->full_name,
                            'balance' => $balance,
                            'class'   => $student->currentEnrollment?->classArm?->full_name ?? 'N/A',
                        ];
                    }
                }
                break;
        }

        if (empty($recipients)) {
            Alert::error('Error', 'No valid recipients found with phone numbers.');
            return back()->withInput();
        }

        // Personalize message for each recipient
        $personalized = [];
        foreach ($recipients as $r) {
            $msg = str_replace(
                ['{{student_name}}', '{{balance}}', '{{class}}', '{{term}}', '{{school}}'],
                [$r['student_name'], number_format($r['balance']), $r['class'], $term->name, $schoolName],
                $validated['message']
            );
            $personalized[] = [
                'phone'   => $r['phone'],
                'user_id' => $r['user_id'],
                'message' => $msg,
            ];
        }

        // Dispatch job
        SendBulkSmsJob::dispatch($personalized, $validated['message'], auth()->id());

        Alert::success('SMS Queued', number_format(count($personalized)) . ' SMS messages have been queued for delivery.');
        return redirect()->route('admin.sms.compose');
    }

    /* ── SMS Logs ───────────────────────────────────────── */
    public function logs()
    {
        $logs = SmsLog::with(['sentBy', 'recipient'])
            ->latest('sent_at')
            ->paginate(30);

        return view('admin.sms.logs', compact('logs'));
    }
}
