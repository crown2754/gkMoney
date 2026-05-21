<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\BankAccount;
use App\Models\AssetSnapshot;
use App\Services\AssetSnapshotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetSnapshotServiceTest extends TestCase
{
    use RefreshDatabase;

    private AssetSnapshotService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AssetSnapshotService::class);
    }

    /** @test */
    public function 可以產生每日快照()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
        ]);

        BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '測試帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // Act
        $snapshot = $this->service->createDailySnapshot($user);

        // Assert
        $this->assertInstanceOf(AssetSnapshot::class, $snapshot);
        $this->assertEquals(30000.00, $snapshot->total_twd);
        $this->assertEquals(now()->toDateString(), $snapshot->snapshot_date);
    }

    /** @test */
    public function 可以取得最新快照()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
        ]);

        BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '測試帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        $this->service->createDailySnapshot($user);

        // Act
        $latestSnapshot = $this->service->getLatestSnapshot($user);

        // Assert
        $this->assertInstanceOf(AssetSnapshot::class, $latestSnapshot);
        $this->assertEquals(30000.00, $latestSnapshot->total_twd);
    }

    /** @test */
    public function 可以取得歷史快照()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
        ]);

        BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '測試帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // 產生多日快照
        $this->service->createDailySnapshot($user);
        $this->service->createDailySnapshot($user);

        // Act
        $history = $this->service->getSnapshotHistory($user, 30);

        // Assert
        $this->assertGreaterThanOrEqual(1, $history->count());
    }

    /** @test */
    public function 快照日期正確()
    {
        // Arrange
        $user = User::factory()->create();
        $bank = Bank::factory()->create();
        $currency = Currency::factory()->create();
        ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
        ]);

        BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => '測試帳戶',
            'account_number' => '1234567890',
            'balance' => 1000,
        ]);

        // Act
        $snapshot = $this->service->createDailySnapshot($user);

        // Assert
        $this->assertEquals(now()->toDateString(), $snapshot->snapshot_date);
    }
}