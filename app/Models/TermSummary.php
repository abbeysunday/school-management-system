<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermSummary extends Model
{
    protected $fillable = [
        'student_id','class_arm_id','term_id',
        'total_obtainable','total_obtained','percentage',
        'arm_position','class_position',
        'no_of_subjects','no_passed','no_failed',
        'days_present','days_absent','total_school_days',
        'form_teacher_remark','principal_remark',
        'is_published','published_at',
    ];

    protected $casts = [
        'percentage'      => 'decimal:2',
        'total_obtainable'=> 'decimal:2',
        'total_obtained'  => 'decimal:2',
        'is_published'    => 'boolean',
        'published_at'    => 'datetime',
    ];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function classArm(): BelongsTo { return $this->belongsTo(ClassArm::class); }
    public function term(): BelongsTo { return $this->belongsTo(Term::class); }

    public function getAttendancePercentageAttribute(): float
    {
        if ($this->total_school_days === 0) return 0;
        return round(($this->days_present / $this->total_school_days) * 100, 1);
    }
}
