<?php

namespace App\Services;

use App\Models\Timetable;

class TimetableService
{
    /**
     * Check if a teacher is already assigned to another class arm
     * at the same period and day.
     */
    public function checkTeacherConflict(
        int $teacherId,
        int $periodId,
        string $day,
        int $sessionId,
        ?int $excludingArmId = null
    ): ?array {
        $query = Timetable::with(['classArm.classLevel', 'subject'])
            ->where('teacher_id', $teacherId)
            ->where('period_id', $periodId)
            ->where('day_of_week', $day)
            ->where('session_id', $sessionId);

        if ($excludingArmId) {
            $query->where('class_arm_id', '!=', $excludingArmId);
        }

        $conflict = $query->first();

        if (!$conflict) {
            return null;
        }

        return [
            'class_arm' => $conflict->classArm?->full_name ?? 'Unknown',
            'subject' => $conflict->subject?->name ?? 'Unknown',
            'day' => $conflict->day_of_week,
            'period' => $conflict->period?->period_name ?? 'Unknown',
        ];
    }

    /**
     * Check if same subject is already scheduled on same day for same class.
     */
    public function checkSubjectConflict(
        int $subjectId,
        int $classArmId,
        string $day,
        int $sessionId,
        ?int $excludingPeriodId = null
    ): ?array {
        $query = Timetable::with(['period', 'subject'])
            ->where('class_arm_id', $classArmId)
            ->where('session_id', $sessionId)
            ->where('day_of_week', $day)
            ->where('subject_id', $subjectId);

        if ($excludingPeriodId) {
            $query->where('period_id', '!=', $excludingPeriodId);
        }

        $conflict = $query->first();

        if (!$conflict) {
            return null;
        }

        return [
            'subject' => $conflict->subject?->name ?? 'Unknown',
            'day' => $conflict->day_of_week,
            'period' => $conflict->period?->period_name ?? 'Unknown',
        ];
    }

    /**
     * Get all timetable entries for a class arm in a session,
     * organized by day and period.
     */
    public function getClassArmTimetable(int $classArmId, int $sessionId): array
    {
        $entries = Timetable::with(['subject', 'teacher.user', 'period'])
            ->where('class_arm_id', $classArmId)
            ->where('session_id', $sessionId)
            ->get()
            ->keyBy(fn($item) => $item->day_of_week . '|' . $item->period_id);

        return $entries->toArray();
    }

    /**
     * Get all timetable entries for a teacher in a session.
     */
    public function getTeacherTimetable(int $teacherId, int $sessionId): array
    {
        return Timetable::with(['subject', 'classArm.classLevel', 'period'])
            ->where('teacher_id', $teacherId)
            ->where('session_id', $sessionId)
            ->get()
            ->groupBy(fn($item) => $item->day_of_week . '|' . $item->period_id)
            ->toArray();
    }
}
