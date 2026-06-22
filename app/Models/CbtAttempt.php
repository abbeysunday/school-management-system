<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CbtAttempt extends Model
{
    protected $fillable = [
        'cbt_exam_id','student_id','attempt_number',
        'started_at','submitted_at','time_spent_seconds',
        'score','percentage',
        'total_answered','total_correct','total_wrong','total_skipped',
        'status','ip_address',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'score'        => 'decimal:2',
        'percentage'   => 'decimal:2',
    ];

    public function exam(): BelongsTo { return $this->belongsTo(CbtExam::class, 'cbt_exam_id'); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function answers(): HasMany { return $this->hasMany(CbtAnswer::class, 'attempt_id'); }

    public function isInProgress(): bool { return $this->status === 'In Progress'; }
    public function isSubmitted(): bool { return $this->status === 'Submitted'; }
}
