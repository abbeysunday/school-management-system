<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\ArmSubject;
use App\Models\AttendanceRecord;
use App\Models\CaConfiguration;
use App\Models\CaScore;
use App\Models\ClassArm;
use App\Models\ExamScore;
use App\Models\GradingScale;
use App\Models\Result;
use App\Models\SchoolProfile;
use App\Models\StudentEnrollment;
use App\Models\Term;
use App\Models\TermSummary;
use Illuminate\Support\Facades\DB;

class ResultCalculationService
{
    /**
     * 244. Calculate results for all students in an arm_subject + term.
     */
    public function calculateForArmSubject(int $armSubjectId, int $termId): void
    {
        $armSubject = ArmSubject::findOrFail($armSubjectId);
        $classArmId = $armSubject->class_arm_id;
        $subjectId = $armSubject->subject_id;

        $session = AcademicSession::getCurrent();

        // Enrolled students (per-session)
        $enrolledStudentIds = StudentEnrollment::where('class_arm_id', $classArmId)
            ->where('session_id', $session->id)
            ->where('is_active', true)
            ->pluck('student_id');

        foreach ($enrolledStudentIds as $studentId) {
            $this->computeStudentResult($studentId, $subjectId, $classArmId, $termId);
        }

        // 245. class_average, highest_score, lowest_score
        $this->computeClassStats($classArmId, $subjectId, $termId);

        // 246. Rank by total_score DESC, handle ties
        $this->computeSubjectPositions($classArmId, $subjectId, $termId);
    }

    public function computeStudentResult(int $studentId, int $subjectId, int $classArmId, int $termId): Result
    {
        $caTotal = CaScore::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->whereHas('caConfig', fn($q) => $q->where('is_active', true))
            ->sum('score');

        $examScore = ExamScore::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->value('score') ?? 0;

        $totalScore = $caTotal + $examScore;
        $grade = $this->resolveGrade($totalScore);

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
                'class_average' => round($stats?->avg ?? 0, 2),
                'highest_score' => round($stats?->max ?? 0, 2),
                'lowest_score'  => round($stats?->min ?? 0, 2),
            ]);
    }

    private function computeSubjectPositions(int $classArmId, int $subjectId, int $termId): void
    {
        $results = Result::where('class_arm_id', $classArmId)
            ->where('subject_id', $subjectId)
            ->where('term_id', $termId)
            ->orderByDesc('total_score')
            ->get();

        $position = 1;
        $previousScore = null;
        $previousPosition = 1;
        $skipCount = 0;

        foreach ($results as $result) {
            if ($previousScore !== null && $result->total_score < $previousScore) {
                $position = $previousPosition + $skipCount + 1;
                $skipCount = 0;
            } elseif ($previousScore !== null && $result->total_score == $previousScore) {
                $skipCount++;
            }

            $result->update(['subject_position' => $position]);
            $previousScore = $result->total_score;
            $previousPosition = $position;
        }
    }

    /**
     * 247. calculateTermSummary(class_arm_id, term_id)
     */
    public function calculateTermSummary(int $classArmId, int $termId): void
    {
        $classArm = ClassArm::with('classLevel')->findOrFail($classArmId);
        $classLevelId = $classArm->class_level_id;
        $session = AcademicSession::getCurrent();

        $enrollments = StudentEnrollment::with('student.user')
            ->where('class_arm_id', $classArmId)
            ->where('session_id', $session->id)
            ->where('is_active', true)
            ->get();

        $armSubjectIds = ArmSubject::where('class_arm_id', $classArmId)
            ->where('session_id', $session->id)
            ->pluck('subject_id');

        $totalSubjects = $armSubjectIds->count();
        $totalObtainable = $totalSubjects * 100;

        foreach ($enrollments as $enrollment) {
            $studentId = $enrollment->student_id;

            $subjectResults = Result::where('student_id', $studentId)
                ->where('class_arm_id', $classArmId)
                ->where('term_id', $termId)
                ->whereIn('subject_id', $armSubjectIds)
                ->get();

            $totalObtained = $subjectResults->sum('total_score');
            $noPassed = $subjectResults->filter(fn($r) => $r->grade !== 'F9')->count();
            $noFailed = $subjectResults->filter(fn($r) => $r->grade === 'F9')->count();

            $percentage = $totalObtainable > 0
                ? round(($totalObtained / $totalObtainable) * 100, 2)
                : 0;

            $overallGrade = $this->resolveGrade($percentage);

            $attendanceStats = AttendanceRecord::where('student_id', $studentId)
                ->where('term_id', $termId)
                ->selectRaw("
                    COUNT(*) as total_days,
                    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent
                ")
                ->first();

            $daysPresent = (int) ($attendanceStats?->present ?? 0);
            $daysAbsent = (int) ($attendanceStats?->absent ?? 0);
            $totalSchoolDays = (int) ($attendanceStats?->total_days ?? 0);

            TermSummary::updateOrCreate(
                ['student_id' => $studentId, 'term_id' => $termId],
                [
                    'class_arm_id'        => $classArmId,
                    'total_obtainable'    => $totalObtainable,
                    'total_obtained'      => $totalObtained,
                    'percentage'          => $percentage,
                    'no_of_subjects'      => $totalSubjects,
                    'no_passed'           => $noPassed,
                    'no_failed'           => $noFailed,
                    'days_present'        => $daysPresent,
                    'days_absent'         => $daysAbsent,
                    'total_school_days'   => $totalSchoolDays,
                    'form_teacher_remark' => $overallGrade['teacher_remark'],
                    'principal_remark'    => $overallGrade['grade_remark'],
                    'is_published'        => false,
                    'published_at'        => null,
                ]
            );
        }

        // 248. arm_position
        $this->computeArmPositions($classArmId, $termId);
        // 249. class_position
        $this->computeClassPositions($classLevelId, $termId);
    }

    private function computeArmPositions(int $classArmId, int $termId): void
    {
        $summaries = TermSummary::where('class_arm_id', $classArmId)
            ->where('term_id', $termId)
            ->orderByDesc('percentage')
            ->get();

        $this->applyRanking($summaries, 'arm_position');
    }

    private function computeClassPositions(int $classLevelId, int $termId): void
    {
        $classArmIds = ClassArm::where('class_level_id', $classLevelId)->pluck('id');

        $summaries = TermSummary::whereIn('class_arm_id', $classArmIds)
            ->where('term_id', $termId)
            ->orderByDesc('percentage')
            ->get();

        $this->applyRanking($summaries, 'class_position');
    }

    private function applyRanking($summaries, string $positionColumn): void
    {
        $position = 1;
        $previousScore = null;
        $previousPosition = 1;
        $skipCount = 0;

        foreach ($summaries as $summary) {
            $currentScore = (float) $summary->percentage;

            if ($previousScore !== null && $currentScore < $previousScore) {
                $position = $previousPosition + $skipCount + 1;
                $skipCount = 0;
            } elseif ($previousScore !== null && $currentScore == $previousScore) {
                $skipCount++;
            }

            $summary->update([$positionColumn => $position]);
            $previousScore = $currentScore;
            $previousPosition = $position;
        }
    }

    public function resolveGrade(float $score): array
    {
        $scale = GradingScale::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();

        if (!$scale) {
            return ['grade' => 'N/A', 'grade_remark' => 'No grade found', 'teacher_remark' => ''];
        }

        return [
            'grade'         => $scale->grade,
            'grade_remark'  => $scale->remarks ?? '',
            'teacher_remark'=> $this->generateTeacherRemark($scale->grade),
        ];
    }

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

    public function getGradeColor(string $grade): string
    {
        return match($grade) {
            'A1' => '#16a34a', 'B2', 'B3' => '#2563eb',
            'C4', 'C5', 'C6' => '#d97706', 'D7', 'E8' => '#ea580c',
            'F9' => '#dc2626', default => '#9ca3af',
        };
    }

    public function getGradeBgColor(string $grade): string
    {
        return match($grade) {
            'A1' => '#dcfce7', 'B2', 'B3' => '#dbeafe',
            'C4', 'C5', 'C6' => '#fef3c7', 'D7', 'E8' => '#ffedd5',
            'F9' => '#fee2e2', default => '#f3f4f6',
        };
    }
}
