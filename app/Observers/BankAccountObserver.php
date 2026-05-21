<?php

namespace App\Observers;

use App\Models\BankAccount;
use App\Services\BankAccountService;

class BankAccountObserver
{
    public function created(BankAccount $bankAccount)
    {
        BankAccountService::recalculateTWD($bankAccount);
    }

    public function updated(BankAccount $bankAccount)
    {
        BankAccountService::recalculateTWD($bankAccount);
    }

    public function saved(BankAccount $bankAccount)
    {
        BankAccountService::recalculateTWD($bankAccount);
    }
}