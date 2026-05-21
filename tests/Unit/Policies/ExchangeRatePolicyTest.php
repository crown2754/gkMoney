<?php

namespace Tests\Unit\Policies;

use Tests\TestCase;
use App\Models\User;
use App\Policies\ExchangeRatePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExchangeRatePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_manage_exchange_rates()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        $policy = new ExchangeRatePolicy();

        // 管理員權限驗證
        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin));

        // 一般用戶權限驗證
        $this->assertFalse($policy->viewAny($regularUser));
        $this->assertFalse($policy->create($regularUser));
        $this->assertFalse($policy->update($regularUser));
    }
}