<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Timetable;
use App\Models\TimetablePeriod;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TimetableController extends Controller
{
    /**
     * Show the student's class timetable — weekly grid view.
     */
    public function myTimetable()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            Alert::error('Error', 'You are not registered as a student.');
            return redirect()->route('student.dashboard');
        }

        $enrollment = $student->currentEnrollment;
        if (!$enrollment) {
            Alert::error('Error', 'You are not enrolled in any class for the current session.');
            return redirect()->route('student.dashboard');
        }

        $session = AcademicSession::getCurrent();
        $periods = TimetablePeriod::orderBy('period_order')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        $entries = Timetable::with(['subject', 'teacher.user', 'period'])
            ->where('class_arm_id', $enrollment->class_arm_id)
            ->where('session_id', $session->id)
            ->get();

        $grouped = $entries->groupBy(
            fn($item) => $item->day_of_week . '|' . $item->period_id
        );

        $timetableGrid = [];
        foreach ($days as $day) {
            foreach ($periods as $period) {
                $key = $day . '|' . $period->id;
                $timetableGrid[$day][$period->id] = $grouped->get($key)?->first();
            }
        }

        // Subject colors
        $subjectColors = [];
        $allSubjects = $entries->pluck('subject.name')->unique()->filter()->values();
        $palette = [
            '#16a34a', '#2563eb', '#7c3aed', '#d97706', '#ea580c',
            '#0891b2', '#db2777', '#64748b', '#65a30d', '#e11d48',
            '#0d9488', '#9333ea', '#be123c', '#4338ca', '#b45309',
            '#059669', '#4f46e5', '#c026d3', '#dc2626', '#0284c7',
        ];
        foreach ($allSubjects as $i => $subjectName) {
            $subjectColors[$subjectName] = $palette[$i % count($palette)];
        }

        // Current period
        $now = now();
        $nowMinutes = $now->hour * 60 + $now->minute;
        $nowPeriodId = null;
        foreach ($periods as $period) {
            if (!$period->isTeaching()) continue;
            [$startH, $startM] = explode(':', $period->start_time);
            [$endH, $endM] = explode(':', $period->end_time);
            $startMin = (int) $startH * 60 + (int) $startM;
            $endMin   = (int) $endH * 60 + (int) $endM;
            if ($nowMinutes >= $startMin && $nowMinutes < $endMin) {
                $nowPeriodId = $period->id;
                break;
            }
        }

        $todayNum = (int) $now->format('N');
        $isWeekday = in_array($todayNum, [1, 2, 3, 4, 5]);
        $activeDayNum = $isWeekday ? $todayNum : 1;

        $weekDays = [];
        foreach ([1, 2, 3, 4, 5] as $num) {
            $dayName = $days[$num - 1];
            $weekDays[$num] = [
                'short' => substr($dayName, 0, 3),
                'full'  => $dayName,
                'date'  => $now->copy()->startOfWeek()->addDays($num - 1)->format('j'),
            ];
        }

        $currentTerm = $enrollment->term?->name ?? 'Current Term';
        $classArmName = $enrollment->classArm?->full_name ?? 'Your Class';

        $totalPeriods = $periods->count();
        $teachingPeriods = $periods->filter(fn($p) => $p->isTeaching())->count();
        $scheduledPeriods = $entries->count();
        $subjectsCount = $allSubjects->count();

        return view('student.timetable.index', compact(
            'periods', 'days', 'timetableGrid', 'enrollment', 'session',
            'subjectColors', 'nowPeriodId', 'todayNum', 'activeDayNum',
            'isWeekday', 'weekDays', 'currentTerm', 'classArmName',
            'totalPeriods', 'teachingPeriods', 'scheduledPeriods', 'subjectsCount'
        ));
    }

    /**
     * Printable timetable view — clean layout, no nav/sidebar.
     */
    public function print()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Not a student.');
        }

        $enrollment = $student->currentEnrollment;
        if (!$enrollment) {
            abort(404, 'No enrollment found.');
        }

        $session = AcademicSession::getCurrent();
        $periods = TimetablePeriod::orderBy('period_order')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $school = SchoolProfile::first();

        $entries = Timetable::with(['subject', 'teacher.user', 'period'])
            ->where('class_arm_id', $enrollment->class_arm_id)
            ->where('session_id', $session->id)
            ->get();

        $grouped = $entries->groupBy(
            fn($item) => $item->day_of_week . '|' . $item->period_id
        );

        $timetableGrid = [];
        foreach ($days as $day) {
            foreach ($periods as $period) {
                $key = $day . '|' . $period->id;
                $timetableGrid[$day][$period->id] = $grouped->get($key)?->first();
            }
        }

        $subjectColors = [];
        $allSubjects = $entries->pluck('subject.name')->unique()->filter()->values();
        $palette = [
            '#16a34a', '#2563eb', '#7c3aed', '#d97706', '#ea580c',
            '#0891b2', '#db2777', '#64748b', '#65a30d', '#e11d48',
            '#0d9488', '#9333ea', '#be123c', '#4338ca', '#b45309',
            '#059669', '#4f46e5', '#c026d3', '#dc2626', '#0284c7',
        ];
        foreach ($allSubjects as $i => $subjectName) {
            $subjectColors[$subjectName] = $palette[$i % count($palette)];
        }

        $classArmName = $enrollment->classArm?->full_name ?? 'Your Class';
        $currentTerm = $enrollment->term?->name ?? 'Current Term';

        return view('student.timetable.print', compact(
            'periods', 'days', 'timetableGrid', 'session', 'school',
            'subjectColors', 'classArmName', 'currentTerm', 'student'
        ));
    }
}
