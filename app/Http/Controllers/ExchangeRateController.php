<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExchangeRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $exchangeRates = ExchangeRate::with('currency')
            ->orderBy('date', 'desc')
            ->orderBy('currency_id')
            ->get();

        return view('exchange-rates.index', compact('exchangeRates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $currencyCode)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rate_to_twd' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $currency = Currency::where('code', $currencyCode)->firstOrFail();

        ExchangeRate::updateOrCreate(
            [
                'currency_id' => $currency->id,
                'date' => $validated['date'],
            ],
            [
                'rate_to_twd' => $validated['rate_to_twd'],
            ]
        );

        return redirect()->route('asset.exchange-rates.index')
            ->with('success', "匯率 {$currencyCode} 已更新");
    }
}