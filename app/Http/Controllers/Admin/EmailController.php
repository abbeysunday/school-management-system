<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBulkEmailJob;
use App\Mail\FeeReminderMail;
use App\Models\ClassArm;
use App\Models\Student;
use App\Models\StudentFeeLedger;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;

class EmailController extends Controller
{
    public function compose(Request $request)
    {
        $term = Term::getCurrent();
        $classArms = ClassArm::with('classLevel')->orderBy('arm')->get();
        $preselected = [];

        if ($request->query('target') === 'defaulters') {
            $students = Student::with('user')->where('status', 'Active')->whereHas('feeLedger', function ($q) use ($term) {
                $q->where('term_id', $term->id)->whereRaw('net_amount - amount_paid > 0');
            })->get();

            foreach ($students as $student) {
                $balance = StudentFeeLedger::where('student_id', $student->id)->where('term_id', $term->id)->sum(DB::raw('net_amount - amount_paid'));
                if ($balance > 0) {
                    $preselected[] = ['id' => $student->id, 'name' => $student->user->full_name, 'email' => $student->user->email, 'parent_email' => $student->primaryParent()?->email, 'class' => $student->currentEnrollment?->classArm?->full_name ?? 'N/A', 'balance' => $balance];
                }
            }
        }

        $templates = [
            'fee_reminder' => ['subject' => 'Fee Payment Reminder — {{term}}', 'body' => "<p>Dear Parent/Guardian,</p><p>This is a friendly reminder that <strong>{{student_name}}</strong> ({{class}}) has an outstanding fee balance of <strong>₦{{balance}}</strong> for <strong>{{term}}</strong>.</p><p>Please log in to the parent portal to make payment or visit the bursary office.</p><p>Thank you,<br>{{school}}</p>"],
            'payment_due' => ['subject' => 'Payment Due — {{term}} Fees', 'body' => "<p>Dear Parent/Guardian,</p><p>{{term}} fees for <strong>{{student_name}}</strong> are now due. Outstanding balance: <strong>₦{{balance}}</strong>.</p><p>Kindly settle to avoid any penalties.</p><p>Thank you,<br>{{school}}</p>"],
            'general' => ['subject' => '', 'body' => ''],
        ];

        return view('admin.email.compose', compact('classArms', 'term', 'preselected', 'templates'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'target_type' => 'required|in:all_defaulters,class_arm,specific_students',
            'class_arm_id' => 'nullable|required_if:target_type,class_arm|exists:class_arms,id',
            'student_ids' => 'nullable|required_if:target_type,specific_students|array',
            'student_ids.*' => 'exists:students,id',
            'subject' => 'required|string|max:200',
            'body' => 'required|string',
            'use_parent_email' => 'nullable|boolean',
        ]);

        $term = Term::getCurrent();
        if (!$term) { Alert::error('Error', 'No active term found.'); return back(); }

        $recipients = [];
        $school = \App\Models\SchoolProfile::first();
        $schoolName = $school?->name ?? 'School';

        switch ($validated['target_type']) {
            case 'all_defaulters':
                $students = Student::with(['user', 'currentEnrollment.classArm.classLevel'])->where('status', 'Active')->whereHas('feeLedger', function ($q) use ($term) { $q->where('term_id', $term->id)->whereRaw('net_amount - amount_paid > 0'); })->get();
                foreach ($students as $student) {
                    $balance = StudentFeeLedger::where('student_id', $student->id)->where('term_id', $term->id)->sum(DB::raw('net_amount - amount_paid'));
                    if ($balance <= 0) continue;
                    $email = $validated['use_parent_email'] ? ($student->primaryParent()?->email ?? $student->user->email) : $student->user->email;
                    if ($email) $recipients[] = ['email' => $email, 'student_id' => $student->id, 'student_name' => $student->user->full_name, 'balance' => $balance, 'class' => $student->currentEnrollment?->classArm?->full_name ?? 'N/A'];
                }
                break;
            case 'class_arm':
                $students = Student::with(['user', 'currentEnrollment.classArm.classLevel'])->where('status', 'Active')->whereHas('currentEnrollment', fn($q) => $q->where('class_arm_id', $validated['class_arm_id'])->where('is_active', true))->get();
                foreach ($students as $student) {
                    $balance = StudentFeeLedger::where('student_id', $student->id)->where('term_id', $term->id)->sum(DB::raw('net_amount - amount_paid'));
                    $email = $validated['use_parent_email'] ? ($student->primaryParent()?->email ?? $student->user->email) : $student->user->email;
                    if ($email) $recipients[] = ['email' => $email, 'student_id' => $student->id, 'student_name' => $student->user->full_name, 'balance' => $balance, 'class' => $student->currentEnrollment?->classArm?->full_name ?? 'N/A'];
                }
                break;
            case 'specific_students':
                $students = Student::with(['user', 'currentEnrollment.classArm.classLevel'])->whereIn('id', $validated['student_ids'])->get();
                foreach ($students as $student) {
                    $balance = StudentFeeLedger::where('student_id', $student->id)->where('term_id', $term->id)->sum(DB::raw('net_amount - amount_paid'));
                    $email = $validated['use_parent_email'] ? ($student->primaryParent()?->email ?? $student->user->email) : $student->user->email;
                    if ($email) $recipients[] = ['email' => $email, 'student_id' => $student->id, 'student_name' => $student->user->full_name, 'balance' => $balance, 'class' => $student->currentEnrollment?->classArm?->full_name ?? 'N/A'];
                }
                break;
        }

        if (empty($recipients)) { Alert::error('Error', 'No valid recipients found with email addresses.'); return back()->withInput(); }

        $personalized = [];
        foreach ($recipients as $r) {
            $subject = str_replace(['{{student_name}}', '{{balance}}', '{{class}}', '{{term}}', '{{school}}'], [$r['student_name'], number_format($r['balance']), $r['class'], $term->name, $schoolName], $validated['subject']);
            $body = str_replace(['{{student_name}}', '{{balance}}', '{{class}}', '{{term}}', '{{school}}'], [$r['student_name'], number_format($r['balance']), $r['class'], $term->name, $schoolName], $validated['body']);
            $personalized[] = ['email' => $r['email'], 'student_id' => $r['student_id'], 'subject' => $subject, 'body' => $body];
        }

        SendBulkEmailJob::dispatch($personalized, auth()->id());
        Alert::success('Email Queued', number_format(count($personalized)) . ' emails have been queued for delivery.');
        return redirect()->route('admin.email.compose');
    }

    public function preview(Request $request)
    {
        $validated = $request->validate(['subject' => 'required|string', 'body' => 'required|string']);
        $school = \App\Models\SchoolProfile::first();
        $subject = str_replace(['{{student_name}}', '{{balance}}', '{{class}}', '{{term}}', '{{school}}'], ['John Doe', '50,000', 'JSS1A', 'First Term 2024/2025', $school?->name ?? 'School'], $validated['subject']);
        $body = str_replace(['{{student_name}}', '{{balance}}', '{{class}}', '{{term}}', '{{school}}'], ['John Doe', '50,000', 'JSS1A', 'First Term 2024/2025', $school?->name ?? 'School'], $validated['body']);
        return view('emails.fee-reminder', ['subject' => $subject, 'body' => $body, 'school' => $school]);
    }
}
