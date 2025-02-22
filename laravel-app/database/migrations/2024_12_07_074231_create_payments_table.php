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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->enum('product_type', ['plan', 'command']);
            $table->unsignedDecimal('amount');
            $table->string('method'); // momo, carte, paypal...
            $table->string('reference')->unique();
            $table->enum('command_state', ['first_half', 'second_half'])->nullable();
            $table->boolean('paid')->default(false);

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->timestamps(); // payment_date == created_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
