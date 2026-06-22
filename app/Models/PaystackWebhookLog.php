<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaystackWebhookLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event','payload','reference','processed','process_result','processed_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'processed'    => 'boolean',
        'processed_at' => 'datetime',
        'created_at'   => 'datetime',
    ];
}
