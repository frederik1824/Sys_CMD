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
        Schema::table('pyp_evaluaciones', function (Blueprint $table) {
            // Eliminar columnas viejas si existen
            $table->dropColumn(['diagnosticos', 'signos_vitales', 'factores_riesgo', 'observaciones', 'score_calculado', 'nivel_resultante']);
            
            // Agregar columnas nuevas unificadas
            $table->json('datos_evaluacion_json')->after('medico_id')->nullable();
            $table->text('diagnostico')->after('datos_evaluacion_json')->nullable();
            $table->text('plan_accion')->after('diagnostico')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pyp_evaluaciones', function (Blueprint $table) {
            $table->dropColumn(['datos_evaluacion_json', 'diagnostico', 'plan_accion']);
            
            $table->json('diagnosticos')->nullable();
            $table->json('signos_vitales')->nullable();
            $table->json('factores_riesgo')->nullable();
            $table->text('observaciones')->nullable();
            $table->decimal('score_calculado', 8, 2);
            $table->enum('nivel_resultante', ['Bajo', 'Moderado', 'Alto']);
        });
    }
};
