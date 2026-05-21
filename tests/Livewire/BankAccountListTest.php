<?php

namespace Tests\Livewire;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\BankAccount;
use Livewire\Livewire;
use App\Livewire\BankAccountList;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BankAccountListTest extends TestCase
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

        $this->bank = Bank::create(['code' => '004', 'name' => 'Bank', 'is_active' => true]);
        $this->currency = Currency::create(['code' => 'USD', 'name' => 'USD', 'is_active' => true]);
    }

    public function test_it_lists_only_logged_in_users_accounts()
    {
        BankAccount::create([
            'user_id' => $this->userA->id,
            'bank_id' => $this->bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'A Account',
            'account_number' => 'A111',
            'balance' => 100.00,
            'is_active' => true,
        ]);

        BankAccount::create([
            'user_id' => $this->userB->id,
            'bank_id' => $this->bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'B Account',
            'account_number' => 'B222',
            'balance' => 200.00,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->userA)
            ->test(BankAccountList::class)
            ->assertSee('A Account')
            ->assertDontSee('B Account');
    }

    public function test_user_can_delete_own_bank_account()
    {
        $account = BankAccount::create([
            'user_id' => $this->userA->id,
            'bank_id' => $this->bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'Disposable Account',
            'account_number' => 'A111',
            'balance' => 100.00,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->userA)
            ->test(BankAccountList::class)
            ->call('deleteAccount', $account->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('bank_accounts', ['id' => $account->id]);
    }
}