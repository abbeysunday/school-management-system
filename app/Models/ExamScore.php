<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamScore extends Model
{
    protected $fillable = [
        'student_id','subject_id','class_arm_id',
        'term_id','score','recorded_by','submitted_at',
    ];

    protected $casts = ['score' => 'decimal:2', 'submitted_at' => 'datetime'];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function classArm(): BelongsTo { return $this->belongsTo(ClassArm::class); }
    public function term(): BelongsTo { return $this->belongsTo(Term::class); }
    public function recorder(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
