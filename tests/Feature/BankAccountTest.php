<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\BankAccount;
use App\Models\AssetSnapshot;
use App\Services\BankAccountService;
use App\Services\ExchangeRateService;
use App\Services\AssetSnapshotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 帳戶_CRUD_功能正常()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        $exchangeRate = ExchangeRate::factory()->create();

        // Act & Assert - 新增帳戶
        $response = $this->actingAs($user)
            ->post(route('bank-accounts.store'), [
                'bank_id' => $bank->id,
                'currency_id' => $currency->id,
                'alias' => '我的帳戶',
                'account_number' => '1234567890',
                'balance' => 1000,
            ]);

        $response->assertRedirect(route('bank-accounts.index'));
        $this->assertDatabaseHas('bank_accounts', [
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '我的帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // Assert - 讀取帳戶列表
        $response = $this->actingAs($user)
            ->get(route('bank-accounts.index'));
        $response->assertStatus(200);

        // Assert - 更新帳戶
        $account = BankAccount::first();
        $response = $this->actingAs($user)
            ->put(route('bank-accounts.update', $account->id), [
                'alias' => '更新後的帳戶',
            ]);
        $response->assertRedirect(route('bank-accounts.index');

        // Assert - 刪除帳戶
        $response = $this->actingAs($user)
            ->delete(route('bank-accounts.destroy', $account->id));
        $response->assertRedirect(route('bank-accounts.index'));
    }

    /** @test */
    public function balance_twd_自動計算正確()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        $exchangeRate = ExchangeRate::factory()->create([
            'rate_to_twd' => 30.000000,
        ]);

        // Act
        $account = BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '測試帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // Assert
        $this->assertEquals(30000.00, $account->balance_twd);
    }

    /** @test */
    public function 首頁儀表板顯示總資產正確()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        $exchangeRate = ExchangeRate::factory()->create([
            'rate_to_twd' => 30.000000,
        ]);

        // Act
        BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '測試帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // Assert
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('totalAssets', 30000.00);
    }

    /** @test */
    public function 匯率更新觸發重新計算()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        $exchangeRate = ExchangeRate::factory()->create([
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
        $account->refresh();

        // Assert
        $this->assertEquals(31000.00, $account->balance_twd);
    }

    /** @test */
    public function 權限隔離有效_使用者_A_無法存取使用者_B_的帳戶()
    {
        // Arrange
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        $exchangeRate = ExchangeRate::factory()->create();

        $account = BankAccount::create([
            'user_id' => $userB->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '使用者 B 的帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // Act & Assert
        $response = $this->actingAs($userA)
            ->get(route('bank-accounts.show', $account->id));
        $response->assertStatus(403);

        $response = $this->actingAs($userA)
            ->put(route('bank-accounts.update', $account->id), [
                'alias' => '竄改後的帳戶',
            ]);
        $response->assertStatus(403);

        $response = $this->actingAs($userA)
            ->delete(route('bank-accounts.destroy', $account->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function 銀行與幣別下拉選單正常顯示()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create(['name' => '測試銀行']);
        $currency = Currency::factory()->create(['name' => '測試幣別']);

        // Act
        $response = $this->actingAs($user)
            ->get(route('bank-accounts.create'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('測試銀行');
        $response->assertSee('測試幣別');
    }

    /** @test */
    public function 金額格式統一_千分位逗號_兩位小數()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        $exchangeRate = ExchangeRate::factory()->create([
            'rate_to_twd' => 30.000000,
        ]);

        BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '測試帳戶',
            'account_number' => '1234567890',
            'balance' => 1234567.89,
        ]);

        // Act
        $response = $this->actingAs($user)
            ->get(route('bank-accounts.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('1,234,567.89');
        $response->assertSee('37,037,036.70');
    }

    /** @test */
    public function 匯率_API_失敗時保留前一日匯率()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        $exchangeRate = ExchangeRate::factory()->create([
            'rate_to_twd' => 30.000000,
            'date' => now()->subDay(),
        ]);

        $account = BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '測試帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // Act - 模擬 API 失敗
        $this->mock(ExchangeRateService::class, function ($mock) {
            $mock->shouldReceive('fetchTodayRates')
                ->andThrow(new \Exception('API 失敗'));
        });

        // 執行排程
        $this->artisan('exchange:fetch-today-rates')->assertExitCode(0);

        // Assert
        $account->refresh();
        $this->assertEquals(30000.00, $account->balance_twd);
    }
}