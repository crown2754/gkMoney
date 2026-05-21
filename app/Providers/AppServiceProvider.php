<?php
// file: app/Providers/AppServiceProvider.php

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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        BankAccount::observe(BankAccountObserver::class);
    }
}