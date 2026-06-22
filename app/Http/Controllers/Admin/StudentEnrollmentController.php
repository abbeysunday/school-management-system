<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GeneratesFeeLedger;
use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassArm;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeLedger;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class StudentEnrollmentController extends Controller
{
    use GeneratesFeeLedger;

    public function index(Request $request)
    {
        try {
            $session = AcademicSession::getCurrent();
            $term    = Term::getCurrent();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            Alert::error('Error', 'No active session or term found.');
            return redirect()->route('admin.dashboard');
        }

        $classArms     = ClassArm::with('classLevel')->get()->sortBy('full_name');
        $selectedArmId = $request->integer('class_arm_id') ?: null;
        $selectedArm   = $selectedArmId ? ClassArm::with('classLevel')->find($selectedArmId) : null;

        $enrolledStudents = collect();
        if ($selectedArm) {
            $enrolledStudents = Student::with(['user', 'enrollments'])
                ->whereHas('enrollments', fn ($q) => $q->where('class_arm_id', $selectedArmId)
                    ->where('session_id', $session->id)
                    ->where('is_active', true))
                ->get()
                ->sortBy('full_name');
        }

        $search = $request->input('search');
        $unenrolledQuery = Student::with('user')
            ->where('status', 'Active')
            ->whereDoesntHave('enrollments', fn ($q) => $q->where('session_id', $session->id)->where('is_active', true));

        if ($search) {
            $unenrolledQuery->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) => $uq->whereRaw("CONCAT(first_name,' ',last_name) like ?", ["%{$search}%"]));
            });
        }

        $unenrolledStudents = $unenrolledQuery->get()->sortBy('full_name');

        $armEnrollmentCounts = StudentEnrollment::where('session_id', $session->id)
            ->where('is_active', true)
            ->selectRaw('class_arm_id, COUNT(*) as total')
            ->groupBy('class_arm_id')
            ->pluck('total', 'class_arm_id');

        return view('admin.students.enrollment', compact(
            'session', 'term', 'classArms', 'selectedArm', 'enrolledStudents',
            'unenrolledStudents', 'armEnrollmentCounts', 'search'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_arm_id'  => 'required|exists:class_arms,id',
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'integer|exists:students,id',
        ]);

        try {
            $session = AcademicSession::getCurrent();
            $term    = Term::getCurrent();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            Alert::error('Error', 'No active session/term.');
            return back();
        }

        $classArm = ClassArm::with('classLevel')->findOrFail($request->class_arm_id);
        $enrolled = 0;
        $skipped  = 0;

        DB::transaction(function () use ($request, $session, $term, $classArm, &$enrolled, &$skipped) {
            foreach ($request->student_ids as $studentId) {
                $alreadyActive = StudentEnrollment::where('student_id', $studentId)
                    ->where('session_id', $session->id)
                    ->where('is_active', true)
                    ->exists();

                if ($alreadyActive) {
                    $skipped++;
                    continue;
                }

                StudentEnrollment::where('student_id', $studentId)
                    ->where('session_id', $session->id)
                    ->delete();

                StudentEnrollment::create([
                    'student_id'      => $studentId,
                    'class_arm_id'    => $classArm->id,
                    'session_id'      => $session->id,
                    'enrollment_date' => now(),
                    'is_active'       => true,
                ]);

                $this->generateFeeLedger($studentId, $classArm, $term);
                $enrolled++;
            }
        });

        $msg = "{$enrolled} student(s) enrolled into {$classArm->full_name}.";
        if ($skipped) $msg .= " {$skipped} already enrolled (skipped).";

        Alert::success('Success', $msg);
        return redirect()->route('admin.students.enrollment', ['class_arm_id' => $classArm->id]);
    }

    public function transfer(Request $request, Student $student)
    {
        $request->validate(['new_class_arm_id' => 'required|exists:class_arms,id']);

        try {
            $session = AcademicSession::getCurrent();
            $term    = Term::getCurrent();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            Alert::error('Error', 'No active session/term.');
            return back();
        }

        $newArm = ClassArm::with('classLevel')->findOrFail($request->new_class_arm_id);

        DB::transaction(function () use ($student, $newArm, $session, $term) {
            $existing = StudentEnrollment::where('student_id', $student->id)
                ->where('session_id', $session->id)
                ->first();

            if ($existing) {
                $oldLevelId = ClassArm::find($existing->class_arm_id)?->class_level_id;
                $existing->update(['class_arm_id' => $newArm->id, 'is_active' => true]);

                if ($oldLevelId !== $newArm->class_level_id) {
                    StudentFeeLedger::where('student_id', $student->id)->where('term_id', $term->id)->delete();
                    $this->generateFeeLedger($student->id, $newArm, $term);
                }
            } else {
                StudentEnrollment::create([
                    'student_id'      => $student->id,
                    'class_arm_id'    => $newArm->id,
                    'session_id'      => $session->id,
                    'enrollment_date' => now(),
                    'is_active'       => true,
                ]);
                $this->generateFeeLedger($student->id, $newArm, $term);
            }
        });

        Alert::success('Success', $student->full_name . ' transferred to ' . $newArm->full_name . '.');
        return redirect()->route('admin.students.enrollment', ['class_arm_id' => $newArm->id]);
    }

    public function destroy(StudentEnrollment $enrollment)
    {
        $enrollment->load(['student.user', 'classArm']);
        $name = $enrollment->student->full_name;
        $arm  = $enrollment->classArm->full_name;

        $enrollment->delete();
        Alert::success('Success', "{$name} unenrolled from {$arm}.");
        return redirect()->back();
    }
}
