<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->uuid('account_id')->nullable()->index();
            $table->bigInteger('amount_cents');
            $table->char('currency', 3)->default('USD');
            $table->date('expense_date');
            $table->text('description')->nullable();
            $table->uuid('category_id')->nullable()->index();
            $table->boolean('is_recurring')->default(false);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestampTz('deleted_at')->nullable();
            $table->uuid('receipt_id')->nullable()->index();
            $table->string('external_id', 64)->nullable()->unique();
            $table->jsonb('metadata')->nullable();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('set null');
            $table->foreign('receipt_id')->references('id')->on('expense_receipts')->onDelete('set null');
        });

        // Index for quick daily list (PostgreSQL supports DESC in index via raw)
        DB::statement('CREATE INDEX idx_expenses_user_date ON expenses (user_id, expense_date DESC);');
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};