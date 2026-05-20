<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_id',
        'currency_id',
        'name',
        'account_number',
        'balance',
        'balance_twd',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'float',
        'balance_twd' => 'float',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'balance_formatted',
        'balance_twd_formatted',
    ];

    /**
     * 銀行帳戶屬於某個使用者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 銀行帳戶屬於某個銀行
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * 銀行帳戶屬於某個幣別
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * 取得格式化後的餘額（千分位逗號＋兩位小數）
     */
    public function getBalanceFormattedAttribute(): string
    {
        return number_format($this->balance, 2);
    }

    /**
     * 取得格式化後的台幣餘額（千分位逗號＋兩位小數）
     */
    public function getBalanceTwdFormattedAttribute(): string
    {
        return number_format($this->balance_twd, 2);
    }
}