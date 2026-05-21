<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\BankAccount;
use App\Models\ExchangeRate;
use App\Services\BankAccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BankAccountServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BankAccountService $service;
    protected User $user;
    protected Bank $bank;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BankAccountService();

        $this->user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->bank = Bank::create([
            'code' => '008',
            'name' => 'Hua Nan Bank',
            'is_active' => true,
        ]);

        $this->currency = Currency::create([
            'code' => 'EUR',
            'name' => 'Euro',
            'is_active' => true,
        ]);

        ExchangeRate::create([
            'currency_id' => $this->currency->id,
            'rate_to_twd' => 34.000000,
            'date' => now()->toDateString(),
        ]);
    }

    public function test_it_can_create_bank_account()
    {
        $data = [
            'bank_id' => $this->bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'My EUR Wallet',
            'account_number' => '888-999-111',
            'balance' => 150.00,
            'is_active' => true,
        ];

        $account = $this->service->createAccount($this->user->id, $data);

        $this->assertInstanceOf(BankAccount::class, $account);
        $this->assertEquals('My EUR Wallet', $account->alias);
        // 自動重新計算 TWD: 150 * 34 = 5100
        $this->assertEquals(5100.00, $account->balance_twd);
    }

    public function test_it_can_update_bank_account()
    {
        $account = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'Old Alias',
            'account_number' => '111',
            'balance' => 100.00,
            'is_active' => true,
        ]);

        $updatedData = [
            'alias' => 'New Alias',
            'balance' => 200.00,
        ];

        $this->service->updateAccount($account->id, $updatedData);

        $account->refresh();
        $this->assertEquals('New Alias', $account->alias);
        $this->assertEquals(200.00, $account->balance);
        $this->assertEquals(6800.00, $account->balance_twd);
    }

    public function test_it_can_delete_bank_account()
    {
        $account = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'Delete Me',
            'account_number' => '111',
            'balance' => 100.00,
            'is_active' => true,
        ]);

        $this->service->deleteAccount($account->id);

        $this->assertDatabaseMissing('bank_accounts', ['id' => $account->id]);
    }

    public function test_recalculate_all_by_currency()
    {
        $account1 = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'Acc 1',
            'account_number' => '111',
            'balance' => 100.00,
            'balance_twd' => 3400.00,
            'is_active' => true,
        ]);

        // 模擬當匯率調整到 35.00
        $this->service->recalculateAllByCurrency($this->currency->id, 35.00);

        $this->assertEquals(3500.00, $account1->fresh()->balance_twd);
    }
}