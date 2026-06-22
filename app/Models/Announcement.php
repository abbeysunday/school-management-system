<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    protected $fillable = [
        'created_by','title','body','audience',
        'class_arm_id','target_student_id','priority',
        'attachment','send_sms','send_email',
        'sms_sent_at','scheduled_at','is_published','published_at',
    ];

    protected $casts = [
        'send_sms'     => 'boolean',
        'send_email'   => 'boolean',
        'is_published' => 'boolean',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'sms_sent_at'  => 'datetime',
    ];

    public function author(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function classArm(): BelongsTo { return $this->belongsTo(ClassArm::class); }
    public function targetStudent(): BelongsTo { return $this->belongsTo(Student::class, 'target_student_id'); }
    public function reads(): HasMany { return $this->hasMany(AnnouncementRead::class); }

    public function scopePublished($query) { return $query->where('is_published', true); }

    public function isReadBy(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }
}
