<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClassLevel extends Model
{
    protected $fillable = ['name', 'level_order', 'category'];

    public function classArms(): HasMany
    {
        return $this->hasMany(ClassArm::class);
    }

    public function promotionCriteria(): HasOne
    {
        return $this->hasOne(PromotionCriteria::class);
    }

    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function isJunior(): bool
    {
        return $this->category === 'Junior';
    }

    public function isSenior(): bool
    {
        return $this->category === 'Senior';
    }

    public function isFinalLevel(): bool
    {
        return $this->name === 'SS3';
    }

    public function nextLevel(): ?static
    {
        return static::where('level_order', $this->level_order + 1)->first();
    }
}
