<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentFeeLedger extends Model
{
    protected $table = 'student_fee_ledger';

    protected $fillable = [
        'student_id','fee_structure_id','term_id',
        'original_amount','discount_amount','discount_reason','net_amount','amount_paid','status',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount'      => 'decimal:2',
        'amount_paid'     => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class, 'ledger_id');
    }

    public function getBalanceAttribute(): float
    {
        return $this->net_amount - $this->amount_paid;
    }
}
