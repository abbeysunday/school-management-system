<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    protected $fillable = [
        'user_id','admission_number','date_of_birth','gender',
        'religion','state_of_origin','lga','home_address',
        'blood_group','genotype','medical_conditions',
        'previous_school','admission_date','status',
    ];

    protected $casts = [
        'date_of_birth'  => 'date',
        'admission_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function currentEnrollment(): HasOne
    {
        return $this->hasOne(StudentEnrollment::class)
            ->where('is_active', true)
            ->latestOfMany();
    }


        public function currentArm(): ?ClassArm
        {
            return $this->currentEnrollment?->classArm;
        }

    public function parentStudents(): HasMany
    {
        return $this->hasMany(ParentStudent::class);
    }

    public function parents()
    {
        return $this->hasManyThrough(User::class, ParentStudent::class, 'student_id', 'id', 'id', 'parent_user_id');
    }

    public function primaryParent(): ?User
    {
        $ps = $this->parentStudents()->where('is_primary_contact', true)->with('parentUser')->first();
        return $ps?->parentUser;
    }

    public function feeLedger(): HasMany
    {
        return $this->hasMany(StudentFeeLedger::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function termSummaries(): HasMany
    {
        return $this->hasMany(TermSummary::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function cbtAttempts(): HasMany
    {
        return $this->hasMany(CbtAttempt::class);
    }

    public function promotionRecords(): HasMany
    {
        return $this->hasMany(PromotionRecord::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->full_name;
    }
}
