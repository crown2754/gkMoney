<?php

namespace App\Providers;

use App\Models\BankAccount;
use App\Models\ExchangeRate;
use App\Policies\BankAccountPolicy;
use App\Policies\ExchangeRatePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        BankAccount::class => BankAccountPolicy::class,
        ExchangeRate::class => ExchangeRatePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function (User $user) {
            if ($user->is_admin) {
                return true;
            }
        });
    }
}