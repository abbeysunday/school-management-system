<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ArmSubject;
use App\Models\ClassArm;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ArmSubjectController extends Controller
{
    public function index(Request $request): View
    {
        $classArms = ClassArm::with('classLevel')->orderBy('arm')->get();
        $sessions  = AcademicSession::orderByDesc('start_year')->pluck('name', 'id');
        $subjects  = Subject::active()->orderBy('category')->orderBy('name')->get();

        $assignedSubjectIds = collect();

        if ($request->filled('class_arm_id') && $request->filled('session_id')) {
            $assignedSubjectIds = ArmSubject::where('class_arm_id', $request->class_arm_id)
                ->where('session_id', $request->session_id)
                ->pluck('subject_id');
        }

        return view('admin.subjects.assignments', compact('classArms', 'sessions', 'subjects', 'assignedSubjectIds'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_arm_id' => 'required|exists:class_arms,id',
            'session_id'   => 'required|exists:academic_sessions,id',
            'subject_ids'  => 'nullable|array',
            'subject_ids.*'=> 'exists:subjects,id',
        ]);

        $armId      = $validated['class_arm_id'];
        $sessionId  = $validated['session_id'];
        $subjectIds = $validated['subject_ids'] ?? [];

        DB::transaction(function () use ($armId, $sessionId, $subjectIds) {
            // Remove unchecked
            ArmSubject::where('class_arm_id', $armId)
                ->where('session_id', $sessionId)
                ->whereNotIn('subject_id', $subjectIds)
                ->delete();

            // Insert new (ignore duplicates via unique constraint)
            foreach ($subjectIds as $subjectId) {
                ArmSubject::firstOrCreate([
                    'class_arm_id' => $armId,
                    'subject_id'   => $subjectId,
                    'session_id'   => $sessionId,
                ]);
            }
        });

        alert()->success('Success', 'Subjects assigned successfully.');
        return redirect()->route('admin.subjects.assignments', [
            'class_arm_id' => $armId,
            'session_id'   => $sessionId,
        ]);
    }

    public function destroy(ArmSubject $armSubject): RedirectResponse
    {
        $armSubject->delete();
        alert()->success('Success', 'Subject removed from class arm.');
        return redirect()->route('admin.subjects.assignments');
    }
}
