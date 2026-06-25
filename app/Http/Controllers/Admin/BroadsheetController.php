<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\BroadsheetExport;
use App\Models\ArmSubject;
use App\Models\ClassArm;
use App\Models\GradingScale;
use App\Models\Result;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Term;
use App\Models\TermSummary;
use App\Services\ResultCalculationService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class BroadsheetController extends Controller
{
    public function __construct(private ResultCalculationService $calcService) {}

    public function index(Request $request)
    {
        $termId = $request->get('term_id');
        $armId = $request->get('arm_id');

        if (!$armId || !$termId) {
            $terms = Term::with('session')->orderByDesc('id')->get();
            $classArms = ClassArm::with('classLevel')
                ->whereHas('enrollments', fn($q) => $q->where('is_active', true))
                ->get();

            return view('admin.results.broadsheet_select', compact('terms', 'classArms'));
        }

        // FIX: ClassArm has no 'session' relationship. Get session from term instead.
        $classArm = ClassArm::with('classLevel')->findOrFail($armId);
        $term = Term::with('session')->findOrFail($termId);
        $sessionId = $term->session_id; // Get session_id from term, not classArm

        $subjects = Subject::whereIn('id', function ($q) use ($armId) {
            $q->select('subject_id')
              ->from('arm_subjects')
              ->where('class_arm_id', $armId);
        })->orderBy('name')->get();

        $enrollments = StudentEnrollment::with('student.user')
            ->where('class_arm_id', $armId)
            ->where('session_id', $sessionId) // Use session_id from term
            ->where('is_active', true)
            ->get()
            ->sortBy('student.user.full_name');

        $results = Result::where('class_arm_id', $armId)
            ->where('term_id', $termId)
            ->whereIn('subject_id', $subjects->pluck('id'))
            ->get()
            ->keyBy(fn($r) => $r->student_id . '|' . $r->subject_id);

        $termSummaries = TermSummary::where('class_arm_id', $armId)
            ->where('term_id', $termId)
            ->get()
            ->keyBy('student_id');

        $gradeColors = [];
        foreach ($enrollments as $enrollment) {
            $studentId = $enrollment->student_id;
            foreach ($subjects as $subject) {
                $result = $results->get($studentId . '|' . $subject->id);
                if ($result) {
                    $gradeColors[$studentId][$subject->id] = [
                        'color' => $this->calcService->getGradeColor($result->grade),
                        'bg'    => $this->calcService->getGradeBgColor($result->grade),
                    ];
                }
            }
        }

        return view('admin.results.broadsheet', compact(
            'classArm', 'term', 'subjects', 'enrollments',
            'results', 'termSummaries', 'gradeColors'
        ));
    }

    public function export(Request $request)
    {
        $termId = $request->get('term_id');
        $armId = $request->get('arm_id');

        if (!$armId || !$termId) {
            Alert::error('Error', 'Please select a class arm and term.');
            return redirect()->back();
        }

        $classArm = ClassArm::with('classLevel')->findOrFail($armId);
        $term = Term::findOrFail($termId);

        return Excel::download(
            new BroadsheetExport($armId, $termId),
            "broadsheet_{$classArm->full_name}_{$term->name}.xlsx"
        );
    }

    public function recalculate(Request $request)
    {
        $termId = $request->get('term_id');
        $armId = $request->get('arm_id');

        if (!$armId || !$termId) {
            Alert::error('Error', 'Please select a class arm and term.');
            return redirect()->back();
        }

        $armSubjectIds = ArmSubject::where('class_arm_id', $armId)
            ->pluck('id');

        foreach ($armSubjectIds as $armSubjectId) {
            $this->calcService->calculateForArmSubject($armSubjectId, $termId);
        }

        $this->calcService->calculateTermSummary($armId, $termId);

        Alert::success('Recalculated', 'All results and rankings have been recalculated.');
        return redirect()->route('admin.results.broadsheet', ['arm_id' => $armId, 'term_id' => $termId]);
    }
}
