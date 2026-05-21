<?php

namespace App\Providers;

use App\Models\BankAccount;
use App\Observers\BankAccountObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        BankAccount::observe(BankAccountObserver::class);
    }
}