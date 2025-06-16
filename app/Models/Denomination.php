<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denomination extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_id',
        'value',
        'count',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('count', '>', 0);
    }
}
