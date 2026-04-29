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
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: "Importación 2026-03-22 12:00"
            $table->foreignId('corte_id')->constrained('cortes');
            $table->string('empresa_tipo'); // CMD o OTRAS
            $table->foreignId('user_id')->constrained('users');
            $table->integer('total_registros')->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
