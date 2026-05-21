<?php

namespace Tests\Unit\Observers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\BankAccount;
use App\Models\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BankAccountObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_recalculates_twd_on_created_and_updated()
    {
        // 建立測試資料
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $bank = Bank::create([
            'code' => '007',
            'name' => 'First Bank',
            'is_active' => true,
        ]);

        $currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'is_active' => true,
        ]);

        // 建立當日匯率
        ExchangeRate::create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 31.500000,
            'date' => now()->toDateString(),
        ]);

        // 建立帳戶：驗證 created 時觸發 Observer
        $account = BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => 'My USD Account',
            'account_number' => '123456789',
            'balance' => 100.00,
            'is_active' => true,
        ]);

        // 100 * 31.5 = 3150
        $this->assertEquals(3150.00, $account->fresh()->balance_twd);

        // 更新帳戶：驗證 updated 時觸發 Observer
        $account->update([
            'balance' => 200.00,
        ]);

        // 200 * 31.5 = 6300
        $this->assertEquals(6300.00, $account->fresh()->balance_twd);
    }
}