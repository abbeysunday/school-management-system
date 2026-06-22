<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolCalendar extends Model
{
    protected $fillable = ['title', 'date', 'type', 'term_id', 'is_public_holiday'];

    protected $casts = [
        'date' => 'date',
        'is_public_holiday' => 'boolean',
    ];
}
