<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Añadir detalles de auditoría al historial
        Schema::table('historial_solicitud_afiliacion', function (Blueprint $table) {
            $table->json('detalles')->nullable()->after('comentario');
        });

        // Añadir campos para control de pausa de SLA y duplicidad en solicitudes
        Schema::table('solicitudes_afiliacion', function (Blueprint $table) {
            $table->timestamp('pausado_at')->nullable()->after('sla_fecha_limite');
            $table->integer('sla_acumulado_segundos')->default(0)->after('pausado_at');
        });
    }

    public function down(): void
    {
        Schema::table('historial_solicitud_afiliacion', function (Blueprint $table) {
            $table->dropColumn('detalles');
        });

        Schema::table('solicitudes_afiliacion', function (Blueprint $table) {
            $table->dropColumn(['pausado_at', 'sla_acumulado_segundos']);
        });
    }
};
