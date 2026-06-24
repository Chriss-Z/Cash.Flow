<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingCharge extends Model
{
    protected $fillable = [
        'bill_id',
        'invoice_date',
        'due_date',
        'amount',
        'discount_pct',
        'status',
        'description',
        'paid_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'discount_pct' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }
}
