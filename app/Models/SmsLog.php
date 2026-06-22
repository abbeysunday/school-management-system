<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        'sent_by','recipient_phone','recipient_user_id',
        'message','termii_message_id','status','purpose','cost','sent_at',
    ];

    protected $casts = ['sent_at' => 'datetime', 'cost' => 'decimal:4'];

    public function sentBy(): BelongsTo { return $this->belongsTo(User::class, 'sent_by'); }
    public function recipient(): BelongsTo { return $this->belongsTo(User::class, 'recipient_user_id'); }
}
