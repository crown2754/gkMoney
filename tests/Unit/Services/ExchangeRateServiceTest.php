<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExchangeRateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ExchangeRateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExchangeRateService();
    }

    public function test_it_can_fetch_and_store_today_rates()
    {
        $currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'is_active' => true,
        ]);

        // 模擬外部匯率 API 回傳
        Http::fake([
            '*' => Http::response([
                'rates' => [
                    'USD' => 31.25
                ]
            ], 200)
        ]);

        $this->service->fetchTodayRates();

        $this->assertDatabaseHas('exchange_rates', [
            'currency_id' => $currency->id,
            'rate_to_twd' => 31.250000,
            'date' => now()->toDateString(),
        ]);
    }

    public function test_it_caches_exchange_rates()
    {
        $currency = Currency::create([
            'code' => 'USD', 
            'name' => 'USD', 
            'is_active' => true
        ]);
        
        ExchangeRate::create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
            'date' => now()->toDateString(),
        ]);

        // 測試是否成功寫入與讀取快取
        $rate = $this->service->getRate($currency->id, now()->toDateString());
        $this->assertEquals(30.000000, $rate);

        // 手動覆寫資料庫，若仍有快取，應回傳舊的值
        ExchangeRate::where('currency_id', $currency->id)->update(['rate_to_twd' => 35.00]);
        $cachedRate = $this->service->getRate($currency->id, now()->toDateString());
        $this->assertEquals(30.000000, $cachedRate);
    }

    public function test_it_can_manually_update_rate_and_clears_cache()
    {
        $currency = Currency::create([
            'code' => 'USD', 
            'name' => 'USD', 
            'is_active' => true
        ]);
        
        $date = now()->toDateString();
        
        // 建立快照並快取
        $this->service->updateRate($currency->id, 32.50, $date);

        $this->assertDatabaseHas('exchange_rates', [
            'currency_id' => $currency->id,
            'rate_to_twd' => 32.500000,
            'date' => $date,
        ]);

        // 更新匯率，測試快取是否已被清除並讀取到新值
        $this->service->updateRate($currency->id, 33.00, $date);
        $rate = $this->service->getRate($currency->id, $date);
        
        $this->assertEquals(33.000000, $rate);
    }
}