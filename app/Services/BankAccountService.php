<?php
// file: app/Services/BankAccountService.php

namespace App\Services;

use App\Models\BankAccount;
use Illuminate\Support\Facades\DB;

class BankAccountService
{
    /**
     * Recalculate the TWD equivalent balance for a single bank account.
     */
    public function recalculateTWD(BankAccount $account): void
    {
        $rate = app(ExchangeRateService::class)->getRate($account->currency->code);
        if ($rate !== null) {
            $balanceTwd = round($account->balance * $rate, 2);
            $account->update(['balance_twd' => $balanceTwd]);
        }
    }

    /**
     * Recalculate TWD balance for all active accounts in a specific currency.
     */
    public function recalculateAllByCurrency(int $currencyId): void
    {
        $accounts = BankAccount::where('currency_id', $currencyId)
            ->where('is_active', true)
            ->get();

        foreach ($accounts as $account) {
            $this->recalculateTWD($account);
        }
    }
}