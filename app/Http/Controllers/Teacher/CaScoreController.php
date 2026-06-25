<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ArmSubject;
use App\Models\CaConfiguration;
use App\Models\CaScore;
use App\Models\ExamScore;
use App\Models\StudentEnrollment;
use App\Models\TeacherArmSubject;
use App\Models\Term;
use App\Services\ResultCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class CaScoreController extends Controller
{
    public function __construct(private ResultCalculationService $calcService) {}

    public function index()
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            Alert::error('Error', 'You are not registered as a teacher.');
            return redirect()->route('teacher.dashboard');
        }

        $session = AcademicSession::getCurrent();
        $term = Term::getCurrent();

        $assignments = TeacherArmSubject::with(['armSubject.classArm.classLevel', 'armSubject.subject'])
            ->where('teacher_id', $teacher->id)
            ->whereHas('armSubject', fn($q) => $q->where('session_id', $session->id))
            ->get();

        $subjects = $assignments->map(function ($assignment) use ($term, $session) {
            $armSubject = $assignment->armSubject;
            $classArm = $armSubject->classArm;

            $caProgress = $this->getCaProgress($armSubject->class_arm_id, $armSubject->subject_id, $term->id, $session->id);
            $examProgress = $this->getExamProgress($armSubject->class_arm_id, $armSubject->subject_id, $term->id, $session->id);

            return [
                'assignment'     => $assignment,
                'arm_subject'    => $armSubject,
                'class_arm'      => $classArm,
                'subject'        => $armSubject->subject,
                'ca_progress'    => $caProgress,
                'exam_progress'  => $examProgress,
                'is_locked'      => $term->results_published,
            ];
        });

        return view('teacher.scores.index', compact('subjects', 'term', 'session'));
    }

    public function showForm(int $armSubjectId)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            Alert::error('Error', 'Not a teacher.');
            return redirect()->route('teacher.dashboard');
        }

        $session = AcademicSession::getCurrent();
        $term = Term::getCurrent();

        $assignment = TeacherArmSubject::where('teacher_id', $teacher->id)
            ->where('arm_subject_id', $armSubjectId)
            ->first();

        if (!$assignment) {
            Alert::error('Unauthorized', 'You are not assigned to this subject.');
            return redirect()->route('teacher.ca-scores.index');
        }

        if ($term->results_published) {
            Alert::error('Locked', 'Results have been published. Scores are locked.');
            return redirect()->route('teacher.ca-scores.index');
        }

        $armSubject = ArmSubject::with(['classArm.classLevel', 'subject'])
            ->findOrFail($armSubjectId);

        $caConfigs = CaConfiguration::active()->get();

        // FIX: Use session_id instead of term_id for enrollment query
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

        return view('teacher.scores.ca', compact(
            'armSubject', 'caConfigs', 'enrollments', 'scoreMatrix', 'term'
        ));
    }

    public function store(Request $request, int $armSubjectId)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $session = AcademicSession::getCurrent();
        $term = Term::getCurrent();

        $assignment = TeacherArmSubject::where('teacher_id', $teacher->id)
            ->where('arm_subject_id', $armSubjectId)
            ->first();

        if (!$assignment) {
            return response()->json(['success' => false, 'message' => 'Not assigned'], 403);
        }

        if ($term->results_published) {
            return response()->json(['success' => false, 'message' => 'Scores are locked'], 403);
        }

        $armSubject = ArmSubject::findOrFail($armSubjectId);

        $validated = $request->validate([
            'scores'              => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.config_id'  => 'required|exists:ca_configurations,id',
            'scores.*.score'      => 'nullable|numeric|min:0',
        ]);

        $caConfigs = CaConfiguration::active()->pluck('max_score', 'id');

        DB::transaction(function () use ($validated, $armSubject, $term, $teacher, $caConfigs) {
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
                        'recorded_by' => $teacher->user_id,
                    ]
                );
            }
        });

        $this->calcService->calculateForArmSubject($armSubject->id, $term->id);

        return response()->json([
            'success' => true,
            'message' => 'CA scores saved successfully.',
        ]);
    }

    private function getCaProgress(int $classArmId, int $subjectId, int $termId, int $sessionId): array
    {
        $activeConfigs = CaConfiguration::active()->count();

        // FIX: Use session_id for enrollment count
        $studentIds = StudentEnrollment::where('class_arm_id', $classArmId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->pluck('student_id');

        $total = $studentIds->count();
        if ($total === 0 || $activeConfigs === 0) {
            return ['total_students' => 0, 'completed' => 0, 'percentage' => 0];
        }

        $completed = CaScore::whereIn('student_id', $studentIds)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->selectRaw('student_id, COUNT(DISTINCT ca_config_id) as config_count')
            ->groupBy('student_id')
            ->havingRaw('config_count >= ?', [$activeConfigs])
            ->count();

        return [
            'total_students' => $total,
            'completed'      => $completed,
            'percentage'     => round(($completed / $total) * 100, 1),
        ];
    }

    private function getExamProgress(int $classArmId, int $subjectId, int $termId, int $sessionId): array
    {
        // FIX: Use session_id for enrollment count
        $studentIds = StudentEnrollment::where('class_arm_id', $classArmId)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->pluck('student_id');

        $total = $studentIds->count();
        if ($total === 0) {
            return ['total_students' => 0, 'completed' => 0, 'percentage' => 0];
        }

        $completed = ExamScore::whereIn('student_id', $studentIds)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->where('score', '>', 0)
            ->count();

        return [
            'total_students' => $total,
            'completed'      => $completed,
            'percentage'     => round(($completed / $total) * 100, 1),
        ];
    }
}
