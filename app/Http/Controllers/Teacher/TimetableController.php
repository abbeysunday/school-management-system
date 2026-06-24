<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Timetable;
use App\Models\TimetablePeriod;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TimetableController extends Controller
{
   /* ── Teacher Personal Timetable ───────────────────── */
    public function index()
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            Alert::error('Error', 'You are not registered as a teacher.');
            return redirect()->route('teacher.dashboard');
        }

        $session = AcademicSession::getCurrent();
        $periods = TimetablePeriod::orderBy('period_order')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        // Get raw entries BEFORE grouping (keep this for stats)
        $rawEntries = Timetable::with(['subject', 'classArm.classLevel', 'period'])
            ->where('teacher_id', $teacher->id)
            ->where('session_id', $session->id)
            ->get();

        // Group for grid display
        $grouped = $rawEntries->groupBy(
            fn($item) => $item->day_of_week . '|' . $item->period_id
        );

        $timetableGrid = [];
        foreach ($days as $day) {
            foreach ($periods as $period) {
                $key = $day . '|' . $period->id;
                $timetableGrid[$day][$period->id] = $grouped->get($key)?->first();
            }
        }

        // Use $rawEntries for stats (not the grouped collection)
        $totalPeriods = $rawEntries->count();
        $teachingPeriods = $rawEntries->filter(fn($e) => $e->period?->isTeaching())->count();
        $classArms = $rawEntries->pluck('classArm.full_name')->unique()->values();

        return view('teacher.timetable.index', compact(
            'periods', 'days', 'timetableGrid', 'totalPeriods', 'teachingPeriods', 'classArms'
        ));
    }
}
