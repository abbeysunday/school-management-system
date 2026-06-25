<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ArmSubject;
use App\Models\ExamScore;
use App\Models\GradingScale;
use App\Models\SchoolProfile;
use App\Models\StudentEnrollment;
use App\Models\TeacherArmSubject;
use App\Models\Term;
use App\Services\ResultCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class ExamScoreController extends Controller
{
    public function __construct(private ResultCalculationService $calcService) {}

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

        $school = SchoolProfile::first();
        $examMax = 100 - ($school->ca_weight ?? 30);

        // FIX: Use session_id instead of term_id for enrollment query
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

        $isSubmitted = $existingScores->contains(fn($s) => $s->submitted_at !== null);

        $caTotals = DB::table('ca_scores')
            ->select('student_id', DB::raw('SUM(score) as total'))
            ->where('subject_id', $armSubject->subject_id)
            ->where('class_arm_id', $armSubject->class_arm_id)
            ->where('term_id', $term->id)
            ->groupBy('student_id')
            ->pluck('total', 'student_id');

        $gradingScales = GradingScale::orderBy('min_score', 'desc')->get();

$gradingScalesJson = $gradingScales->map(fn($s) => [
    'min'   => $s->min_score,
    'max'   => $s->max_score,
    'grade' => $s->grade,
    'color' => $s->grade === 'A1' ? '#16a34a' : ($s->grade === 'F9' ? '#dc2626' : '#2563eb')
]);

return view('teacher.scores.exam', compact(
    'armSubject', 'enrollments', 'existingScores', 'caTotals',
    'examMax', 'term', 'isSubmitted', 'gradingScales', 'gradingScalesJson'
));
    }

    public function store(Request $request, int $armSubjectId)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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
        $alreadySubmitted = ExamScore::where('subject_id', $armSubject->subject_id)
            ->where('class_arm_id', $armSubject->class_arm_id)
            ->where('term_id', $term->id)
            ->whereNotNull('submitted_at')
            ->exists();

        if ($alreadySubmitted) {
            return response()->json(['success' => false, 'message' => 'Scores already submitted for review. Contact admin to unlock.'], 403);
        }

        $school = SchoolProfile::first();
        $examMax = 100 - ($school->ca_weight ?? 30);

        $validated = $request->validate([
            'scores'              => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.score'      => 'nullable|numeric|min:0|max:' . $examMax,
        ]);

        DB::transaction(function () use ($validated, $armSubject, $term, $teacher, $examMax) {
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
                        'recorded_by'  => $teacher->user_id,
                    ]
                );
            }
        });

        $this->calcService->calculateForArmSubject($armSubject->id, $term->id);

        return response()->json([
            'success' => true,
            'message' => 'Exam scores saved successfully.',
        ]);
    }

    public function submitForReview(Request $request, int $armSubjectId)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $term = Term::getCurrent();

        $assignment = TeacherArmSubject::where('teacher_id', $teacher->id)
            ->where('arm_subject_id', $armSubjectId)
            ->first();

        if (!$assignment) {
            return response()->json(['success' => false, 'message' => 'Not assigned'], 403);
        }

        $armSubject = ArmSubject::findOrFail($armSubjectId);

        ExamScore::where('subject_id', $armSubject->subject_id)
            ->where('class_arm_id', $armSubject->class_arm_id)
            ->where('term_id', $term->id)
            ->whereNull('submitted_at')
            ->update(['submitted_at' => now()]);

        $this->calcService->calculateForArmSubject($armSubject->id, $term->id);

        return response()->json([
            'success' => true,
            'message' => 'Scores submitted for review. They are now locked for editing.',
        ]);
    }
}
