<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicSession extends Model
{
 protected $fillable = [
        'name', 'start_year', 'end_year', 'is_current', 'is_closed'
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'is_closed'  => 'boolean',
    ];

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class, 'session_id');
    }

    public function armSubjects(): HasMany
    {
        return $this->hasMany(ArmSubject::class, 'session_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class, 'session_id');
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
