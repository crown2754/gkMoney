<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AddExpenseTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_see_the_add_expense_page(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/expenses/create');
        $response->assertOk();
        $response->assertSee('新增支出');
    }

    public function test_user_can_submit_a_valid_expense_via_livewire(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create(['name' => '食餐']);

        Storage::fake('public');

        Livewire::test(\App\Livewire\AddExpense::class)
            ->set('amount', '120.00')
            ->set('currency', 'TWD')
            ->set('category_id', $category->id)
            ->set('expense_date', today()->toDateString())
            ->set('payment_method', 'cash')
            ->set('description', '測試備註')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'amount' => '120.00',
            'currency' => 'TWD',
            'category_id' => $category->id,
        ]);
    }
}
