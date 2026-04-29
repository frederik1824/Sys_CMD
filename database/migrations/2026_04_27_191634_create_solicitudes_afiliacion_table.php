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
        Schema::create('solicitudes_afiliacion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_solicitud')->unique();
            $table->foreignId('tipo_solicitud_id')->constrained('tipos_solicitud_afiliacion');
            $table->foreignId('solicitante_user_id')->constrained('users');
            $table->foreignId('asignado_user_id')->nullable()->constrained('users');
            $table->uuid('afiliado_id')->nullable(); // Usamos UUID ya que la tabla afiliados usa UUIDs
            $table->string('cedula');
            $table->string('nombre_completo');
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->string('empresa')->nullable();
            $table->string('rnc_empresa')->nullable();
            $table->string('estado')->default('Borrador');
            $table->string('prioridad')->default('Normal');
            $table->text('observacion_solicitante')->nullable();
            $table->text('observacion_interna')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->text('motivo_devolucion')->nullable();
            $table->timestamp('sla_fecha_limite')->nullable();
            $table->timestamp('fecha_asignacion')->nullable();
            $table->timestamp('fecha_cierre')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_afiliacion');
    }
};
