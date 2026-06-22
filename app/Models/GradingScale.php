<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingScale extends Model
{
    protected $fillable = ['grade','min_score','max_score','remark','is_pass','grade_order'];

    protected $casts = ['is_pass' => 'boolean', 'min_score' => 'decimal:2', 'max_score' => 'decimal:2'];

    public static function findGrade(float $score): ?static
    {
        return static::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();
    }
}
