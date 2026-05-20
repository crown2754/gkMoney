<?php
// file: app/Observers/BankAccountObserver.php

namespace App\Observers;

use App\Models\BankAccount;
use App\Services\BankAccountService;

class BankAccountObserver
{
    /**
     * Handle the BankAccount "created" event.
     */
    public function created(BankAccount $bankAccount): void
    {
        app(BankAccountService::class)->recalculateTWD($bankAccount);
    }

    /**
     * Handle the BankAccount "updated" event.
     */
    public function updated(BankAccount $bankAccount): void
    {
        app(BankAccountService::class)->recalculateTWD($bankAccount);
    }

    /**
     * Handle the BankAccount "saved" event.
     */
    public function saved(BankAccount $bankAccount): void
    {
        app(BankAccountService::class)->recalculateTWD($bankAccount);
    }
}