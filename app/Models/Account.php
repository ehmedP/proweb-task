<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'currency_id',
        'account_number',
        'balance',
        'daily_limit',
        'daily_withdrawn',
        'last_withdrawal_date',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'daily_limit' => 'decimal:2',
        'daily_withdrawn' => 'decimal:2',
        'last_withdrawal_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function canWithdraw(float $amount): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->balance < $amount) {
            return false;
        }

        return ($this->daily_withdrawn + $amount) <= $this->daily_limit;
    }

    public function resetDailyLimitIfNeeded(): void
    {
        $today = now()->toDateString();

        if (is_null($this->last_withdrawal_date) ||
            $this->last_withdrawal_date->toDateString() !== $today) {

            $this->update([
                'daily_withdrawn' => 0,
                'last_withdrawal_date' => $today,
            ]);
        }
    }

}
