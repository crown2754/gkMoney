<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'expenses';

    protected $fillable = [
        'user_id',
        'date',
        'amount_cents',
        'currency',
        'category_id',
        'description',
        'receipt_image_url',
        'is_deleted',
    ];

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'date' => 'date',
        'amount_cents' => 'integer',
        'currency' => 'string',
        'category_id' => 'string',
        'description' => 'string',
        'receipt_image_url' => 'string',
        'created_at' => 'datetime:tz',
        'updated_at' => 'datetime:tz',
        'is_deleted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ExpenseCategory::class, 'category_id');
    }
}