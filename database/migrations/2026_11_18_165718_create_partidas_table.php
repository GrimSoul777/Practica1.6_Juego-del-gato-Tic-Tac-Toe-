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
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugadorX_id')->constrained('users');
            $table->foreignId('jugadorO_id')->nullable()->constrained('users');
            $table->string('tablero')->default('---------');
            $table->string('turno');
            $table->string('estado')->default('disponible');
            $table->foreignId('ganador_id')->nullable()->constrained('users');
            $table->string('resultado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
