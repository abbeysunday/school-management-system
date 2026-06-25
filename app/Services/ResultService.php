<?php

namespace App\Services;

use App\Models\ArmSubject;
use App\Models\CaConfiguration;
use App\Models\CaScore;
use App\Models\ExamScore;
use App\Models\GradingScale;
use App\Models\Result;
use App\Models\SchoolProfile;
use App\Models\StudentEnrollment;
use App\Models\Term;
use Illuminate\Support\Facades\DB;

class ResultCalculationService
{
    /**
     * Calculate results for all students in an arm_subject + term.
     */
    public function calculateForArmSubject(int $armSubjectId, int $termId): void
    {
        $armSubject = ArmSubject::findOrFail($armSubjectId);
        $classArmId = $armSubject->class_arm_id;
        $subjectId = $armSubject->subject_id;

        $enrolledStudentIds = StudentEnrollment::where('class_arm_id', $classArmId)
            ->where('term_id', $termId)
            ->where('is_active', true)
            ->pluck('student_id');

        foreach ($enrolledStudentIds as $studentId) {
            $this->computeStudentResult($studentId, $subjectId, $classArmId, $termId);
        }

        // Compute class-wide stats
        $this->computeClassStats($classArmId, $subjectId, $termId);

        // Compute subject positions within class arm
        $this->computePositions($classArmId, $subjectId, $termId);
    }

    /**
     * Compute a single student's result.
     */
    public function computeStudentResult(int $studentId, int $subjectId, int $classArmId, int $termId): Result
    {
        // CA Total from active components only
        $caTotal = CaScore::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->whereHas('caConfig', fn($q) => $q->where('is_active', true))
            ->sum('score');

        // Exam Score (uses 'score' column in exam_scores table)
        $examScore = ExamScore::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->value('score') ?? 0;

        // Total
        $totalScore = $caTotal + $examScore;

        // Grade
        $grade = $this->resolveGrade($totalScore);

        // Upsert result (matching user's schema)
        return Result::updateOrCreate(
            [
                'student_id'   => $studentId,
                'subject_id'   => $subjectId,
                'class_arm_id' => $classArmId,
                'term_id'      => $termId,
            ],
            [
                'ca_total'         => $caTotal,
                'exam_score'       => $examScore,
                'total_score'      => $totalScore,
                'grade'            => $grade['grade'],
                'grade_remark'     => $grade['grade_remark'],
                'teacher_remark'   => $grade['teacher_remark'],
                'is_published'     => false,
            ]
        );
    }

    /**
     * Compute class-wide stats (average, highest, lowest).
     */
    private function computeClassStats(int $classArmId, int $subjectId, int $termId): void
    {
        $stats = Result::where('class_arm_id', $classArmId)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->selectRaw('AVG(total_score) as avg, MAX(total_score) as max, MIN(total_score) as min')
            ->first();

        Result::where('class_arm_id', $classArmId)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->update([
                'class_average' => $stats?->avg ?? 0,
                'highest_score' => $stats?->max ?? 0,
                'lowest_score'  => $stats?->min ?? 0,
            ]);
    }

    /**
     * Resolve grade from total score using grading_scales.
     */
    public function resolveGrade(float $score): array
    {
        $scale = GradingScale::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();

        if (!$scale) {
            return [
                'grade'         => 'N/A',
                'grade_remark'  => 'No grade found',
                'teacher_remark'=> '',
            ];
        }

        return [
            'grade'         => $scale->grade,
            'grade_remark'  => $scale->remarks ?? '',
            'teacher_remark'=> $this->generateTeacherRemark($scale->grade),
        ];
    }

    /**
     * Generate a teacher remark based on grade.
     */
    private function generateTeacherRemark(string $grade): string
    {
        return match($grade) {
            'A1' => 'Excellent performance. Keep it up!',
            'B2', 'B3' => 'Very good. Keep working hard.',
            'C4', 'C5', 'C6' => 'Good. There is room for improvement.',
            'D7', 'E8' => 'Fair. Needs more effort and dedication.',
            'F9' => 'Poor. Needs serious improvement and extra help.',
            default => '',
        };
    }

    /**
     * Compute subject positions within a class arm.
     */
    private function computePositions(int $classArmId, int $subjectId, int $termId): void
    {
        $results = Result::where('class_arm_id', $classArmId)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->orderByDesc('total_score')
            ->get();

        $position = 1;
        $previousScore = null;
        $previousPosition = 1;

        foreach ($results as $result) {
            if ($previousScore !== null && $result->total_score < $previousScore) {
                $position = $previousPosition + 1;
            }
            $result->update(['subject_position' => $position]);
            $previousScore = $result->total_score;
            $previousPosition = $position;
        }
    }

    /**
     * Get preview data for exam score entry form.
     */
    public function getPreview(int $armSubjectId, int $termId): array
    {
        $armSubject = ArmSubject::findOrFail($armSubjectId);
        $classArmId = $armSubject->class_arm_id;
        $subjectId = $armSubject->subject_id;

        $enrollments = StudentEnrollment::with('student.user')
            ->where('class_arm_id', $classArmId)
            ->where('term_id', $termId)
            ->where('is_active', true)
            ->get();

        $preview = [];
        foreach ($enrollments as $enrollment) {
            $studentId = $enrollment->student_id;

            $caTotal = CaScore::where('student_id', $studentId)
                ->where('subject_id', $subjectId)
                ->where('term_id', $termId)
                ->whereHas('caConfig', fn($q) => $q->where('is_active', true))
                ->sum('score');

            $examScore = ExamScore::where('student_id', $studentId)
                ->where('subject_id', $subjectId)
                ->where('term_id', $termId)
                ->value('score') ?? 0;

            $total = $caTotal + $examScore;
            $grade = $this->resolveGrade($total);

            $preview[] = [
                'student_id'   => $studentId,
                'student_name' => $enrollment->student->user->full_name,
                'ca_total'     => $caTotal,
                'exam_score'   => $examScore,
                'total'        => $total,
                'grade'        => $grade['grade'],
                'grade_remark' => $grade['grade_remark'],
            ];
        }

        return $preview;
    }
}
