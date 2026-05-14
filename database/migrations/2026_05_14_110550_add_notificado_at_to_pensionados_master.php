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
        Schema::table('dispersion_pensionados_master', function (Blueprint $table) {
            $table->timestamp('notificado_at')->nullable()->after('ultimo_pago_confirmado_at');
            $table->foreignId('solicitud_id')->nullable()->after('cedula')->constrained('solicitudes_afiliacion')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispersion_pensionados_master', function (Blueprint $table) {
            $table->dropForeign(['solicitud_id']);
            $table->dropColumn(['notificado_at', 'solicitud_id']);
        });
    }
};
