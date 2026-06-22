<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaScore extends Model
{
    protected $fillable = [
        'student_id','subject_id','class_arm_id',
        'term_id','ca_config_id','score','recorded_by',
    ];

    protected $casts = ['score' => 'decimal:2'];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function classArm(): BelongsTo { return $this->belongsTo(ClassArm::class); }
    public function term(): BelongsTo { return $this->belongsTo(Term::class); }
    public function caConfig(): BelongsTo { return $this->belongsTo(CaConfiguration::class, 'ca_config_id'); }
    public function recorder(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
