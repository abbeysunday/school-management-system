<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    protected $fillable = ['payment_id', 'ledger_id', 'amount_allocated'];

    protected $casts = ['amount_allocated' => 'decimal:2'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function ledger(): BelongsTo
    {
        return $this->belongsTo(StudentFeeLedger::class, 'ledger_id');
    }
}
