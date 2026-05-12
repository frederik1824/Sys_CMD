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
        Schema::table('asistencia_empleados', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('estado');
        });

        Schema::table('asistencia_registros', function (Blueprint $table) {
            $table->index('fecha');
            $table->index('minutos_tardanza');
            $table->index('requiere_justificacion');
        });
        
        Schema::table('asistencia_permisos', function (Blueprint $table) {
            $table->index('estado');
            $table->index(['fecha_desde', 'fecha_hasta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_empleados', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['estado']);
        });

        Schema::table('asistencia_registros', function (Blueprint $table) {
            $table->dropIndex(['fecha']);
            $table->dropIndex(['minutos_tardanza']);
            $table->dropIndex(['requiere_justificacion']);
        });

        Schema::table('asistencia_permisos', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['fecha_desde', 'fecha_hasta']);
        });
    }
};
