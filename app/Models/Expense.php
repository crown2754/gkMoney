<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Expense extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'amount_cents' => 'integer',
        'expense_date' => 'date',
        'metadata' => 'array',
        'is_recurring' => 'boolean',
        'created_at' => 'datetime:tz',
        'updated_at' => 'datetime:tz',
        'deleted_at' => 'datetime:tz',
    ];

    protected $fillable = [
        'user_id',
        'account_id',
        'amount_cents',
        'currency',
        'expense_date',
        'description',
        'category_id',
        'is_recurring',
        'receipt_id',
        'external_id',
        'metadata',
    ];

    protected $attributes = [
        'currency' => 'USD',
        'is_recurring' => false,
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\ExpenseCategory::class);
    }

    public function receipt()
    {
        return $this->belongsTo(\App\Models\ExpenseReceipt::class);
    }

    public function tags()
    {
        return $this->belongsToMany(
            \App\Models\ExpenseTag::class,
            'expense_tag_pivot',
            'expense_id',
            'tag_id'
        )->withTimestamps();
    }

    // Accessor/Mutator for amount in dollars
    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->amount_cents / 100,
            set: fn (float $value) => $this->attributes['amount_cents'] = intval(round($value * 100))
        );
    }
}