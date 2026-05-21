<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_id',
        'rate',
        'date',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'float',
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * 匯率屬於某個幣別
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}