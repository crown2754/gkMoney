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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();                               // primary key (bigint unsigned)
            $table->foreignId('user_id')                // <-- matches users.id (bigint unsigned)
                  ->constrained()                       // foreign key to users.id
                  ->onDelete('cascade');                // or 'set null' per business rule
            $table->decimal('amount', 10, 2);
            $table->string('description');
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