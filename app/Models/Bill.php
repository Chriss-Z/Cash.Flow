<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bill extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'plan_amount',
        'daily_rate',
        'duration_days',
        'monthly_cycle_day',
        'prorata',
        'annual_discount_pct',
        'start_date',
        'due_date',
        'expires_at',
        'last_charge_date',
        'frequency',
        'status',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'plan_amount' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'annual_discount_pct' => 'decimal:2',
        'prorata' => 'boolean',
        'start_date' => 'date',
        'due_date' => 'date',
        'expires_at' => 'datetime',
        'last_charge_date' => 'date',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function charges(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BillingCharge::class, 'bill_id');
    }
}
