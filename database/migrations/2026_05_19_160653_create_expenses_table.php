<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::createIfNotExists('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()          // references users.id (bigint)
                  ->onDelete('cascade');   // adjust as needed
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->date('expense_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};