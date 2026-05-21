<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\BankAccount;
use App\Services\BankAccountService;
use App\Services\ExchangeRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankAccountServiceTest extends TestCase
{
    use RefreshDatabase;

    private BankAccountService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(BankAccountService::class);
    }

    /** @test */
    public function 可以新增帳戶()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
        ]);

        // Act
        $account = $this->service->createAccount($user, [
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '測試帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // Assert
        $this->assertInstanceOf(BankAccount::class, $account);
        $this->assertEquals('測試帳戶', $account->alias);
        $this->assertEquals(30000.00, $account->balance_twd);
    }

    /** @test */
    public function 可以更新帳戶()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
        ]);

        $account = BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '原名稱',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // Act
        $updated = $this->service->updateAccount($account, [
            'alias' => '新名稱',
            'balance' => 2000,
        ]);

        // Assert
        $this->assertEquals('新名稱', $updated->alias);
        $this->assertEquals(2000, $updated->balance);
        $this->assertEquals(60000.00, $updated->balance_twd);
    }

    /** @test */
    public function 可以刪除帳戶()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
        ]);

        $account = BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '測試帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // Act
        $result = $this->service->deleteAccount($account);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('bank_accounts', ['id' => $account->id]);
    }

    /** @test */
    public function 可以取得使用者所有帳戶()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
        ]);

        BankAccount::factory()->count(3)->create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
        ]);

        // Act
        $accounts = $this->service->getUserAccounts($user);

        // Assert
        $this->assertCount(3, $accounts);
    }

    /** @test */
    public function 重新計算_balance_twd()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        $exchangeRate = ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
        ]);

        $account = BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '測試帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // Act
        $exchangeRate->update(['rate_to_twd' => 31.000000]);
        $this->service->recalculateBalanceTwd($account);

        // Assert
        $account->refresh();
        $this->assertEquals(31000.00, $account->balance_twd);
    }
}