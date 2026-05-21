<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    protected $fillable = ['currency_id', 'rate_to_twd', 'date'];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}