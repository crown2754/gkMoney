<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\BankAccount;
use App\Models\AssetSnapshot;
use App\Services\AssetSnapshotService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssetSnapshotServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AssetSnapshotService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AssetSnapshotService();
    }

    public function test_it_calculates_total_assets_and_creates_snapshot()
    {
        $user = User::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => bcrypt('password'),
        ]);

        $bank = Bank::create(['code' => '004', 'name' => 'Bank', 'is_active' => true]);
        $currency = Currency::create(['code' => 'USD', 'name' => 'USD', 'is_active' => true]);

        // 建立兩個帳戶
        BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => 'Account A',
            'account_number' => 'A123',
            'balance' => 100.00,
            'balance_twd' => 3000.00,
            'is_active' => true,
        ]);

        BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => 'Account B',
            'account_number' => 'B123',
            'balance' => 200.00,
            'balance_twd' => 6000.00,
            'is_active' => true,
        ]);

        // 執行快照
        $this->service->createDailySnapshot($user->id, now()->toDateString());

        $this->assertDatabaseHas('asset_snapshots', [
            'user_id' => $user->id,
            'total_twd' => 9000.00,
            'snapshot_date' => now()->toDateString(),
        ]);
    }

    public function test_it_updates_snapshot_if_already_exists_for_the_day()
    {
        $user = User::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => bcrypt('password'),
        ]);

        $date = now()->toDateString();

        // 先手動建立一個快照
        AssetSnapshot::create([
            'user_id' => $user->id,
            'total_twd' => 5000.00,
            'snapshot_date' => $date,
        ]);

        $bank = Bank::create(['code' => '004', 'name' => 'Bank', 'is_active' => true]);
        $currency = Currency::create(['code' => 'USD', 'name' => 'USD', 'is_active' => true]);

        BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => 'Account A',
            'account_number' => 'A123',
            'balance' => 100.00,
            'balance_twd' => 8000.00,
            'is_active' => true,
        ]);

        // 再次執行快照，應更新為 8000.00
        $this->service->createDailySnapshot($user->id, $date);

        $this->assertDatabaseHas('asset_snapshots', [
            'user_id' => $user->id,
            'total_twd' => 8000.00,
            'snapshot_date' => $date,
        ]);

        $this->assertEquals(1, AssetSnapshot::where('user_id', $user->id)->where('snapshot_date', $date)->count());
    }
}