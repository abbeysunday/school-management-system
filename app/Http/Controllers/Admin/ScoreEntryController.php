<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ArmSubject;
use App\Models\CaConfiguration;
use App\Models\CaScore;
use App\Models\ClassArm;
use App\Models\ExamScore;
use App\Models\GradingScale;
use App\Models\Result;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TeacherArmSubject;
use App\Models\Term;
use App\Models\TermSummary;
use App\Services\ResultCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class ScoreEntryController extends Controller
{
    public function __construct(private ResultCalculationService $calcService) {}

    public function dashboard()
{
    $session = AcademicSession::getCurrent();
    $term = Term::getCurrent();
    $school = SchoolProfile::first();
    $caWeight = $school->ca_weight ?? 30;
    $examMax = 100 - $caWeight;

    $armSubjects = ArmSubject::with(['classArm.classLevel', 'subject', 'teacherAssignments.teacher.user'])
        ->where('session_id', $session->id)
        ->get();

    $statuses = $armSubjects->map(function ($armSubject) use ($term, $session, $caWeight, $examMax) {
        $classArmId = $armSubject->class_arm_id;
        $subjectId = $armSubject->subject_id;

        $totalStudents = StudentEnrollment::where('class_arm_id', $classArmId)
            ->where('session_id', $session->id)
            ->where('is_active', true)
            ->count();

        $activeCaConfigs = CaConfiguration::active()->count();
        $caEnteredCount = 0;
        if ($totalStudents > 0 && $activeCaConfigs > 0) {
            $caEnteredCount = CaScore::where('subject_id', $subjectId)
                ->where('class_arm_id', $classArmId)
                ->where('term_id', $term->id)
                ->selectRaw('student_id, COUNT(DISTINCT ca_config_id) as cnt')
                ->groupBy('student_id')
                ->havingRaw('cnt >= ?', [$activeCaConfigs])
                ->count();
        }
        $caStatus = $totalStudents === 0 ? 'N/A' : ($caEnteredCount >= $totalStudents ? 'Complete' : 'Pending');
        $caPercent = $totalStudents > 0 ? round(($caEnteredCount / $totalStudents) * 100, 1) : 0;

        $examEnteredCount = ExamScore::where('subject_id', $subjectId)
            ->where('class_arm_id', $classArmId)
            ->where('term_id', $term->id)
            ->where('score', '>', 0)
            ->count();
        $examStatus = $totalStudents === 0 ? 'N/A' : ($examEnteredCount >= $totalStudents ? 'Complete' : 'Pending');
        $examPercent = $totalStudents > 0 ? round(($examEnteredCount / $totalStudents) * 100, 1) : 0;

        $submittedCount = ExamScore::where('subject_id', $subjectId)
            ->where('class_arm_id', $classArmId)
            ->where('term_id', $term->id)
            ->whereNotNull('submitted_at')
            ->count();

        // FIX: isSubmitted = all entered exam scores have been submitted
        $isSubmitted = $submittedCount > 0 && $submittedCount >= $examEnteredCount && $examEnteredCount > 0;

        // FIX: Check if term results are published FIRST
$overallStatus = 'Pending';
if ($term->results_published) {           // ← This must be checked FIRST
    $overallStatus = 'Published';
} elseif ($caStatus === 'Complete' && $examStatus === 'Complete') {
    $overallStatus = $isSubmitted ? 'Submitted' : 'Ready for Submission';
} elseif ($caStatus === 'N/A' && $examStatus === 'N/A') {
    $overallStatus = 'No Students';
}
        $teacherName = $armSubject->teacherAssignments->first()?->teacher?->user?->full_name ?? 'Unassigned';

        return [
            'arm_subject'     => $armSubject,
            'class_arm'       => $armSubject->classArm,
            'subject'         => $armSubject->subject,
            'teacher_name'    => $teacherName,
            'total_students'  => $totalStudents,
            'ca_status'       => $caStatus,
            'ca_percent'      => $caPercent,
            'exam_status'     => $examStatus,
            'exam_percent'    => $examPercent,
            'is_submitted'    => $isSubmitted,
            'overall_status'  => $overallStatus,
            'is_published'    => $term->results_published ?? false,
        ];
    });

    // Summary counts
    $summary = [
        'total_subjects'    => $statuses->count(),
        'ca_complete'       => $statuses->where('ca_status', 'Complete')->count(),
        'exam_complete'     => $statuses->where('exam_status', 'Complete')->count(),
        'submitted'         => $statuses->where('overall_status', 'Submitted')->count(),
        'published'         => $statuses->where('overall_status', 'Published')->count(),
        'pending'           => $statuses->where('overall_status', 'Pending')->count(),
    ];

    return view('admin.scores.dashboard', compact(
        'statuses', 'summary', 'term', 'session', 'caWeight', 'examMax'
    ));
}

    public function editCaScores(int $armSubjectId)
    {
        $session = AcademicSession::getCurrent();
        $term = Term::getCurrent();
        $armSubject = ArmSubject::with(['classArm.classLevel', 'subject'])
            ->findOrFail($armSubjectId);

        $caConfigs = CaConfiguration::active()->get();

        // FIX: Use session_id
        $enrollments = StudentEnrollment::with('student.user')
            ->where('class_arm_id', $armSubject->class_arm_id)
            ->where('session_id', $session->id)
            ->where('is_active', true)
            ->orderBy('student_id')
            ->get();

        $existingScores = CaScore::where('subject_id', $armSubject->subject_id)
            ->where('class_arm_id', $armSubject->class_arm_id)
            ->where('term_id', $term->id)
            ->get()
            ->keyBy(fn($s) => $s->student_id . '|' . $s->ca_config_id);

        $scoreMatrix = [];
        foreach ($enrollments as $enrollment) {
            foreach ($caConfigs as $config) {
                $key = $enrollment->student_id . '|' . $config->id;
                $scoreMatrix[$enrollment->student_id][$config->id] =
                    $existingScores->get($key)?->score ?? '';
            }
        }

        return view('admin.scores.ca_edit', compact(
            'armSubject', 'caConfigs', 'enrollments', 'scoreMatrix', 'term'
        ));
    }

    public function updateCaScores(Request $request, int $armSubjectId)
    {
        $term = Term::getCurrent();
        $armSubject = ArmSubject::findOrFail($armSubjectId);

        $validated = $request->validate([
            'scores'              => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.config_id'  => 'required|exists:ca_configurations,id',
            'scores.*.score'      => 'nullable|numeric|min:0',
        ]);

        $caConfigs = CaConfiguration::active()->pluck('max_score', 'id');
        $adminId = auth()->id();

        DB::transaction(function () use ($validated, $armSubject, $term, $adminId, $caConfigs) {
            foreach ($validated['scores'] as $item) {
                $maxScore = $caConfigs[$item['config_id']] ?? 999;
                $score = $item['score'] !== null && $item['score'] !== ''
                    ? min((float) $item['score'], (float) $maxScore)
                    : 0;

                CaScore::updateOrCreate(
                    [
                        'student_id'   => $item['student_id'],
                        'subject_id'   => $armSubject->subject_id,
                        'class_arm_id' => $armSubject->class_arm_id,
                        'term_id'      => $term->id,
                        'ca_config_id' => $item['config_id'],
                    ],
                    [
                        'score'       => $score,
                        'recorded_by' => $adminId,
                    ]
                );
            }
        });

        $this->calcService->calculateForArmSubject($armSubject->id, $term->id);

        Alert::success('Updated', 'CA scores updated by admin.');
        return redirect()->route('admin.scores.dashboard');
    }

    public function editExamScores(int $armSubjectId)
    {
        $session = AcademicSession::getCurrent();
        $term = Term::getCurrent();
        $armSubject = ArmSubject::with(['classArm.classLevel', 'subject'])
            ->findOrFail($armSubjectId);

        $school = SchoolProfile::first();
        $examMax = 100 - ($school->ca_weight ?? 30);

        // FIX: Use session_id
        $enrollments = StudentEnrollment::with('student.user')
            ->where('class_arm_id', $armSubject->class_arm_id)
            ->where('session_id', $session->id)
            ->where('is_active', true)
            ->orderBy('student_id')
            ->get();

        $existingScores = ExamScore::where('subject_id', $armSubject->subject_id)
            ->where('class_arm_id', $armSubject->class_arm_id)
            ->where('term_id', $term->id)
            ->get()
            ->keyBy('student_id');

        $caTotals = DB::table('ca_scores')
            ->select('student_id', DB::raw('SUM(score) as total'))
            ->where('subject_id', $armSubject->subject_id)
            ->where('class_arm_id', $armSubject->class_arm_id)
            ->where('term_id', $term->id)
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $gradingScales = GradingScale::orderBy('min_score', 'desc')->get();

        return view('admin.scores.exam_edit', compact(
            'armSubject', 'enrollments', 'existingScores', 'caTotals',
            'examMax', 'term', 'gradingScales'
        ));
    }

    public function updateExamScores(Request $request, int $armSubjectId)
    {
        $term = Term::getCurrent();
        $armSubject = ArmSubject::findOrFail($armSubjectId);
        $school = SchoolProfile::first();
        $examMax = 100 - ($school->ca_weight ?? 30);

        $validated = $request->validate([
            'scores'              => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.score'      => 'nullable|numeric|min:0|max:' . $examMax,
        ]);

        $adminId = auth()->id();

        DB::transaction(function () use ($validated, $armSubject, $term, $adminId, $examMax) {
            foreach ($validated['scores'] as $item) {
                $score = $item['score'] !== null && $item['score'] !== ''
                    ? min((float) $item['score'], (float) $examMax)
                    : 0;

                ExamScore::updateOrCreate(
                    [
                        'student_id'   => $item['student_id'],
                        'subject_id'   => $armSubject->subject_id,
                        'class_arm_id' => $armSubject->class_arm_id,
                        'term_id'      => $term->id,
                    ],
                    [
                        'score'        => $score,
                        'recorded_by'  => $adminId,
                    ]
                );
            }
        });

        $this->calcService->calculateForArmSubject($armSubject->id, $term->id);

        Alert::success('Updated', 'Exam scores updated by admin.');
        return redirect()->route('admin.scores.dashboard');
    }

    public function unlockScores(int $armSubjectId)
    {
        $term = Term::getCurrent();
        $armSubject = ArmSubject::findOrFail($armSubjectId);

        ExamScore::where('subject_id', $armSubject->subject_id)
            ->where('class_arm_id', $armSubject->class_arm_id)
            ->where('term_id', $term->id)
            ->whereNotNull('submitted_at')
            ->update(['submitted_at' => null]);

        Alert::success('Unlocked', 'Scores unlocked for re-editing.');
        return redirect()->route('admin.scores.dashboard');
    }

    public function publishResults()
{
    $term = Term::getCurrent();

    // Set term-level flag to lock ALL score entry
    $term->update(['results_published' => true]);

    // Also mark all individual results as published
    Result::where('term_id', $term->id)->update(['is_published' => true]);

    Alert::success('Published', 'Results have been published for ' . $term->name . '.');
    return redirect()->route('admin.scores.dashboard');
}
}
