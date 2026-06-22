<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimetablePeriod extends Model
{
    protected $fillable = ['period_name','start_time','end_time','period_order','period_type'];

    public function timetables(): HasMany { return $this->hasMany(Timetable::class, 'period_id'); }

    public function isTeaching(): bool { return $this->period_type === 'Teaching'; }
}
