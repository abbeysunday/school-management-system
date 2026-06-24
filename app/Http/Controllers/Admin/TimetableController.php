<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassArm;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\TimetablePeriod;
use App\Models\AcademicSession;
use App\Services\TimetableService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class TimetableController extends Controller
{
    public function __construct(
        private TimetableService $timetableService,
    ) {}

    /* ── Periods CRUD ───────────────────────────────────── */
    public function periods()
    {
        $periods = TimetablePeriod::orderBy('period_order')->get();
        return view('admin.settings.periods', compact('periods'));
    }

    public function storePeriod(Request $request)
    {
        $validated = $request->validate([
            'period_name' => 'required|string|max:30|unique:timetable_periods',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'period_order' => 'required|integer|min:1',
            'period_type' => 'required|in:Teaching,Break,Assembly,Games,Closing',
        ]);

        TimetablePeriod::create($validated);
        Alert::success('Created', 'Period slot added.');
        return redirect()->route('admin.timetable.periods');
    }

    public function updatePeriod(Request $request, TimetablePeriod $period)
    {
        $validated = $request->validate([
            'period_name' => 'required|string|max:30|unique:timetable_periods,period_name,' . $period->id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'period_order' => 'required|integer|min:1',
            'period_type' => 'required|in:Teaching,Break,Assembly,Games,Closing',
        ]);

        $period->update($validated);
        Alert::success('Updated', 'Period slot updated.');
        return redirect()->route('admin.timetable.periods');
    }

    public function destroyPeriod(TimetablePeriod $period)
    {
        if ($period->timetables()->exists()) {
            Alert::error('Error', 'Cannot delete period used in timetables.');
            return back();
        }
        $period->delete();
        Alert::success('Deleted', 'Period slot removed.');
        return redirect()->route('admin.timetable.periods');
    }

    /* ── Timetable Builder ──────────────────────────────── */
    public function builder(Request $request)
    {
        $session = AcademicSession::getCurrent();
        $armId = $request->query('arm_id');
        $classArms = ClassArm::with('classLevel')->orderBy('arm')->get();
        $periods = TimetablePeriod::orderBy('period_order')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        $selectedArm = null;
        $timetableGrid = [];
        $armSubjects = [];
        $armTeachers = [];

        if ($armId) {
            $selectedArm = ClassArm::with(['classLevel', 'armSubjects.subject'])
    ->find($armId);

            // Get entries organized by day|period
            $entries = Timetable::with(['subject', 'teacher.user', 'period'])
                ->where('class_arm_id', $armId)
                ->where('session_id', $session->id)
                ->get()
                ->keyBy(fn($item) => $item->day_of_week . '|' . $item->period_id);

            foreach ($days as $day) {
                foreach ($periods as $period) {
                    $key = $day . '|' . $period->id;
                    $timetableGrid[$day][$period->id] = $entries->get($key);
                }
            }

            // Get subjects assigned to this arm (via armSubjects)
            $armSubjects = $selectedArm->armSubjects()
                ->where('session_id', $session->id)
                ->with('subject')
                ->get()
                ->pluck('subject')
                ->unique('id')
                ->values();

            // Get teachers assigned to this arm
            // NEW (fixed):
$armTeachers = Teacher::with('user')
    ->where(function ($q) use ($armId, $session) {
        $q->whereHas('armSubjectAssignments', function ($sq) use ($armId, $session) {
            $sq->whereHas('armSubject', function ($aq) use ($armId, $session) {
                $aq->where('class_arm_id', $armId)->where('session_id', $session->id);
            });
        })
        ->orWhereHas('classArmTeachers', function ($sq) use ($armId, $session) {
            $sq->where('class_arm_id', $armId)->where('session_id', $session->id);
        });
    })
    ->get();
        }

        return view('admin.timetable.builder', compact(
            'classArms', 'periods', 'days', 'selectedArm', 'armId',
            'timetableGrid', 'armSubjects', 'armTeachers', 'session'
        ));
    }

    /* ── Store Timetable Entry ──────────────────────────── */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_arm_id' => 'required|exists:class_arms,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'period_id' => 'required|exists:timetable_periods,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'room' => 'nullable|string|max:50',
        ]);

        $period = TimetablePeriod::find($validated['period_id']);

        // Non-teaching periods don't need subject/teacher
        if (!$period->isTeaching()) {
            $validated['subject_id'] = null;
            $validated['teacher_id'] = null;
        } else {
            // Teaching periods require subject
            if (empty($validated['subject_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject is required for teaching periods.',
                ], 422);
            }
        }

        // Check teacher conflict via service
        if ($validated['teacher_id']) {
            $conflict = $this->timetableService->checkTeacherConflict(
                teacherId: $validated['teacher_id'],
                periodId: $validated['period_id'],
                day: $validated['day_of_week'],
                sessionId: $validated['session_id'],
                excludingArmId: $validated['class_arm_id']
            );

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => "Teacher already assigned to {$conflict['class_arm']} for {$conflict['subject']} at this time.",
                ], 422);
            }
        }

        // Check subject conflict (warning only, not blocking)
        $subjectConflict = null;
        if ($validated['subject_id'] && $period->isTeaching()) {
            $subjectConflict = $this->timetableService->checkSubjectConflict(
                subjectId: $validated['subject_id'],
                classArmId: $validated['class_arm_id'],
                day: $validated['day_of_week'],
                sessionId: $validated['session_id'],
                excludingPeriodId: $validated['period_id']
            );
        }

        // Upsert
        $entry = Timetable::updateOrCreate(
            [
                'class_arm_id' => $validated['class_arm_id'],
                'session_id' => $validated['session_id'],
                'period_id' => $validated['period_id'],
                'day_of_week' => $validated['day_of_week'],
            ],
            [
                'subject_id' => $validated['subject_id'],
                'teacher_id' => $validated['teacher_id'],
                'room' => $validated['room'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Entry saved.' . ($subjectConflict ? " Warning: {$subjectConflict['subject']} is already scheduled on {$subjectConflict['day']} during {$subjectConflict['period']}." : ''),
            'entry_id' => $entry->id,
        ]);
    }

    /* ── Delete Timetable Entry ─────────────────────────── */
    public function destroy(Timetable $timetable)
    {
        $timetable->delete();
        return response()->json(['success' => true, 'message' => 'Entry removed.']);
    }

    /* ── Copy Timetable ─────────────────────────────────── */
    public function copyTimetable(Request $request)
    {
        $validated = $request->validate([
            'from_class_arm_id' => 'required|exists:class_arms,id',
            'to_class_arm_id' => 'required|exists:class_arms,id|different:from_class_arm_id',
            'session_id' => 'required|exists:academic_sessions,id',
        ]);

        $fromEntries = Timetable::where('class_arm_id', $validated['from_class_arm_id'])
            ->where('session_id', $validated['session_id'])
            ->get();

        DB::transaction(function () use ($fromEntries, $validated) {
            Timetable::where('class_arm_id', $validated['to_class_arm_id'])
                ->where('session_id', $validated['session_id'])
                ->delete();

            foreach ($fromEntries as $entry) {
                Timetable::create([
                    'class_arm_id' => $validated['to_class_arm_id'],
                    'session_id' => $validated['session_id'],
                    'period_id' => $entry->period_id,
                    'day_of_week' => $entry->day_of_week,
                    'subject_id' => $entry->subject_id,
                    'teacher_id' => $entry->teacher_id,
                    'room' => $entry->room,
                ]);
            }
        });

        Alert::success('Copied', 'Timetable copied successfully.');
        return redirect()->route('admin.timetable.builder', ['arm_id' => $validated['to_class_arm_id']]);
    }

    /* ── Print Timetable PDF ────────────────────────────── */
    public function print(Request $request)
    {
        $armId = $request->query('arm_id');
        if (!$armId) {
            Alert::error('Error', 'Class arm is required.');
            return back();
        }

        $session = AcademicSession::getCurrent();
        $classArm = ClassArm::with('classLevel')->findOrFail($armId);
        $periods = TimetablePeriod::orderBy('period_order')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $school = \App\Models\SchoolProfile::first();

        $entries = Timetable::with(['subject', 'teacher.user', 'period'])
            ->where('class_arm_id', $armId)
            ->where('session_id', $session->id)
            ->get()
            ->keyBy(fn($item) => $item->day_of_week . '|' . $item->period_id);

        $timetableGrid = [];
        foreach ($days as $day) {
            foreach ($periods as $period) {
                $key = $day . '|' . $period->id;
                $timetableGrid[$day][$period->id] = $entries->get($key);
            }
        }

        $pdf = Pdf::loadView('pdf.timetable', compact(
            'classArm', 'periods', 'days', 'timetableGrid', 'session', 'school'
        ))->setPaper('a4', 'landscape');

        $safeName = str_replace(['/', '\\'], '_', $classArm->full_name);
        return $pdf->stream("timetable-{$safeName}-{$session->name}.pdf");
    }
}
