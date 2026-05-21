<?php

namespace Tests\Unit\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExchangeRateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ExchangeRateService::class);
    }

    /** @test */
    public function 可以抓取今日匯率()
    {
        // Arrange
        $currency = Currency::factory()->create(['code' => 'USD']);

        // Act
        $rates = $this->service->fetchTodayRates();

        // Assert
        $this->assertIsArray($rates);
        $this->assertArrayHasKey('USD', $rates);
    }

    /** @test */
    public function 可以手動更新匯率()
    {
        // Arrange
        $currency = Currency::factory()->create(['code' => 'USD']);

        // Act
        $exchangeRate = $this->service->updateRate($currency, 30.500000);

        // Assert
        $this->assertInstanceOf(ExchangeRate::class, $exchangeRate);
        $this->assertEquals(30.500000, $exchangeRate->rate_to_twd);
        $this->assertEquals(now()->toDateString(), $exchangeRate->date);
    }

    /** @test */
    public function 可以取得最新匯率()
    {
        // Arrange
        $currency = Currency::factory()->create(['code' => 'USD']);
        ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
            'date' => now()->subDay(),
        ]);
        ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 31.000000,
            'date' => now(),
        ]);

        // Act
        $rate = $this->service->getLatestRate($currency);

        // Assert
        $this->assertEquals(31.000000, $rate);
    }

    /** @test */
    public function 匯率_API_失敗時保留前一日匯率()
    {
        // Arrange
        $currency = Currency::factory()->create(['code' => 'USD']);
        ExchangeRate::factory()->create([
            'currency_id' => $currency->id,
            'rate_to_twd' => 30.000000,
            'date' => now()->subDay(),
        ]);

        // Act - 模擬 API 失敗
        $this->mock(ExchangeRateService::class, function ($mock) {
            $mock->shouldReceive('fetchTodayRates')
                ->andThrow(new \Exception('API 失敗'));
        });

        // Assert
        $rate = $this->service->getLatestRate($currency);
        $this->assertEquals(30.000000, $rate);
    }

    /** @test */
    public function 可以快取匯率()
    {
        // Arrange
        $currency = Currency::factory()->create(['code' => 'USD']);

        // Act
        $this->service->cacheRate($currency, 30.000000);

        // Assert
        $cachedRate = $this->service->getCachedRate($currency);
        $this->assertEquals(30.000000, $cachedRate);
    }
}