<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\BankAccount;
use App\Services\BankAccountService;

class BankAccountForm extends Component
{
    public $accountId = null;
    public $bankId = '';
    public $currencyId = '';
    public $alias = '';
    public $accountNumber = '';
    public $balance = 0.00;

    protected $listeners = [
        'editBankAccount' => 'loadAccount',
        'resetBankAccountForm' => 'resetFields'
    ];

    protected function rules()
    {
        return [
            'bankId' => 'required|exists:banks,id',
            'currencyId' => 'required|exists:currencies,id',
            'alias' => 'required|string|max:100',
            'accountNumber' => 'required|string|max:50',
            'balance' => 'required|numeric',
        ];
    }

    protected $validationAttributes = [
        'bankId' => '銀行',
        'currencyId' => '幣別',
        'alias' => '帳戶別名',
        'accountNumber' => '帳號',
        'balance' => '帳戶餘額',
    ];

    public function mount()
    {
        $this->resetFields();
    }

    public function loadAccount($id)
    {
        $account = BankAccount::where('user_id', auth()->id())->findOrFail($id);
        $this->accountId = $account->id;
        $this->bankId = $account->bank_id;
        $this->currencyId = $account->currency_id;
        $this->alias = $account->alias;
        $this->accountNumber = $account->account_number;
        $this->balance = $account->balance;
    }

    public function resetFields()
    {
        $this->accountId = null;
        $this->bankId = '';
        $this->currencyId = '';
        $this->alias = '';
        $this->accountNumber = '';
        $this->balance = 0.00;
        $this->resetErrorBag();
    }

    public function save(BankAccountService $service)
    {
        $validated = $this->validate();

        $data = [
            'bank_id' => $this->bankId,
            'currency_id' => $this->currencyId,
            'alias' => $this->alias,
            'account_number' => $this->accountNumber,
            'balance' => $this->balance,
        ];

        if ($this->accountId) {
            // 確保要編輯的帳戶確實屬於當前登入者
            $account = BankAccount::where('user_id', auth()->id())->findOrFail($this->accountId);
            $service->updateAccount($account->id, $data);
            session()->flash('message', '銀行帳戶已成功更新！');
        } else {
            $data['user_id'] = auth()->id();
            $service->createAccount($data);
            session()->flash('message', '銀行帳戶已成功建立！');
        }

        $this->dispatch('bankAccountSaved');
        $this->resetFields();
    }

    public function render()
    {
        $banks = Bank::where('is_active', true)->orderBy('name')->get();
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('livewire.bank-account-form', compact('banks', 'currencies'));
    }
}