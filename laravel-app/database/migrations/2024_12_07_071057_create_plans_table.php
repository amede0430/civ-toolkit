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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');

            $table->string('title');
            $table->text('description');
            // $table->string('file_url');

            $table->unsignedDecimal('price');
            $table->boolean('free')->default(false);

            $table->string('cover_path');
            $table->string('pdf_path');
            $table->string('zip_path');

            $table->timestamps(); // Upload_date == created_at

            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            $table->foreign('uploader_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
