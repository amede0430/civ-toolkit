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
        Schema::create('commands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('engineer_id')->nullable();

            $table->float('area');  // Superficie
            $table->tinyInteger('levels_number');  // Nombre de niveaux
            $table->text('materials');  // A voir
            $table->enum('zone', ['agglomeration', 'village']);
            $table->enum('construction_type', ['individual', 'public']);
            $table->enum('command_type', ['entire', 'element']);  // Nature de la commande
            $table->string('file_path')->nullable();  // Fichier joint (DWG/PDF, etc)
            $table->timestamp('deadline');  // Dernier delai
            $table->text('comment')->nullable();  // Description et commentaire

            $table->enum('status', ['pending', 'accepted', 'rejected']);
            $table->unsignedDecimal('price')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('engineer_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
