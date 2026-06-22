<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timetable extends Model
{
    protected $fillable = [
        'class_arm_id','session_id','period_id',
        'day_of_week','subject_id','teacher_id','room',
    ];

    public function classArm(): BelongsTo { return $this->belongsTo(ClassArm::class); }
    public function session(): BelongsTo { return $this->belongsTo(AcademicSession::class, 'session_id'); }
    public function period(): BelongsTo { return $this->belongsTo(TimetablePeriod::class, 'period_id'); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }
}
