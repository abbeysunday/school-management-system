<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'student_id','class_arm_id','term_id',
        'attendance_date','status','remarks','marked_by',
    ];

    protected $casts = ['attendance_date' => 'date'];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function classArm(): BelongsTo { return $this->belongsTo(ClassArm::class); }
    public function term(): BelongsTo { return $this->belongsTo(Term::class); }
    public function markedBy(): BelongsTo { return $this->belongsTo(User::class, 'marked_by'); }

    public function isPresent(): bool { return in_array($this->status, ['Present', 'Late']); }
    public function isAbsent(): bool { return $this->status === 'Absent'; }

    public function scopePresent($query) { return $query->whereIn('status', ['Present', 'Late']); }
    public function scopeAbsent($query) { return $query->where('status', 'Absent'); }
}
