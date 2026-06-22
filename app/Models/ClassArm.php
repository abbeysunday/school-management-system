<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassArm extends Model
{
    protected $fillable = ['class_level_id', 'arm', 'capacity'];

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function armSubjects(): HasMany
    {
        return $this->hasMany(ArmSubject::class);
    }

    public function formTeacher(int $sessionId): ?Teacher
    {
        return $this->classArmTeachers()
            ->where('session_id', $sessionId)
            ->where('role', 'Form Teacher')
            ->first()
            ?->teacher;
    }

    public function classArmTeachers(): HasMany
    {
        return $this->hasMany(ClassArmTeacher::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->classLevel->name . $this->arm;
    }
}
