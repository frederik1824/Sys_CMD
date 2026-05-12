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
        Schema::table('asistencia_registros', function (Blueprint $table) {
            $table->boolean('requiere_justificacion')->default(false)->after('cumplio_jornada');
            $table->text('justificacion_empleado')->nullable()->after('requiere_justificacion');
            $table->timestamp('hora_salida_ajustada')->nullable()->after('justificacion_empleado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_registros', function (Blueprint $table) {
            $table->dropColumn(['requiere_justificacion', 'justificacion_empleado', 'hora_salida_ajustada']);
        });
    }
};
