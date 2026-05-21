<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BankAccountService
{
    /**
     * 計算 `balance_twd = balance * 當日匯率`，保留兩位小數
     */
    public function recalculateTWD(BankAccount $account): void
    {
        $rate = app(ExchangeRateService::class)->getRate($account->currency->code);

        if ($rate) {
            $account->update([
                'balance_twd' => round($account->balance * $rate, 2),
            ]);
        }
    }

    /**
     * 重新計算該幣別所有活躍帳戶的 `balance_twd`
     */
    public function recalculateAllByCurrency(string $currencyCode): void
    {
        $currency = Currency::where('code', $currencyCode)->first();

        if (!$currency) {
            throw new \InvalidArgumentException("Currency [{$currencyCode}] not found");
        }

        $accounts = BankAccount::where('currency_id', $currency->id)
            ->where('is_active', true)
            ->get();

        foreach ($accounts as $account) {
            $this->recalculateTWD($account);
        }
    }

    /**
     * 建立帳戶並觸發重新計算
     */
    public function createAccount(array $data): BankAccount
    {
        $account = DB::transaction(function () use ($data) {
            $account = BankAccount::create($data);
            $this->recalculateTWD($account);
            return $account;
        });

        return $account;
    }

    /**
     * 更新帳戶並觸發重新計算
     */
    public function updateAccount(BankAccount $account, array $data): BankAccount
    {
        $account = DB::transaction(function () use ($account, $data) {
            $account->update($data);
            $this->recalculateTWD($account);
            return $account;
        });

        return $account;
    }

    /**
     * 軟刪除或硬刪除
     */
    public function deleteAccount(BankAccount $account): void
    {
        if (array_key_exists('deleted_at', $account->getAttributes())) {
            $account->delete(); // 軟刪除
        } else {
            $account->forceDelete(); // 硬刪除
        }
    }
}