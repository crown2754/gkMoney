<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_balance_twd',
        'snapshot_date',
    ];

    protected $casts = [
        'total_balance_twd' => 'float',
        'snapshot_date' => 'date',
    ];

    /**
     * 資產快照屬於某個使用者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}