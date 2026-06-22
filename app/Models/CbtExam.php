<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CbtExam extends Model
{
    protected $fillable = [
        'term_id','subject_id','class_arm_id','title','exam_type',
        'total_questions','duration_minutes','total_marks','marks_per_question',
        'negative_marking','marks_deducted_per_wrong',
        'randomize_questions','randomize_options',
        'show_result_immediately','allow_retake','max_retakes',
        'start_datetime','end_datetime','instructions','status','created_by',
    ];

    protected $casts = [
        'start_datetime'         => 'datetime',
        'end_datetime'           => 'datetime',
        'negative_marking'       => 'boolean',
        'randomize_questions'    => 'boolean',
        'randomize_options'      => 'boolean',
        'show_result_immediately'=> 'boolean',
        'allow_retake'           => 'boolean',
        'total_marks'            => 'decimal:2',
        'marks_per_question'     => 'decimal:2',
    ];

    public function term(): BelongsTo { return $this->belongsTo(Term::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function classArm(): BelongsTo { return $this->belongsTo(ClassArm::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'cbt_exam_questions')
            ->withPivot('question_order');
    }

    public function attempts(): HasMany { return $this->hasMany(CbtAttempt::class); }

    public function isActive(): bool { return $this->status === 'Active'; }
    public function isLive(): bool
    {
        return $this->status === 'Active'
            && now()->between($this->start_datetime, $this->end_datetime);
    }
}
