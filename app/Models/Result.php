<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    protected $fillable = [
        'student_id','subject_id','class_arm_id','term_id',
        'ca_total','exam_score','total_score',
        'class_average','highest_score','lowest_score',
        'grade','grade_remark','subject_position',
        'teacher_remark','is_published',
    ];

    protected $casts = [
        'ca_total'        => 'decimal:2',
        'exam_score'      => 'decimal:2',
        'total_score'     => 'decimal:2',
        'class_average'   => 'decimal:2',
        'highest_score'   => 'decimal:2',
        'lowest_score'    => 'decimal:2',
        'is_published'    => 'boolean',
    ];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function classArm(): BelongsTo { return $this->belongsTo(ClassArm::class); }
    public function term(): BelongsTo { return $this->belongsTo(Term::class); }

    public function scopePublished($query) { return $query->where('is_published', true); }
    public function isPassed(): bool { return $this->grade !== 'F9'; }
}
