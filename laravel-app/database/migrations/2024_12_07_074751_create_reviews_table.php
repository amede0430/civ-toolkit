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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('plan_id');
            $table->unsignedTinyInteger('rating');
            $table->text('comment');


            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->timestamps(); //review_date == created_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
