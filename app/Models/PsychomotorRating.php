<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychomotorRating extends Model
{
    protected $fillable = ['student_id','term_id','domain','trait_name','rating','rated_by'];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function term(): BelongsTo { return $this->belongsTo(Term::class); }
    public function rater(): BelongsTo { return $this->belongsTo(User::class, 'rated_by'); }

    public function getRatingLabelAttribute(): string
    {
        return match($this->rating) {
            '5' => 'Excellent',
            '4' => 'Very Good',
            '3' => 'Good',
            '2' => 'Fair',
            '1' => 'Poor',
            default => '-',
        };
    }
}
