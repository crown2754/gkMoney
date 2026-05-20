<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Currency;
use App\Services\BankAccountService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BankAccountForm extends Component
{
    public $bankAccountId = null;
    public $bankId;
    public $currencyId;
    public $alias;
    public $accountNumber;
    public $balance = 0;

    public $banks;
    public $currencies;

    protected $bankAccountService;

    protected function rules()
    {
        return [
            'bankId'       => 'required|exists:banks,id',
            'currencyId'    => 'required|exists:currencies,id',
            'alias'         => 'required|string|max:100',
            'accountNumber'  => 'required|string|max:50',
            'balance'        => 'required|numeric',
        ];
    }

    public function boot(BankAccountService $bankAccountService)
    {
        $this->bankAccountService = $bankAccountService;
    }

    public function mount()
    {
        $this->banks      = Bank::where('is_active', true)->orderBy('name')->get();
        $this->currencies = Currency::where('is_active', true)->orderBy('code')->get();
    }

    public function edit(BankAccount $account)
    {
        $this->bankAccountId = $account->id;
        $this->bankId        = $account->bank_id;
        $this->currencyId    = $account->currency_id;
        $this->alias         = $account->alias;
        $this->accountNumber = $account->account_number;
        $this->balance       = $account->balance;
    }

    public function save()
    {
        $this->authorize('create', BankAccount::class);

        $validated = $this->validate();

        $data = [
            'bank_id'        => $validated['bankId'],
            'currency_id'    => $validated['currencyId'],
            'alias'          => $validated['alias'],
            'account_number' => $validated['accountNumber'],
            'balance'        => $validated['balance'],
        ];

        if ($this->bankAccountId) {
            $account = BankAccount::findOrFail($this->bankAccountId);
            $this->authorize('update', $account);
            $this->bankAccountService->update($account, $data);
        } else {
            $this->bankAccountService->create(Auth::user(), $data);
        }

        $this->reset(['bankAccountId', 'bankId', 'currencyId', 'alias', 'accountNumber', 'balance']);
        $this->dispatch('bankAccountSaved');
        $this->dispatch('notify', type: 'success', message: '帳戶已儲存');
    }

    public function cancel()
    {
        $this->reset(['bankAccountId', 'bankId', 'currencyId', 'alias', 'accountNumber', 'balance']);
    }

    public function render()
    {
        return view('livewire.bank-account-form');
    }
}