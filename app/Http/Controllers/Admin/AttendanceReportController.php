<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceRecordsExport;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ClassArm;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Term;
use App\Models\TermSummary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceReportController extends Controller
{
    /* ── Attendance Records Report ──────────────────────── */
    public function report(Request $request)
    {
        $term = Term::getCurrent();
        $query = AttendanceRecord::with(['student.user', 'classArm.classLevel', 'term', 'markedBy'])
            ->latest('attendance_date');

        if ($request->filled('class_arm_id')) {
            $query->where('class_arm_id', $request->class_arm_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user', fn($q) => $q->whereRaw("CONCAT(first_name,' ',last_name) like ?", ["%{$search}%"]));
        }

        // Sorting
        $sortBy = $request->query('sort_by', 'attendance_date');
        $sortOrder = $request->query('sort_order', 'desc');
        $allowedSorts = ['attendance_date', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $records = $query->paginate(40)->withQueryString();
        $classArms = ClassArm::with('classLevel')->orderBy('arm')->get();

        // Summary stats for the filtered range
        $statsQuery = AttendanceRecord::query();
        if ($request->filled('class_arm_id')) $statsQuery->where('class_arm_id', $request->class_arm_id);
        if ($request->filled('date_from')) $statsQuery->whereDate('attendance_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $statsQuery->whereDate('attendance_date', '<=', $request->date_to);

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'present' => (clone $statsQuery)->whereIn('status', ['Present', 'Late'])->count(),
            'absent' => (clone $statsQuery)->where('status', 'Absent')->count(),
            'late' => (clone $statsQuery)->where('status', 'Late')->count(),
            'sick' => (clone $statsQuery)->where('status', 'Sick')->count(),
            'excused' => (clone $statsQuery)->where('status', 'Excused')->count(),
        ];

        return view('admin.attendance.report', compact('records', 'classArms', 'stats', 'term'));
    }

    /* ── Export Attendance Records to Excel ─────────────── */
    public function exportReport(Request $request)
    {
        $term = Term::getCurrent();
        $filename = 'attendance_report_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new AttendanceRecordsExport(
            classArmId: $request->query('class_arm_id'),
            dateFrom: $request->query('date_from'),
            dateTo: $request->query('date_to'),
            status: $request->query('status'),
        ), $filename);
    }

    /* ── Student Attendance Summary ─────────────────────── */
    public function studentSummary(Request $request)
    {
        $term = Term::getCurrent();
        if (!$term) {
            Alert::error('Error', 'No active term found.');
            return redirect()->route('admin.dashboard');
        }

        $classArmId = $request->query('class_arm_id');

        // Get all school days in the term (Mon-Fri within term date range)
        $schoolDays = $this->getSchoolDays($term->start_date, $term->end_date);
        $totalSchoolDays = count($schoolDays);

        // Get students
        $query = Student::with(['user', 'currentEnrollment.classArm.classLevel'])
            ->where('status', 'Active')
            ->whereHas('currentEnrollment', fn($q) => $q->where('is_active', true));

        if ($classArmId) {
            $query->whereHas('currentEnrollment', fn($q) => $q->where('class_arm_id', $classArmId));
        }

        $students = $query->orderBy('admission_number')->get();
        $classArms = ClassArm::with('classLevel')->orderBy('arm')->get();

        // Calculate per-student attendance stats
        $summaries = [];
        foreach ($students as $student) {
            $records = AttendanceRecord::where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->whereIn('attendance_date', $schoolDays)
                ->get();

            $present = $records->where('status', 'Present')->count();
            $absent = $records->where('status', 'Absent')->count();
            $late = $records->where('status', 'Late')->count();
            $sick = $records->where('status', 'Sick')->count();
            $excused = $records->where('status', 'Excused')->count();
            $marked = $records->count();

            $summaries[] = [
                'student_id' => $student->id,
                'name' => $student->user->full_name,
                'admission_no' => $student->admission_number,
                'class' => $student->currentEnrollment?->classArm?->full_name ?? 'N/A',
                'days_present' => $present,
                'days_absent' => $absent,
                'days_late' => $late,
                'days_sick' => $sick,
                'days_excused' => $excused,
                'total_marked' => $marked,
                'total_school_days' => $totalSchoolDays,
                'percentage' => $totalSchoolDays > 0 ? round((($present + $late) / $totalSchoolDays) * 100, 1) : 0,
            ];
        }

        return view('admin.attendance.student-summary', compact(
            'summaries', 'classArms', 'term', 'totalSchoolDays', 'classArmId'
        ));
    }

    /* ── Calculate & Store Term Attendance Summaries ────── */
    public function calculateTermSummaries(Request $request)
    {
        $term = Term::getCurrent();
        if (!$term) {
            Alert::error('Error', 'No active term found.');
            return back();
        }

        $schoolDays = $this->getSchoolDays($term->start_date, $term->end_date);
        $totalSchoolDays = count($schoolDays);

        $students = Student::where('status', 'Active')
            ->whereHas('currentEnrollment', fn($q) => $q->where('is_active', true))
            ->get();

        $updated = 0;
        foreach ($students as $student) {
            $records = AttendanceRecord::where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->whereIn('attendance_date', $schoolDays)
                ->get();

            $present = $records->where('status', 'Present')->count();
            $absent = $records->where('status', 'Absent')->count();
            $late = $records->where('status', 'Late')->count();
            $sick = $records->where('status', 'Sick')->count();
            $excused = $records->where('status', 'Excused')->count();

            $percentage = $totalSchoolDays > 0 ? round((($present + $late) / $totalSchoolDays) * 100, 1) : 0;

            // Update or create term_summary
            TermSummary::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'term_id' => $term->id,
                ],
                [
                    'session_id' => $term->session_id,
                    'class_arm_id' => $student->currentEnrollment?->class_arm_id,
                    'days_present' => $present,
                    'days_absent' => $absent,
                    'days_late' => $late,
                    'days_sick' => $sick,
                    'days_excused' => $excused,
                    'total_school_days' => $totalSchoolDays,
                    'attendance_percentage' => $percentage,
                ]
            );

            $updated++;
        }

        Alert::success('Summaries Calculated', "Attendance summaries updated for {$updated} students in {$term->name}.");
        return redirect()->route('admin.attendance.student-summary');
    }

    /* ── Class Register PDF ─────────────────────────────── */
    public function classRegister(Request $request)
    {
        $validated = $request->validate([
            'arm_id' => 'required|exists:class_arms,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $term = Term::with('session')->find($validated['term_id']);
        $classArm = ClassArm::with('classLevel')->find($validated['arm_id']);
        $school = SchoolProfile::first();

        // Get school days in the term
        $schoolDays = $this->getSchoolDays($term->start_date, $term->end_date);

        // Get students
        $students = Student::with(['user'])
            ->whereHas('currentEnrollment', fn($q) => $q->where('class_arm_id', $validated['arm_id'])->where('is_active', true))
            ->where('status', 'Active')
            ->orderBy('admission_number')
            ->get();

        // Get attendance records
        $records = AttendanceRecord::where('class_arm_id', $validated['arm_id'])
            ->where('term_id', $validated['term_id'])
            ->whereIn('attendance_date', $schoolDays)
            ->get()
            ->groupBy('student_id');

        // Build register matrix
        $registerData = [];
        foreach ($students as $student) {
            $studentRecords = $records->get($student->id, collect())->keyBy('attendance_date');
            $dayStatuses = [];
            foreach ($schoolDays as $day) {
                $dayStatuses[$day] = $studentRecords[$day]?->status ?? null;
            }
            $registerData[] = [
                'name' => $student->user->full_name,
                'admission_no' => $student->admission_number,
                'days' => $dayStatuses,
            ];
        }

        // Chunk days for pagination (max 20 days per page to fit A4 landscape)
        $dayChunks = array_chunk($schoolDays, 20);

        $pdf = Pdf::loadView('pdf.class-register', compact(
            'school', 'term', 'classArm', 'registerData', 'schoolDays', 'dayChunks'
        ))->setPaper('a4', 'landscape');

        $safeArm = str_replace(['/', '\\'], '_', $classArm->full_name);
        $safeTerm = str_replace(['/', '\\'], '_', $term->name);
        return $pdf->stream("register-{$safeArm}-{$safeTerm}.pdf");
    }

    /* ── Helper: Get School Days ────────────────────────── */
    private function getSchoolDays(?string $startDate, ?string $endDate): array
    {
        if (!$startDate || !$endDate) return [];

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $days = [];

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if (!$d->isWeekend()) {
                $days[] = $d->format('Y-m-d');
            }
        }

        return $days;
    }
}
