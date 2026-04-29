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
        // Asegurar índices y llaves foráneas en Afiliados
        Schema::table('afiliados', function (Blueprint $table) {
            // Si ya existen las columnas pero no tienen el constrained(), lo aseguramos
            // Note: In Laravel 11/latest, constrained() handles common cases well.
            // We use indexes for performance on filters.
            
            $table->index('provincia_id');
            $table->index('municipio_id');
            $table->index('empresa_id');
        });

        // Asegurar índices y llaves foráneas en Empresas
        Schema::table('empresas', function (Blueprint $table) {
            $table->index('provincia_id');
            $table->index('municipio_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropIndex(['provincia_id']);
            $table->dropIndex(['municipio_id']);
            $table->dropIndex(['empresa_id']);
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropIndex(['provincia_id']);
            $table->dropIndex(['municipio_id']);
        });
    }
};
