tests/Feature/BankAccountManagementTest.php
<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\ExchangeRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_is_redirected_to_login()
    {
        $this->get('/bank-account-management')
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_sees_empty_list()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/bank-account-management')
            ->assertOk()
            ->assertSee('尚未有任何帳戶');
    }

    /** @test */
    public function user_can_create_a_bank_account()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Provide today's exchange rate for USD
        ExchangeRate::factory()->create([
            'currency_code' => 'USD',
            'rate_to_twd' => 30.5,
            'date' => now()->toDateString(),
        ]);

        $this->post('/bank-account-management', [
            'currency_code' => 'USD',
            'balance' => '100.00',
            'is_active' => '1',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/bank-account-management');

        $this->assertDatabaseHas('bank_accounts', [
            'user_id' => $user->id,
            'currency_code' => 'USD',
            'balance' => 100.00,
            'is_active' => true,
        ]);

        $account = BankAccount::first();
        $this->assertEquals(3050.00, $account->balance_twd); // 100 * 30.5

        $this->get('/bank-account-management')
            ->assertSee('USD 帳戶')
            ->assertSee('100.00')
            ->assertSee('3,050.00 TWD');
    }

    /** @test */
    public function user_can_edit_an_existing_bank_account()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        ExchangeRate::factory()->create([
            'currency_code' => 'EUR',
            'rate_to_twd' => 33.0,
            'date' => now()->toDateString(),
        ]);

        $account = BankAccount::factory()->create([
            'user_id' => $user->id,
            'currency_code' => 'EUR',
            'balance' => '50.00',
            'is_active' => true,
        ]);

        $this->patch("/bank-account-management/{$account->id}", [
            'currency_code' => 'EUR',
            'balance' => '75.00',
            'is_active' => '0',
        ])
            ->assertRedirect('/bank-account-management');

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $account->id,
            'balance' => 75.00,
            'is_active' => false,
        ]);

        $account->refresh();
        $this->assertEquals(2475.00, $account->balance_twd); // 75 * 33

        $this->get('/bank-account-management')
            ->assertSee('75.00')
            ->assertSee('2,475.00 TWD')
            ->assertSee('停用');
    }

    /** @test */
    public function user_can_delete_a_bank_account()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $account = BankAccount::factory()->create([
            'user_id' => $user->id,
            'currency_code' => 'JPY',
            'balance' => '1000',
            'is_active' => true,
        ]);

        $this->delete("/bank-account-management/{$account->id}")
            ->assertRedirect('/bank-account-management');

        $this->assertDatabaseMissing('bank_accounts', [
            'id' => $account->id,
        ]);

        $this->get('/bank-account-management')
            ->assertSee('尚未有任何帳戶');
    }

    /** @test */
    public function user_cannot_edit_another_users_account()
    {
        $owner = User::factory()->create();
        $ intruder = User::factory()->create();

        $this->actingAs($intruder);

        ExchangeRate::factory()->create([
            'currency_code' => 'GBP',
            'rate_to_twd' => 38.0,
            'date' => now()->toDateString(),
        ]);

        $account = BankAccount::factory()->create([
            'user_id' => $owner->id,
            'currency_code' => 'GBP',
            'balance' => '100.00',
        ]);

        $response = $this->patch("/bank-account-management/{$account->id}", [
            'currency_code' => 'GBP',
            'balance' => '200.00',
            'is_active' => '1',
        ]);

        // Assuming a 403 is returned for unauthorized attempts
        $response->assertStatus(403);

        // Ensure the account remains unchanged
        $this->assertDatabaseHas('bank_accounts', [
            'id' => $account->id,
            'balance' => 100.00,
        ]);
    }

    /** @test */
    public function user_cannot_delete_another_users_account()
    {
        $owner = User::factory()->create();
        $ intruder = User::factory()->create();

        $this->actingAs($intruder);

        $account = BankAccount::factory()->create([
            'user_id' => $owner->id,
            'currency_code' => 'CAD',
            'balance' => '50.00',
        ]);

        $response = $this->delete("/bank-account-management/{$account->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $account->id,
        ]);
    }

    /** @test */
    public function balance_twd_is_null_when_no_exchange_rate_for_today()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/bank-account-management', [
            'currency_code' => 'CHF',
            'balance' => '200.00',
            'is_active' => '1',
        ]);

        $account = BankAccount::first();
        $this->assertNull($account->balance_twd);

        $this->get('/bank-account-management')
            ->assertSee('約 0.00 TWD'); // Blade shows 0 when null
    }
}