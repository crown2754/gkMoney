<?php

namespace App\Policies;

use App\Models\User;

class ExchangeRatePolicy
{
    /**
     * `update` 方法檢查 `$user->is_admin === true`
     */
    public function update(User $user): bool
    {
        return $user->is_admin === true;
    }
}