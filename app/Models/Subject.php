<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'name','code','category',
        'is_waec_subject','is_neco_subject','is_core','is_active',
    ];

    protected $casts = [
        'is_waec_subject' => 'boolean',
        'is_neco_subject' => 'boolean',
        'is_core'         => 'boolean',
        'is_active'       => 'boolean',
    ];

    public function armSubjects(): HasMany
    {
        return $this->hasMany(ArmSubject::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
