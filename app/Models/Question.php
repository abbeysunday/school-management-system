<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    protected $fillable = [
        'subject_id','class_level_id','created_by',
        'question_text','option_a','option_b','option_c','option_d',
        'correct_option','explanation','difficulty','image_path','is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function classLevel(): BelongsTo { return $this->belongsTo(ClassLevel::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function cbtExams(): BelongsToMany
    {
        return $this->belongsToMany(CbtExam::class, 'cbt_exam_questions')
            ->withPivot('question_order')
            ->withTimestamps();
    }

    public function scopeActive($query) { return $query->where('is_active', true); }
}
