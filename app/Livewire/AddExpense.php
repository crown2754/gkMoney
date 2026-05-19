app/Http/Livewire/BankAccountManagement.php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BankAccountManagement extends Component
{
    // Form state
    public $currency_code = '';
    public $balance = '';
    public $is_active = true;

    // Editing state
    public $editingId = null;

    // List of accounts
    public $accounts;

    protected $rules = [
        'currency_code' => ['required', 'string', 'size:3', Rule::in(['USD', 'EUR', 'JPY', 'GBP', 'AUD', 'CAD', 'CHF', 'CNY'])],
        'balance' => ['required', 'numeric'],
        'is_active' => ['boolean'],
    ];

    public function mount()
    {
        $this->loadAccounts();
    }

    public function loadAccounts()
    {
        $this->accounts = BankAccount::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.bank-account-management');
    }

    public function resetForm()
    {
        $this->currency_code = '';
        $this->balance = '';
        $this->is_active = true;
        $this->editingId = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('show-modal');
    }

    public function edit(BankAccount $account)
    {
        $this->editingId = $account->id;
        $this->currency_code = $account->currency_code;
        $this->balance = $account->balance;
        $this->is_active = $account->is_active;
        $this->dispatch('show-modal');
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $account = BankAccount::findOrFail($this->editingId);
            $this->authorize('update', $account);
            $account->update([
                'currency_code' => $this->currency_code,
                'balance' => $this->balance,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('toast', message='Account updated successfully.', type='success');
        } else {
            BankAccount::create([
                'user_id' => Auth::id(),
                'currency_code' => $this->currency_code,
                'balance' => $this->balance,
                'is_active' => $this->is_active,
            ]);
            $this->dispatch('toast', message='Account created successfully.', type='success');
        }

        $this->resetForm();
        $this->loadAccounts();
        $this->dispatch('hide-modal');
    }

    public function delete(BankAccount $account)
    {
        $this->authorize('delete', $account);
        $account->delete();
        $this->loadAccounts();
        $this->dispatch('toast', message='Account deleted.', type='success');
    }
}