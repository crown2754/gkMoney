<?php

namespace App\Livewire;

use App\Models\BankAccount;
use App\Services\BankAccountService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BankAccountList extends Component
{
    protected $bankAccountService;

    protected $listeners = [
        'bankAccountSaved' => '$refresh',
    ];

    public function boot(BankAccountService $bankAccountService)
    {
        $this->bankAccountService = $bankAccountService;
    }

    public function edit(BankAccount $account)
    {
        $this->dispatch('editBankAccount', account: $account);
    }

    public function delete(BankAccount $account)
    {
        $this->authorize('delete', $account);
        $this->bankAccountService->delete($account);
        $this->dispatch('bankAccountSaved');
        $this->dispatch('notify', type: 'success', message: '帳戶已刪除');
    }

    public function render()
    {
        $accounts = BankAccount::with(['bank', 'currency'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.bank-account-list', compact('accounts'));
    }
}