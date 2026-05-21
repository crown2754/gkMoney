<?php

namespace Tests\Unit\Policies;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\BankAccount;
use App\Policies\BankAccountPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BankAccountPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_access_own_bank_account()
    {
        $userA = User::create([
            'name' => 'User A',
            'email' => 'a@example.com',
            'password' => bcrypt('password'),
        ]);

        $userB = User::create([
            'name' => 'User B',
            'email' => 'b@example.com',
            'password' => bcrypt('password'),
        ]);

        $bank = Bank::create(['code' => '004', 'name' => 'Bank', 'is_active' => true]);
        $currency = Currency::create(['code' => 'USD', 'name' => 'USD', 'is_active' => true]);

        $accountA = BankAccount::create([
            'user_id' => $userA->id,
            'bank_id' => $bank->id,
            'currency_id' => $currency->id,
            'alias' => 'Account A',
            'account_number' => 'A123',
            'balance' => 100.00,
            'balance_twd' => 3000.00,
            'is_active' => true,
        ]);

        $policy = new BankAccountPolicy();

        // User A 可以檢視/更新/刪除自己的帳戶
        $this->assertTrue($policy->view($userA, $accountA));
        $this->assertTrue($policy->update($userA, $accountA));
        $this->assertTrue($policy->delete($userA, $accountA));

        // User B 無法檢視/更新/刪除 User A 的帳戶
        $this->assertFalse($policy->view($userB, $accountA));
        $this->assertFalse($policy->update($userB, $accountA));
        $this->assertFalse($policy->delete($userB, $accountA));
    }
}