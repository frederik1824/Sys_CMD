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
        Schema::table('solicitudes_afiliacion', function (Blueprint $table) {
            $table->unsignedTinyInteger('satisfaccion_nivel')->nullable()->comment('1-5 stars');
            $table->text('satisfaccion_comentario')->nullable();
            $table->boolean('es_primera_resolucion')->default(true)->comment('Flag for First Contact Resolution (FCR)');
            $table->timestamp('fecha_primera_asignacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes_afiliacion', function (Blueprint $table) {
            $table->dropColumn(['satisfaccion_nivel', 'satisfaccion_comentario', 'es_primera_resolucion', 'fecha_primera_asignacion']);
        });
    }
};
