<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbtAnswer extends Model
{
    protected $fillable = [
        'attempt_id','question_id','selected_option',
        'is_correct','is_flagged','answered_at',
    ];

    protected $casts = [
        'is_correct'  => 'boolean',
        'is_flagged'  => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function attempt(): BelongsTo { return $this->belongsTo(CbtAttempt::class, 'attempt_id'); }
    public function question(): BelongsTo { return $this->belongsTo(Question::class); }
}
