<?php

namespace App\Providers;

use App\Models\BankAccount;
use App\Models\ExchangeRate;
use App\Policies\BankAccountPolicy;
use App\Policies\ExchangeRatePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        BankAccount::class => BankAccountPolicy::class,
        ExchangeRate::class => ExchangeRatePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}