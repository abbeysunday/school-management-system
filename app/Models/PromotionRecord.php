<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionRecord extends Model
{
    protected $fillable = [
        'student_id','session_id','from_class_arm_id','to_class_arm_id',
        'promotion_status','decision_type','decided_by','decision_reason','promoted_at',
    ];

    protected $casts = ['promoted_at' => 'datetime'];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function session(): BelongsTo { return $this->belongsTo(AcademicSession::class, 'session_id'); }
    public function fromArm(): BelongsTo { return $this->belongsTo(ClassArm::class, 'from_class_arm_id'); }
    public function toArm(): BelongsTo { return $this->belongsTo(ClassArm::class, 'to_class_arm_id'); }
    public function decidedBy(): BelongsTo { return $this->belongsTo(User::class, 'decided_by'); }
}
