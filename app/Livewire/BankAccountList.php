<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BankAccount;
use App\Services\BankAccountService;

class BankAccountList extends Component
{
    protected $listeners = [
        'bankAccountSaved' => '$refresh',
    ];

    public function edit($accountId)
    {
        $this->dispatch('editBankAccount', $accountId);
    }

    public function delete($accountId, BankAccountService $service)
    {
        // 確保要刪除的帳戶確實屬於當前登入者
        $account = BankAccount::where('user_id', auth()->id())->findOrFail($accountId);
        
        $service->deleteAccount($account->id);

        session()->flash('list-message', '帳戶已成功刪除。');
        $this->dispatch('bankAccountSaved');
    }

    public function render()
    {
        $accounts = BankAccount::where('user_id', auth()->id())
            ->with(['bank', 'currency'])
            ->orderBy('id', 'desc')
            ->get();

        return view('livewire.bank-account-list', compact('accounts'));
    }
}