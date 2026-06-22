<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherArmSubject extends Model
{
    protected $fillable = ['teacher_id', 'arm_subject_id'];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function armSubject(): BelongsTo
    {
        return $this->belongsTo(ArmSubject::class);
    }
}
