<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = [
        'user_id','staff_id','date_of_birth','gender',
        'qualification','specialization','employment_date',
        'employment_type','address',
        'next_of_kin_name','next_of_kin_phone','next_of_kin_relationship',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth'   => 'date',
        'employment_date' => 'date',
        'is_active'       => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function armSubjectAssignments(): HasMany
    {
        return $this->hasMany(TeacherArmSubject::class);
    }

    public function classArmTeachers(): HasMany
    {
        return $this->hasMany(ClassArmTeacher::class);
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->full_name;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
