<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure the uuid-ossp extension is available for PostgreSQL
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('date')->nullable(false)->default(DB::raw('CURRENT_DATE'));
            $table->bigInteger('amount_cents')->nullable(false);
            $table->check('amount_cents > 0', 'expenses_amount_cents_check');
            $table->char('currency', 3)->nullable(false)->default('USD');
            $table->uuid('category_id')->nullable(false);
            $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('restrict');
            $table->text('description')->nullable();
            $table->text('receipt_image_url')->nullable();
            $table->timestampTz('created_at')->nullable(false)->default(DB::raw('now()'));
            $table->timestampTz('updated_at')->nullable(false)->default(DB::raw('now()'));
            $table->boolean('is_deleted')->nullable(false)->default(false);

            // Indexes
            $table->index(['user_id', 'date'], 'expenses_user_id_date_idx');
            $table->index('category_id', 'expenses_category_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};