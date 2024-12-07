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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('plan_id');
            $table->datetime('transaction_date');
            $table->unsignedDecimal('amount');
            $table->enum('status', ['paid', 'pending', 'canceled']);

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
