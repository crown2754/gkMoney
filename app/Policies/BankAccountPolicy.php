<?php

namespace App\Policies;

use App\Models\BankAccount;
use App\Models\User;

class BankAccountPolicy
{
    /**
     * `update` 方法檢查 `$user->id === $bankAccount->user_id`
     */
    public function update(User $user, BankAccount $bankAccount): bool
    {
        return $user->id === $bankAccount->user_id;
    }

    /**
     * `delete` 方法檢查 `$user->id === $bankAccount->user_id`
     */
    public function delete(User $user, BankAccount $bankAccount): bool
    {
        return $user->id === $bankAccount->user_id;
    }
}