<?php
// file: app/Services/ExchangeRateService.php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    private const CACHE_KEY = 'exchange_rates_today';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Fetch today's exchange rates from Taiwan Bank API.
     * Cache for 1 hour. On failure, log warning and keep previous day's rates.
     */
    public function fetchTodayRates(): void
    {
        $cached = Cache::get(self::CACHE_KEY);
        if ($cached) {
            return;
        }

        try {
            $response = Http::timeout(10)->get('https://rate.bot.com.tw/xrt/flcsv/0/day');
            if ($response->successful()) {
                $rates = $this->parseCsvRates($response->body());
                foreach ($rates as $currencyCode => $rate) {
                    ExchangeRate::updateOrCreate(
                        ['currency_code' => $currencyCode],
                        ['rate' => $rate, 'date' => now()->toDateString()]
                    );
                }
                Cache::put(self::CACHE_KEY, true, self::CACHE_TTL);
            } else {
                Log::warning('Failed to fetch exchange rates from Taiwan Bank API', [
                    'status' => $response->status(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Exception while fetching exchange rates: ' . $e->getMessage());
        }
    }

    /**
     * Get the exchange rate for a specific currency code.
     */
    public function getRate(string $currencyCode): ?float
    {
        $rate = ExchangeRate::where('currency_code', $currencyCode)->first();
        return $rate ? (float) $rate->rate : null;
    }

    /**
     * Manually update a specific currency's rate and trigger recalculation of all accounts in that currency.
     */
    public function updateRate(string $currencyCode, float $rate): void
    {
        ExchangeRate::updateOrCreate(
            ['currency_code' => $currencyCode],
            ['rate' => $rate, 'date' => now()->toDateString()]
        );

        // Invalidate cache
        Cache::forget(self::CACHE_KEY);

        // Trigger recalculation for all accounts in this currency
        $currency = \App\Models\Currency::where('code', $currencyCode)->first();
        if ($currency) {
            app(BankAccountService::class)->recalculateAllByCurrency($currency->id);
        }
    }

    /**
     * Parse CSV content from Taiwan Bank API response.
     */
    private function parseCsvRates(string $csvContent): array
    {
        $rates = [];
        $lines = explode("\n", $csvContent);
        foreach ($lines as $line) {
            $columns = str_getcsv($line);
            if (count($columns) >= 3 && !empty($columns[0]) && is_numeric($columns[2])) {
                $currencyCode = trim($columns[0]);
                $rate = (float) $columns[2];
                $rates[$currencyCode] = $rate;
            }
        }
        return $rates;
    }
}