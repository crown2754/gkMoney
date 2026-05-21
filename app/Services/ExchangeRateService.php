<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    private const CACHE_KEY = 'exchange_rates';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * 從臺灣銀行 API 抓取匯率，快取 1 小時，失敗時保留前一日匯率並記錄警告 log
     */
    public function fetchTodayRates(): void
    {
        $rates = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            try {
                $response = Http::timeout(30)->get('https://rate.bot.com.tw/xrt/flate/USD,GBP,EUR,JPY,AUD,CAD,CHF,CNY,HKD,KRW,SGD,NZD,ZAR,MXN,SEK,DKK,PLN,TRY,THB,PHP,MYR,HUF,ILS,CZK,BRL,INR,IDR,RUB,TWD,MXN.json');

                if ($response->successful()) {
                    $data = $response->json();
                    $rates = [];

                    if (isset($data['currencies'])) {
                        foreach ($data['currencies'] as $currency) {
                            $rates[$currency['code']] = $currency['rate'];
                        }
                    }

                    return $rates;
                }
            } catch (\Exception $e) {
                Log::warning('ExchangeRateService: 抓取匯率失敗，使用前一日匯率', [
                    'error' => $e->getMessage(),
                ]);
            }

            return $this->getPreviousDayRates();
        });

        // 更新匯率
        foreach ($rates as $currencyCode => $rate) {
            $this->updateRate($currencyCode, $rate);
        }
    }

    /**
     * 取得指定幣別匯率
     */
    public function getRate(string $currencyCode, ?Carbon $date = null): ?float
    {
        $date = $date ?? Carbon::today();

        $rate = ExchangeRate::where('currency_id', $currencyCode)
            ->whereDate('date', $date)
            ->first();

        return $rate ? $rate->rate_to_twd : null;
    }

    /**
     * 手動更新匯率，觸發該幣別所有帳戶重新計算 `balance_twd`
     */
    public function updateRate(string $currencyCode, float $newRate): void
    {
        $currency = Currency::where('code', $currencyCode)->first();

        if (!$currency) {
            throw new \InvalidArgumentException("Currency [{$currencyCode}] not found");
        }

        ExchangeRate::updateOrCreate(
            ['currency_id' => $currency->id, 'date' => Carbon::today()],
            ['rate_to_twd' => $newRate]
        );

        // 觸發該幣別所有帳戶重新計算 `balance_twd`
        $bankAccountService = app(BankAccountService::class);
        $bankAccountService->recalculateAllByCurrency($currencyCode);
    }

    /**
     * 取得前一日匯率
     */
    private function getPreviousDayRates(): array
    {
        $yesterday = Carbon::yesterday();

        $rates = ExchangeRate::whereDate('date', $yesterday)->get();

        $result = [];
        foreach ($rates as $rate) {
            $result[$rate->currency_id] = $rate->rate_to_twd;
        }

        return $result;
    }
}