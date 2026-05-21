<?php

namespace Tests\Livewire;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\BankAccount;
use App\Models\ExchangeRate;
use Livewire\Livewire;
use App\Livewire\BankAccountForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BankAccountFormTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Bank $bank;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Livewire User',
            'email' => 'lw@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->bank = Bank::create(['code' => '012', 'name' => 'Fubon', 'is_active' => true]);
        $this->currency = Currency::create(['code' => 'USD', 'name' => 'USD', 'is_active' => true]);
        
        ExchangeRate::create([
            'currency_id' => $this->currency->id,
            'rate_to_twd' => 30.000000,
            'date' => now()->toDateString(),
        ]);
    }

    public function test_form_validates_required_fields()
    {
        Livewire::actingAs($this->user)
            ->test(BankAccountForm::class)
            ->call('save')
            ->assertHasErrors([
                'bank_id' => 'required',
                'currency_id' => 'required',
                'alias' => 'required',
                'account_number' => 'required',
                'balance' => 'required',
            ]);
    }

    public function test_form_can_create_bank_account()
    {
        Livewire::actingAs($this->user)
            ->test(BankAccountForm::class)
            ->set('bank_id', $this->bank->id)
            ->set('currency_id', $this->currency->id)
            ->set('alias', 'My Fubon USD')
            ->set('account_number', '123-456789')
            ->set('balance', 250.00)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('bank-accounts.index'));

        $this->assertDatabaseHas('bank_accounts', [
            'user_id' => $this->user->id,
            'alias' => 'My Fubon USD',
            'balance' => 250.00,
            'balance_twd' => 7500.00,
        ]);
    }

    public function test_form_can_edit_existing_bank_account()
    {
        $account = BankAccount::create([
            'user_id' => $this->user->id,
            'bank_id' => $this->bank->id,
            'currency_id' => $this->currency->id,
            'alias' => 'Original Alias',
            'account_number' => '123',
            'balance' => 100.00,
            'balance_twd' => 3000.00,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->user)
            ->test(BankAccountForm::class, ['bankAccount' => $account])
            ->set('alias', 'Updated Livewire Alias')
            ->set('balance', 150.00)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('Updated Livewire Alias', $account->fresh()->alias);
        $this->assertEquals(4500.00, $account->fresh()->balance_twd);
    }
}