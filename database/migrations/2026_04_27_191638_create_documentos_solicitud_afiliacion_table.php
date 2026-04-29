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
        Schema::create('documentos_solicitud_afiliacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes_afiliacion')->onDelete('cascade');
            $table->foreignId('documento_requerido_id')->constrained('documentos_requeridos_solicitud');
            $table->string('archivo_path');
            $table->string('nombre_original');
            $table->string('mime_type')->nullable();
            $table->string('validacion_estado')->default('Pendiente');
            $table->text('comentario_validacion')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_solicitud_afiliacion');
    }
};
