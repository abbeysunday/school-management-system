<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionCriteria extends Model
{
    protected $fillable = ['class_level_id','min_percentage','min_subjects_passed','min_ca'];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'min_ca'         => 'decimal:2',
    ];

    public function classLevel(): BelongsTo { return $this->belongsTo(ClassLevel::class); }
}
