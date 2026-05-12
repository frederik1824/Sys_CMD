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
        Schema::table('pyp_expedientes', function (Blueprint $table) {
            $table->string('riesgo_nivel')->default('Bajo')->change();
            $table->string('estado_clinico')->default('Pendiente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pyp_expedientes', function (Blueprint $table) {
            $table->enum('riesgo_nivel', ['Bajo', 'Moderado', 'Alto'])->default('Bajo')->change();
            $table->enum('estado_clinico', ['Estable', 'Compensado', 'Descompensado', 'Crítico'])->default('Estable')->change();
        });
    }
};
