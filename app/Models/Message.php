<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'sender_id','recipient_id','subject','body',
        'is_read','read_at','parent_message_id',
        'sender_deleted','recipient_deleted',
    ];

    protected $casts = [
        'is_read'           => 'boolean',
        'read_at'           => 'datetime',
        'sender_deleted'    => 'boolean',
        'recipient_deleted' => 'boolean',
    ];

    public function sender(): BelongsTo { return $this->belongsTo(User::class, 'sender_id'); }
    public function recipient(): BelongsTo { return $this->belongsTo(User::class, 'recipient_id'); }
    public function parent(): BelongsTo { return $this->belongsTo(Message::class, 'parent_message_id'); }
    public function replies(): HasMany { return $this->hasMany(Message::class, 'parent_message_id'); }
}
