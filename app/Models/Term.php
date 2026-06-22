<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    protected $fillable = [
        'session_id','name','start_date','end_date',
        'mid_term_break_start','mid_term_break_end',
        'next_resumption_date','total_school_days',
        'is_current','results_published',
    ];

    protected $casts = [
        'start_date'           => 'date',
        'end_date'             => 'date',
        'mid_term_break_start' => 'date',
        'mid_term_break_end'   => 'date',
        'next_resumption_date' => 'date',
        'is_current'           => 'boolean',
        'results_published'    => 'boolean',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public static function getCurrent(): static
    {
        return static::where('is_current', true)->firstOrFail();
    }
}
