<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\BankAccount;
use App\Models\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BankAccountCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected User $userB;
    protected Bank $bank;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::create([
            'name' => 'User A',
            'email' => 'a@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->userB = User::create([
            'name' => 'User B',
            'email' => 'b@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->bank = Bank::create(['code' => '013', 'name' => 'Cathay', 'is_active' => true]);
        $this->currency = Currency::create(['code' => 'USD', 'name' => 'USD', 'is_active' => true]);
        
        ExchangeRate::create([
            'currency_id' => $this->currency->id,
            'rate_to_twd' => 30.000000,
            'date' => now()->toDateString(),
        ]);
    }

    public function test_authenticated_user_can_create_bank_account()
    {
        $response = $this->actingAs($this->userA)
            ->post(route('bank-accounts.store'), [
                'bank_id' => $this->bank->id,
                'currency_id' => $this->currency->id,
                'alias' => 'My Cathay USD',
                'account_number' => '123-456',
                'balance' => 100.00,
                'is_active' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bank_accounts', [
            'user_id' => $this->userA->id,
            'alias' => 'My Cathay USD',
            'balance_twd' => 3000.00,
        ]);
    }

    public function test_user_cannot_access_or_modify_other_users_bank_account()
    {
        // 建立 User B 的帳戶
        $accountB = BankAccount::create([
            'user_id' => $this->userB->id,
            'bank_id' => $this->bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'B Account',
            'account_number' => '999',
            'balance' => 500.00,
            'balance_twd' => 15000.00,
            'is_active' => true,
        ]);

        // User A 嘗試修改 User B 的帳戶
        $response = $this->actingAs($this->userA)
            ->put(route('bank-accounts.update', $accountB->id), [
                'alias' => 'Hacked Alias',
                'balance' => 1000.00,
            ]);

        $response->assertStatus(403);
        $this->assertEquals('B Account', $accountB->fresh()->alias);

        // User A 嘗試刪除 User B 的帳戶
        $responseDelete = $this->actingAs($this->userA)
            ->delete(route('bank-accounts.destroy', $accountB->id));

        $responseDelete->assertStatus(403);
        $this->assertDatabaseHas('bank_accounts', ['id' => $accountB->id]);
    }
}