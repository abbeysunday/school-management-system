<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Jobs\AbsenceAlertJob;
use App\Models\AttendanceRecord;
use App\Models\ClassArm;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            Alert::error('Error', 'You are not registered as a teacher.');
            return redirect()->route('teacher.dashboard');
        }

        $term = Term::getCurrent();
        if (!$term) {
            Alert::error('Error', 'No active term found.');
            return redirect()->route('teacher.dashboard');
        }

        $classArmIds = $teacher->classArmTeachers()
            ->where('session_id', $term->session_id)
            ->where('role', 'Form Teacher')
            ->pluck('class_arm_id');

        if ($classArmIds->isEmpty()) {
            Alert::error('Error', 'You are not assigned as Form Teacher for any class arm.');
            return redirect()->route('teacher.dashboard');
        }

        $selectedArmId = $request->query('class_arm_id', $classArmIds->first());
        if (!$classArmIds->contains($selectedArmId)) {
            $selectedArmId = $classArmIds->first();
        }

        $classArm = ClassArm::with('classLevel')->find($selectedArmId);
        $date = $request->query('date', now()->format('Y-m-d'));

        $students = Student::with(['user'])
            ->whereHas('currentEnrollment', function ($q) use ($selectedArmId) {
                $q->where('class_arm_id', $selectedArmId)->where('is_active', true);
            })
            ->where('status', 'Active')
            ->orderBy('admission_number')
            ->get();

        $existingRecords = AttendanceRecord::where('class_arm_id', $selectedArmId)
            ->where('attendance_date', $date)
            ->where('term_id', $term->id)
            ->get()
            ->keyBy('student_id');

        $isEditMode = $existingRecords->isNotEmpty();

        $attendanceData = [];
        foreach ($students as $student) {
            $record = $existingRecords->get($student->id);
            $attendanceData[] = [
                'student_id' => $student->id,
                'name' => $student->user->full_name,
                'admission_no' => $student->admission_number,
                'photo' => $student->user->photo_url,
                'status' => $record?->status ?? 'Present',
                'remarks' => $record?->remarks ?? '',
                'marked_at' => $record?->created_at?->format('h:i A') ?? null,
            ];
        }

        $dateStats = [
            'present' => AttendanceRecord::where('class_arm_id', $selectedArmId)->where('attendance_date', $date)->whereIn('status', ['Present', 'Late'])->count(),
            'absent' => AttendanceRecord::where('class_arm_id', $selectedArmId)->where('attendance_date', $date)->where('status', 'Absent')->count(),
            'late' => AttendanceRecord::where('class_arm_id', $selectedArmId)->where('attendance_date', $date)->where('status', 'Late')->count(),
            'sick' => AttendanceRecord::where('class_arm_id', $selectedArmId)->where('attendance_date', $date)->where('status', 'Sick')->count(),
            'excused' => AttendanceRecord::where('class_arm_id', $selectedArmId)->where('attendance_date', $date)->where('status', 'Excused')->count(),
            'total' => $students->count(),
        ];

        $classArms = ClassArm::with('classLevel')->whereIn('id', $classArmIds)->get();

        return view('teacher.attendance.index', compact(
            'classArm', 'classArms', 'date', 'attendanceData', 'isEditMode', 'dateStats', 'term'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_arm_id' => 'required|exists:class_arms,id',
            'attendance_date' => 'required|date',
            'statuses' => 'required|array',
            'statuses.*' => 'in:Present,Absent,Late,Sick,Excused',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string|max:255',
        ]);

        $teacher = auth()->user()->teacher;
        $term = Term::getCurrent();
        if (!$term) {
            Alert::error('Error', 'No active term found.');
            return back();
        }

        $isFormTeacher = $teacher->classArmTeachers()
            ->where('class_arm_id', $validated['class_arm_id'])
            ->where('session_id', $term->session_id)
            ->where('role', 'Form Teacher')
            ->exists();

        if (!$isFormTeacher) {
            Alert::error('Unauthorized', 'You are not the Form Teacher of this class arm.');
            return back();
        }

        $absentStudentIds = [];

        try {
            DB::beginTransaction();

            foreach ($validated['statuses'] as $studentId => $status) {
                $record = AttendanceRecord::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'attendance_date' => $validated['attendance_date'],
                    ],
                    [
                        'class_arm_id' => $validated['class_arm_id'],
                        'term_id' => $term->id,
                        'status' => $status,
                        'remarks' => $validated['remarks'][$studentId] ?? null,
                        'marked_by' => auth()->id(),
                    ]
                );

                if ($status === 'Absent') {
                    $absentStudentIds[] = $studentId;
                }
            }

            DB::commit();

            if (!empty($absentStudentIds)) {
                $school = SchoolProfile::first();
                if ($school?->sms_on_absence) {
                    AbsenceAlertJob::dispatch($absentStudentIds, $validated['attendance_date'], $term->id, auth()->id());
                }
            }

            Alert::success('Attendance Saved', 'Attendance has been recorded successfully.' . (count($absentStudentIds) > 0 ? ' ' . count($absentStudentIds) . ' absence alert(s) queued.' : ''));
            return redirect()->route('teacher.attendance.index', ['class_arm_id' => $validated['class_arm_id'], 'date' => $validated['attendance_date']]);

        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'Failed to save attendance: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function report(Request $request)
    {
        $teacher = auth()->user()->teacher;
        $term = Term::getCurrent();
        if (!$term) {
            Alert::error('Error', 'No active term found.');
            return redirect()->route('teacher.dashboard');
        }

        $classArmIds = $teacher->classArmTeachers()
            ->where('session_id', $term->session_id)
            ->where('role', 'Form Teacher')
            ->pluck('class_arm_id');

        $selectedArmId = $request->query('class_arm_id', $classArmIds->first());
        $month = $request->query('month', now()->format('Y-m'));

        $classArm = ClassArm::with('classLevel')->find($selectedArmId);
        $classArms = ClassArm::with('classLevel')->whereIn('id', $classArmIds)->get();

        $startOfMonth = \Carbon\Carbon::parse($month . '-01');
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $schoolDays = [];
        for ($d = $startOfMonth->copy(); $d->lte($endOfMonth); $d->addDay()) {
            if (!$d->isWeekend()) {
                $schoolDays[] = $d->format('Y-m-d');
            }
        }

        $students = Student::with(['user'])
            ->whereHas('currentEnrollment', fn($q) => $q->where('class_arm_id', $selectedArmId)->where('is_active', true))
            ->where('status', 'Active')
            ->orderBy('admission_number')
            ->get();

        $records = AttendanceRecord::where('class_arm_id', $selectedArmId)
            ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->where('term_id', $term->id)
            ->get()
            ->groupBy('student_id');

        $reportData = [];
        foreach ($students as $student) {
            $studentRecords = $records->get($student->id, collect())->keyBy('attendance_date');
            $dayStatuses = [];
            $stats = ['Present' => 0, 'Absent' => 0, 'Late' => 0, 'Sick' => 0, 'Excused' => 0];

            foreach ($schoolDays as $day) {
                $status = $studentRecords[$day]?->status ?? null;
                $dayStatuses[$day] = $status;
                if ($status) $stats[$status]++;
            }

            $reportData[] = [
                'student_id' => $student->id,
                'name' => $student->user->full_name,
                'admission_no' => $student->admission_number,
                'days' => $dayStatuses,
                'stats' => $stats,
                'total_present' => $stats['Present'] + $stats['Late'],
                'total_absent' => $stats['Absent'],
                'attendance_rate' => count($schoolDays) > 0 ? round((($stats['Present'] + $stats['Late']) / count($schoolDays)) * 100, 1) : 0,
            ];
        }

        return view('teacher.attendance.report', compact(
            'classArm', 'classArms', 'month', 'schoolDays', 'students', 'reportData', 'term'
        ));
    }
}
