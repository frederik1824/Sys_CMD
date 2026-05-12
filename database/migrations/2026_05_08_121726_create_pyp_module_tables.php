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
        // 1. Catálogo de Programas PyP (Diabetes, Hipertensión, etc.)
        Schema::create('pyp_programas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->string('icon')->default('ph-activity');
            $table->string('color')->default('#4f46e5');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Expedientes PyP (Perfil Clínico Maestro)
        Schema::create('pyp_expedientes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('afiliado_id')->constrained('afiliados')->onDelete('cascade');
            
            // Riesgo y Scoring
            $table->decimal('riesgo_score', 8, 2)->default(0);
            $table->enum('riesgo_nivel', ['Bajo', 'Moderado', 'Alto'])->default('Bajo');
            
            // Estado Clínico
            $table->enum('estado_clinico', ['Controlado', 'Parcialmente Controlado', 'Descompensado'])->default('Controlado');
            $table->json('enfermedades_json')->nullable(); // Para búsqueda rápida de múltiples patologías
            
            // Control Operativo
            $table->timestamp('ultimo_seguimiento_at')->nullable();
            $table->timestamp('proxima_evaluacion_at')->nullable();
            $table->foreignId('asignado_a')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Evaluaciones Médicas (Detalle Clínico)
        Schema::create('pyp_evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('expediente_id')->constrained('pyp_expedientes')->onDelete('cascade');
            $table->foreignId('medico_id')->constrained('users');
            
            // Datos Clínicos
            $table->json('diagnosticos')->nullable();
            $table->json('signos_vitales')->nullable();
            $table->json('factores_riesgo')->nullable();
            $table->text('observaciones')->nullable();
            
            // Scoring Resultante
            $table->decimal('score_calculado', 8, 2);
            $table->enum('nivel_resultante', ['Bajo', 'Moderado', 'Alto']);
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Seguimientos PyP (Log de Interacciones CRM)
        Schema::create('pyp_seguimientos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('expediente_id')->constrained('pyp_expedientes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            
            $table->enum('tipo_contacto', ['Llamada', 'WhatsApp', 'Visita Domiciliaria', 'Telemedicina', 'Oficina']);
            $table->enum('resultado', ['Exitoso', 'No contestó', 'Cita Programada', 'Rechazado']);
            $table->text('comentarios')->nullable();
            
            $table->timestamp('proximo_contacto_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Relación Muchos a Muchos: Expediente <-> Programas
        Schema::create('pyp_expediente_programa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('pyp_expedientes')->onDelete('cascade');
            $table->foreignId('programa_id')->constrained('pyp_programas')->onDelete('cascade');
            $table->date('fecha_inscripcion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pyp_expediente_programa');
        Schema::dropIfExists('pyp_seguimientos');
        Schema::dropIfExists('pyp_evaluaciones');
        Schema::dropIfExists('pyp_expedientes');
        Schema::dropIfExists('pyp_programas');
    }
};
