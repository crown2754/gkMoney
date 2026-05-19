<?php

use App\Models\User;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use Laravel\Sanctum\Sanctum;

it('allows an authenticated user to add an expense with valid data', function () {
    // Arrange: Create user and category
    $user = User::factory()->create();
    $category = ExpenseCategory::factory()->create([
        'user_id' => $user->id,
    ]);

    // Authenticate user via Sanctum
    Sanctum::actingAs($user, ['*']);

    $expenseData = [
        'date' => today()->toDateString(),
        'amount_cents' => 1500, // $15.00
        'currency' => 'USD',
        'category_id' => $category->id,
        'description': 'Test expense for lunch',
        'receipt_image_url' => null,
    ];

    // Act: Submit expense creation request
    $response = $this->postJson('/api/v1/expenses', $expenseData);

    // Assert: Validate response and database state
    $response->assertCreated()
        ->assertJsonStructure([
            'id',
            'user_id',
            'date',
            'amount_cents',
            'currency',
            'category_id',
            'description',
            'receipt_image_url',
            'created_at',
            'updated_at',
            'is_deleted',
        ])
        ->assertJsonFragment([
            'user_id' => $user->id,
            'date' => today()->toDateString(),
            'amount_cents' => 1500,
            'currency' => 'USD',
            'category_id' => $category->id,
            'description' => 'Test expense for lunch',
            'receipt_image_url' => null,
            'is_deleted' => false,
        ]);

    // Verify expense was stored in database
    $this->assertDatabaseHas('expenses', [
        'id' => $response->json('id'),
        'user_id' => $user->id,
        'date' => today()->toDateString(),
        'amount_cents' => 1500,
        'currency' => 'USD',
        'category_id' => $category->id,
        'description' => 'Test expense for lunch',
        'receipt_image_url' => null,
        'is_deleted' => false,
    ]);
});

it('prevents saving expense when amount is invalid', function () {
    // Arrange: Create user and category
    $user = User::factory()->create();
    $category = ExpenseCategory::factory()->create([
        'user_id' => $user->id,
    ]);

    Sanctum::actingAs($user, ['*']);

    // Act: Submit expense with invalid amount (zero)
    $response = $this->postJson('/api/v1/expenses', [
        'date' => today()->toDateString(),
        'amount_cents' => 0, // Invalid: must be > 0
        'currency' => 'USD',
        'category_id' => $category->id,
        'description' => 'Invalid expense',
    ]);

    // Assert: Validation error response
    $response->assertStatus(400)
        ->assertJsonFragment([
            'error' => 'VALIDATION_FAILED',
        ])
        ->assertJsonStructure([
            'error',
            'details' => [
                '*' => ['field', 'message']
            ]
        ]);

    // Verify no expense was saved
    $this->assertDatabaseCount('expenses', 0);
});

it('prevents saving expense when category belongs to another user', function () {
    // Arrange: Create two users and a category for the first user
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = ExpenseCategory::factory()->create([
        'user_id' => $user->id, // Category belongs to first user
    ]);

    // Authenticate as second user
    Sanctum::actingAs($otherUser, ['*']);

    // Act: Attempt to use first user's category
    $response = $this->postJson('/api/v1/expenses', [
        'date' => today()->toDateString(),
        'amount_cents' => 1000,
        'currency' => 'USD',
        'category_id' => $category->id, // Belongs to other user
        'description' => 'Unauthorized category usage',
    ]);

    // Assert: Forbidden response
    $response->assertStatus(403)
        ->assertJsonFragment([
            'error' => 'FORBIDDEN',
        ]);

    // Verify no expense was saved
    $this->assertDatabaseCount('expenses', 0);
});

it('allows saving expense with optional receipt image URL', function () {
    // Arrange: Create user and category
    $user = User::factory()->create();
    $category = ExpenseCategory::factory()->create([
        'user_id' => $user->id,
    ]);

    Sanctum::actingAs($user, ['*']);

    $receiptUrl = 'https://example.com/receipts/abc123.jpg';

    // Act: Submit expense with receipt
    $response = $this->postJson('/api/v1/expenses', [
        'date' => today()->toDateString(),
        'amount_cents' => 2500,
        'currency' => 'EUR',
        'category_id' => $category->id,
        'description' => 'Dinner with receipt',
        'receipt_image_url' => $receiptUrl,
    ]);

    // Assert: Successful creation
    $response->assertCreated()
        ->assertJsonFragment([
            'receipt_image_url' => $receiptUrl,
        ]);

    // Verify expense stored with receipt URL
    $this->assertDatabaseHas('expenses', [
        'receipt_image_url' => $receiptUrl,
    ]);
});

it('shows appropriate error when expense date is too far in future', function () {
    // Arrange: Create user and category
    $user = User::factory()->create();
    $category = ExpenseCategory::factory()->create([
        'user_id' => $user->id,
    ]);

    Sanctum::actingAs($user, ['*']);

    // Act: Submit expense with date beyond allowed range (today + 1)
    $response = $this->postJson('/api/v1/expenses', [
        'date' => today()->add(2 days)->toDateString(), // Invalid: > tomorrow
        'amount_cents' => 1000,
        'currency' => 'USD',
        'category_id' => $category->id,
        'description' => 'Future expense',
    ]);

    // Assert: Validation error
    $response->assertStatus(400)
        ->assertJsonFragment([
            'error' => 'VALIDATION_FAILED',
        ])
        ->assertJsonStructure([
            'error',
            'details' => [
                '*' => ['field', 'message']
            ]
        ]);

    // Verify no expense was saved
    $this->assertDatabaseCount('expenses', 0);
});

```