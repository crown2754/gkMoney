<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Currency;
use App\Services\ExchangeRateService;

class ExchangeRateForm extends Component
{
    public $currencyCode = '';
    public $rate = '';

    protected $rules = [
        'currencyCode' => 'required|string|exists:currencies,code',
        'rate' => 'required|numeric|min:0.000001',
    ];

    protected $validationAttributes = [
        'currencyCode' => '外幣幣別',
        'rate' => '兌台幣匯率',
    ];

    public function updateRate(ExchangeRateService $service)
    {
        // 檢查是否為管理員，非管理員拋出 403 異常
        if (auth()->user() && method_exists(auth()->user(), 'isAdmin') && !auth()->user()->isAdmin()) {
            abort(403, '只有管理員可以更新匯率。');
        }

        $this->validate();

        $service->updateRate($this->currencyCode, (float) $this->rate);

        session()->flash('rate-message', '匯率已成功更新！');
        $this->dispatch('bankAccountSaved'); // 觸發全站資產與清單重算
        $this->reset(['currencyCode', 'rate']);
    }

    public function render()
    {
        // 僅提供非 TWD 幣別進行匯率管理
        $currencies = Currency::where('is_active', true)
            ->where('code', '!=', 'TWD')
            ->orderBy('code')
            ->get();

        return view('livewire.exchange-rate-form', compact('currencies'));
    }
}