<?php

namespace App\Livewire;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ExchangeRateForm extends Component
{
    public $currencies;
    public $rates = [];

    protected $exchangeRateService;

    public function boot(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    public function mount()
    {
        $this->authorize('update', ExchangeRate::class);

        $this->currencies = Currency::where('is_active', true)->orderBy('code')->get();

        foreach ($this->currencies as $currency) {
            $rate = ExchangeRate::where('currency_id', $currency->id)
                ->whereDate('effective_date', today())
                ->first();
            $this->rates[$currency->id] = $rate ? $rate->rate : '';
        }
    }

    public function save()
    {
        $this->authorize('update', ExchangeRate::class);

        $rules = [];
        foreach ($this->currencies as $currency) {
            $rules["rates.{$currency}.id"] = 'nullable|numeric|min:0.0001';
        }
        $this->validate($rules);

        foreach ($this->currencies as $currency) {
            $value = $this->rates[$currency->id] ?? null;
            if ($value !== null && $value !== '') {
                $this->exchangeRateService->updateRate($currency, today(), $value);
            }
        }

        $this->dispatch('notify', type: 'success', message: '匯率已更新');
    }

    public function render()
    {
        return view('livewire.exchange-rate-form');
    }
}