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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->enum('document_type', ['CV', 'report', 'other']);
            $table->string('title');
            $table->text('content');

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->timestamps(); // last_updated == updated_at; creation_date == created_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
