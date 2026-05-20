<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetSnapshot extends Model
{
    protected $fillable = [
        'user_id',
        'total_twd',
        'snapshot_date',
    ];

    protected $casts = [
        'total_twd' => 'decimal:2',
        'snapshot_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}