<?php

namespace Tests\Livewire;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\BankAccount;
use App\Models\AssetSnapshot;
use Livewire\Livewire;
use App\Livewire\DashboardAsset;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardAssetTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Bank $bank;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Dashboard User',
            'email' => 'dash@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->bank = Bank::create(['code' => '004', 'name' => 'Bank', 'is_active' => true]);
        $this->currency = Currency::create(['code' => 'TWD', 'name' => 'TWD', 'is_active' => true]);
    }

    public function test_it_displays_total_asset_and_percentage_change()
    {
        // 1. 今日合計餘額：10,000 元
        BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'Account 1',
            'account_number' => '111',
            'balance' => 10000.00,
            'balance_twd' => 10000.00,
            'is_active' => true,
        ]);

        // 2. 昨日總資產：8,000 元 (成長 25%)
        AssetSnapshot::create([
            'user_id' => $this->user->id,
            'total_twd' => 8000.00,
            'snapshot_date' => now()->subDay()->toDateString(),
        ]);

        Livewire::actingAs($this->user)
            ->test(DashboardAsset::class)
            ->assertSee('10,000') // 應該顯示格式化後的今日總資產
            ->assertSee('25%');  // 應該顯示變動百分比 ((10000 - 8000) / 8000 * 100% = 25%)
    }

    public function test_it_handles_no_yesterday_snapshot()
    {
        BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'Account 1',
            'account_number' => '111',
            'balance' => 5000.00,
            'balance_twd' => 5000.00,
            'is_active' => true,
        ]);

        // 沒有昨日快照時，應正常顯示資產且不拋出錯誤
        Livewire::actingAs($this->user)
            ->test(DashboardAsset::class)
            ->assertSee('5,000');
    }
}