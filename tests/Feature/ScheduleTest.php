<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\BankAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_exchange_fetch_today_rates_command_runs_successfully()
    {
        $currency = Currency::create(['code' => 'USD', 'name' => 'USD', 'is_active' => true]);

        Http::fake([
            '*' => Http::response([
                'rates' => [
                    'USD' => 31.10
                ]
            ], 200)
        ]);

        $exitCode = Artisan::call('exchange:fetch-today-rates');

        $this->assertEquals(0, $exitCode);
        $this->assertDatabaseHas('exchange_rates', [
            'currency_id' => $currency->id,
            'rate_to_twd' => 31.100000,
            'date' => now()->toDateString(),
        ]);
    }

    public function test_asset_snapshot_command_runs_successfully()
    {
        $user = User::create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => bcrypt('password'),
        ]);

        $bank = Bank::create(['code' => '004', 'name' => 'Bank', 'is_active' => true]);
        $currency = Currency::create(['code' => 'USD', 'name' => 'USD', 'is_active' => true]);

        BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => 'Bob Wallet',
            'account_number' => 'B123',
            'balance' => 100.00,
            'balance_twd' => 3100.00,
            'is_active' => true,
        ]);

        $exitCode = Artisan::call('asset:snapshot');

        $this->assertEquals(0, $exitCode);
        $this->assertDatabaseHas('asset_snapshots', [
            'user_id' => $user->id,
            'total_twd' => 3100.00,
            'snapshot_date' => now()->toDateString(),
        ]);
    }
}