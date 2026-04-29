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
        Schema::create('documentos_requeridos_solicitud', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_solicitud_id')->constrained('tipos_solicitud_afiliacion');
            $table->string('nombre_documento');
            $table->boolean('obligatorio')->default(true);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_requeridos_solicitud');
    }
};
