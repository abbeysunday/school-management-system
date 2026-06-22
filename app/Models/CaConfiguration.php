<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaConfiguration extends Model
{
    protected $fillable = ['component_name', 'max_score', 'order', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'max_score' => 'decimal:2'];

    public function caScores(): HasMany
    {
        return $this->hasMany(CaScore::class, 'ca_config_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
