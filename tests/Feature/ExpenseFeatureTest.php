<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

class ExpenseFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function migration_creates_expenses_table_with_correct_user_id_type_and_foreign_key()
    {
        // 確認表格存在
        $this->assertTrue(
            $this->app->make('db')->getSchemaBuilder()->hasTable('expenses'),
            'expenses 表應該存在'
        );

        // 確認 user_id 欄位存在
        $columns = $this->app->make('db')->getSchemaBuilder()->getColumnListing('expenses');
        $this->assertContains('user_id', $columns, 'user_id 欄位應該存在');

        // 檢查欄位型別為 bigint unsigned
        $details = $this->app->make('db')
            ->connection()
            ->getDoctrineColumn('expenses', 'user_id');

        $this->assertEquals('bigint', $details->getType(), 'user_id 應為 bigint');
        $this->assertTrue(
            $details->getUnsigned(),
            'user_id 應為 unsigned (foreignId)'
        );

        // 檢查外鍵約束指向 users.id
        $foreignKeys = $this->app->make('db')
            ->connection()
            ->getDoctrineSchemaManager()
            ->listTableForeignKeys('expenses');

        $fk = collect($foreignKeys)->firstWhere('localColumns', ['user_id']);
        $this->assertNotNull($fk, '應該存在 user_id 的外鍵約束');
        $this->assertEquals('users', $fk->getForeignTableName(), '外鍵應指向 users 表');
        $this->assertEquals(['id'], $fk->getForeignColumns(), '外鍵應參照 users.id 欄位');
    }

    /** @test */
    public function authenticated_user_can_create_expense_via_livewire_component()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('expense-form.submit'), [
                'amount' => 10.50,
                'description' => 'Test expense',
                'expense_date' => now()->toDateString(),
            ])
            ->assertRedirect() // 假設成功後會重導至同頁或其他頁面
            ->assertSessionHas('message', 'Expense created successfully.');

        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'amount' => 10.50,
            'description' => 'Test expense',
        ]);
    }

    /** @test */
    public function expense_model_belongs_to_user_relation_works()
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->make([
            'amount' => 20.00,
            'description' => 'Another expense',
            'expense_date' => now(),
        ]);

        $user->expenses()->save($expense);

        $this->assertInstanceOf(User::class, $expense->user);
        $this->assertEquals($user->id, $expense->user->id);
    }
}