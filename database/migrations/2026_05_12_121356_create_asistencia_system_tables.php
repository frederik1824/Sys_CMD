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
        // 1. Departamentos
        Schema::create('asistencia_departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 2. Cargos
        Schema::create('asistencia_cargos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('departamento_id')->constrained('asistencia_departamentos');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 3. Turnos
        Schema::create('asistencia_turnos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Mañana, Tarde, Especial
            $table->time('entrada_esperada');
            $table->time('salida_esperada');
            $table->integer('tolerancia_minutos')->default(15);
            $table->integer('minutos_almuerzo')->default(60);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 4. Empleados (Representantes)
        Schema::create('asistencia_empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('codigo_empleado')->unique();
            $table->string('cedula')->unique();
            $table->string('nombre_completo');
            $table->foreignId('cargo_id')->constrained('asistencia_cargos');
            $table->foreignId('turno_id')->constrained('asistencia_turnos');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('fecha_ingreso');
            $table->string('foto_path')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });

        // 5. Registros de Asistencia
        Schema::create('asistencia_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('asistencia_empleados');
            $table->date('fecha');
            $table->timestamp('hora_entrada')->nullable();
            $table->timestamp('hora_salida')->nullable();
            $table->timestamp('inicio_almuerzo')->nullable();
            $table->timestamp('fin_almuerzo')->nullable();
            
            // Metadatos para auditoría
            $table->string('ip_entrada')->nullable();
            $table->string('dispositivo_entrada')->nullable();
            $table->string('ip_salida')->nullable();
            $table->string('dispositivo_salida')->nullable();
            
            // Cálculos automáticos
            $table->integer('minutos_tardanza')->default(0);
            $table->integer('minutos_salida_temprana')->default(0);
            $table->integer('minutos_trabajados_neto')->default(0);
            $table->boolean('cumplio_jornada')->default(false);
            
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->unique(['empleado_id', 'fecha']);
        });

        // 6. Tipos de Permiso
        Schema::create('asistencia_tipos_permiso', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->boolean('requiere_evidencia')->default(false);
            $table->boolean('es_remunerado')->default(true);
            $table->timestamps();
        });

        // 7. Solicitudes de Permiso
        Schema::create('asistencia_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('asistencia_empleados');
            $table->foreignId('tipo_permiso_id')->constrained('asistencia_tipos_permiso');
            $table->date('fecha_desde');
            $table->date('fecha_hasta');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->text('motivo');
            $table->string('evidencia_path')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->foreignId('aprobado_por')->nullable()->constrained('users');
            $table->text('comentario_aprobador')->nullable();
            $table->timestamps();
        });

        // 8. Feriados
        Schema::create('asistencia_feriados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->date('fecha');
            $table->boolean('recurrente')->default(false); // Si se repite cada año
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_feriados');
        Schema::dropIfExists('asistencia_permisos');
        Schema::dropIfExists('asistencia_tipos_permiso');
        Schema::dropIfExists('asistencia_registros');
        Schema::dropIfExists('asistencia_empleados');
        Schema::dropIfExists('asistencia_turnos');
        Schema::dropIfExists('asistencia_cargos');
        Schema::dropIfExists('asistencia_departamentos');
    }
};
