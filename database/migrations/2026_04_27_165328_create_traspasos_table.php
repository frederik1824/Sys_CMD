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
        Schema::create('traspasos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_afiliado');
            $table->string('cedula_afiliado')->index();
            $table->string('nombre_solicitante')->nullable();
            $table->string('cedula_solicitante')->nullable();
            $table->date('fecha_solicitud')->nullable();
            $table->date('fecha_envio_epbd')->nullable();
            $table->string('numero_solicitud_epbd')->unique()->nullable();
            $table->boolean('pendiente_carga_documento')->default(false);
            $table->boolean('pendiente_aprobar_consentimiento')->default(false);
            $table->string('agente')->index();
            $table->string('estado')->index();
            $table->text('motivos_estado')->nullable();
            
            // Campos de Enriquecimiento (Seguimiento Manual)
            $table->date('fecha_efectivo')->nullable();
            $table->integer('cantidad_dependientes')->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traspasos');
    }
};
