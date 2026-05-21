<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\BankAccount;
use App\Models\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExchangeRateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $this->user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        $this->currency = Currency::create(['code' => 'JPY', 'name' => 'Japanese Yen', 'is_active' => true]);
        
        ExchangeRate::create([
            'currency_id' => $this->currency->id,
            'rate_to_twd' => 0.220000,
            'date' => now()->toDateString(),
        ]);
    }

    public function test_non_admin_cannot_update_exchange_rate()
    {
        $response = $this->actingAs($this->user)
            ->post(route('exchange-rates.update'), [
                'currency_id' => $this->currency->id,
                'rate_to_twd' => 0.250000,
                'date' => now()->toDateString(),
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_exchange_rate_and_triggers_recalculation()
    {
        $bank = Bank::create(['code' => '005', 'name' => 'Land Bank', 'is_active' => true]);

        // 建立外幣帳戶
        $account = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'JPY Wallet',
            'account_number' => '123',
            'balance' => 10000.00, // 10000 * 0.22 = 2200
            'is_active' => true,
        ]);

        $this->assertEquals(2200.00, $account->fresh()->balance_twd);

        // 管理員更新匯率
        $response = $this->actingAs($this->admin)
            ->post(route('exchange-rates.update'), [
                'currency_id' => $this->currency->id,
                'rate_to_twd' => 0.250000,
                'date' => now()->toDateString(),
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('exchange_rates', [
            'currency_id' => $this->currency->id,
            'rate_to_twd' => 0.250000,
        ]);

        // 驗證台幣餘額自動更新 (10000 * 0.25 = 2500)
        $this->assertEquals(2500.00, $account->fresh()->balance_twd);
    }
}