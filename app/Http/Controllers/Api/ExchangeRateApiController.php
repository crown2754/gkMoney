<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class ExchangeRateApiController extends Controller
{
    /**
     * Display the specified exchange rate.
     */
    public function show(string $code): JsonResponse
    {
        $currency = Currency::where('code', $code)->first();

        if (!$currency) {
            return response()->json([
                'error' => 'Currency not found',
            ], 404);
        }

        $exchangeRate = ExchangeRate::where('currency_id', $currency->id)
            ->orderBy('date', 'desc')
            ->first();

        if (!$exchangeRate) {
            return response()->json([
                'error' => 'Exchange rate not found',
            ], 404);
        }

        return response()->json([
            'currency_code' => $currency->code,
            'currency_name' => $currency->name,
            'rate_to_twd' => $exchangeRate->rate_to_twd,
            'date' => $exchangeRate->date->format('Y-m-d'),
        ]);
    }
}