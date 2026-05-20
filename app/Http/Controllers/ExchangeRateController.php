<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExchangeRateController extends Controller
{
    public function index()
    {
        $rates = ExchangeRate::all();
        return view('exchange-rates.index', compact('rates'));
    }

    public function edit(ExchangeRate $exchangeRate)
    {
        return view('exchange-rates.edit', compact('exchangeRate'));
    }

    public function update(Request $request, ExchangeRate $exchangeRate)
    {
        $validated = $request->validate([
            'rate_to_twd' => 'required|numeric|min:0',
        ]);

        $exchangeRate->update($validated);

        return redirect()->route('asset.exchange-rates.index')->with('success', '匯率已更新');
    }

    public function apiShow($code)
    {
        $rate = ExchangeRate::where('code', $code)->firstOrFail();

        return response()->json([
            'code' => $rate->code,
            'rate_to_twd' => $rate->rate_to_twd,
            'fetched_at' => $rate->fetched_at,
        ]);
    }
}