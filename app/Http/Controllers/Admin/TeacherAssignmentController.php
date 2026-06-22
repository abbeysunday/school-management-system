<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\ClassArm;
use App\Models\ArmSubject;
use App\Models\ClassArmTeacher;
use App\Models\TeacherArmSubject;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherAssignmentController extends Controller
{
    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        $session = AcademicSession::getCurrent();

        // 1. Get all class arms for the Form Teacher dropdown
        $classArms = ClassArm::with('classLevel')->get();

        // Find current form teacher assignment for this session
        $currentFormClass = ClassArmTeacher::where('teacher_id', $teacher->id)
                                           ->where('session_id', $session->id)
                                           ->where('role', 'Form Teacher')
                                           ->first();

        // 2. Get all Arm Subjects mapped to this session, grouped by Class Arm
        // This builds the massive checkbox grid
        $armSubjects = ArmSubject::with(['subject', 'classArm.classLevel'])
                                 ->where('session_id', $session->id)
                                 ->get()
                                 ->groupBy('class_arm_id');

        // Extract the IDs of arm_subjects the teacher is CURRENTLY assigned to
        $currentAssignedSubjectIds = TeacherArmSubject::where('teacher_id', $teacher->id)
                                                      ->pluck('arm_subject_id')
                                                      ->toArray();

        return view('admin.teachers.assignments', compact(
            'teacher', 'session', 'classArms', 'currentFormClass', 'armSubjects', 'currentAssignedSubjectIds'
        ));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'form_class_arm_id' => ['nullable', 'exists:class_arms,id'],
            'arm_subjects'      => ['nullable', 'array'],
            'arm_subjects.*'    => ['exists:arm_subjects,id'],
        ]);

        $session = AcademicSession::getCurrent();

        try {
            DB::beginTransaction();

            // --- 1. HANDLE FORM TEACHER ASSIGNMENT ---
            // Wipe existing form teacher roles for this session for this specific teacher
            ClassArmTeacher::where('teacher_id', $teacher->id)
                           ->where('session_id', $session->id)
                           ->where('role', 'Form Teacher')
                           ->delete();

            if ($request->filled('form_class_arm_id')) {
                // Check if someone else is already form teacher for this arm to prevent duplicates
                ClassArmTeacher::where('class_arm_id', $request->form_class_arm_id)
                               ->where('session_id', $session->id)
                               ->where('role', 'Form Teacher')
                               ->delete();

                ClassArmTeacher::create([
                    'class_arm_id' => $request->form_class_arm_id,
                    'teacher_id'   => $teacher->id,
                    'session_id'   => $session->id,
                    'role'         => 'Form Teacher',
                ]);
            }

            // --- 2. HANDLE SUBJECT ASSIGNMENTS ---
            // Find all arm_subjects for the CURRENT session
            $currentSessionArmSubjectIds = ArmSubject::where('session_id', $session->id)->pluck('id');

            // Delete ONLY the teacher's subject assignments that belong to the current session
            // (preserves history for past sessions)
            TeacherArmSubject::where('teacher_id', $teacher->id)
                             ->whereIn('arm_subject_id', $currentSessionArmSubjectIds)
                             ->delete();

            $subjectCount = 0;
            if ($request->has('arm_subjects')) {
                $inserts = [];
                foreach ($request->arm_subjects as $armSubjectId) {
                    $inserts[] = [
                        'teacher_id'     => $teacher->id,
                        'arm_subject_id' => $armSubjectId,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                    $subjectCount++;
                }
                TeacherArmSubject::insert($inserts);
            }

            DB::commit();

            // Feature 8: Soft warning if workload is unreasonably high
            if ($subjectCount > 15) {
                return redirect()->route('admin.teachers.index')
                    ->with('success', 'Assignments updated.')
                    ->with('warning', "Notice: This teacher is assigned to {$subjectCount} subjects. Please ensure this does not exceed a reasonable workload.");
            }

            return redirect()->route('admin.teachers.index')->with('success', 'Teacher assignments updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update assignments: ' . $e->getMessage());
        }
    }
}
