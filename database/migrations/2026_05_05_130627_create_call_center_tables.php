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
        // 1. Estados del Call Center (18 niveles)
        Schema::create('call_center_estados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('color')->nullable();
            $table->string('icono')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('finalizador')->default(false);
            $table->timestamps();
        });

        // 2. Cargas Diarias
        Schema::create('call_center_cargas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('user_id')->constrained();
            $table->integer('total_registros')->default(0);
            $table->integer('registros_nuevos')->default(0);
            $table->integer('registros_actualizados')->default(0);
            $table->timestamps();
        });

        // 3. Registros de Call Center (Stage Area)
        Schema::create('call_center_registros', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('carga_id')->constrained('call_center_cargas');
            $table->foreignId('estado_id')->constrained('call_center_estados');
            $table->foreignId('operador_id')->nullable()->constrained('users');
            
            // Datos del Afiliado (Data de Importación)
            $table->string('cedula')->index();
            $table->string('nombre');
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('tipo_afiliado')->nullable();
            
            // Datos de Empresa (Data de Importación)
            $table->string('empresa_nombre')->nullable();
            $table->string('empresa_rnc')->nullable();
            $table->string('empresa_contacto')->nullable();
            $table->string('empresa_direccion')->nullable();
            
            // Ubicación
            $table->string('provincia')->nullable();
            $table->string('municipio')->nullable();
            
            // Relaciones con Sistema Maestro (Si existen)
            $table->char('afiliado_id', 36)->nullable()->index(); // UUID de tabla afiliados
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('lote_id')->nullable()->constrained('lotes');
            
            // Metadatos de Gestión
            $table->integer('intentos_llamada')->default(0);
            $table->timestamp('ultima_gestion_at')->nullable();
            $table->timestamp('proximo_contacto_at')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('prioridad')->default('Media'); // Baja, Media, Alta
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Gestiones / Llamadas
        Schema::create('call_center_gestiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_id')->constrained('call_center_registros');
            $table->foreignId('operador_id')->constrained('users');
            $table->foreignId('estado_anterior_id')->constrained('call_center_estados');
            $table->foreignId('estado_nuevo_id')->constrained('call_center_estados');
            
            $table->string('tipo_contacto')->default('Llamada'); // Llamada, WhatsApp, Empresa
            $table->string('resultado_contacto');
            $table->string('telefono_contactado')->nullable();
            $table->string('persona_contactada')->nullable();
            $table->text('observacion')->nullable();
            $table->date('fecha_proximo_contacto')->nullable();
            
            $table->timestamps();
        });

        // 5. Gestión Documental
        Schema::create('call_center_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_id')->constrained('call_center_registros');
            $table->string('nombre_documento'); // Copia cédula, Formulario firmado, etc.
            $table->enum('estado', ['No solicitado', 'Solicitado', 'Recibido parcial', 'Recibido completo', 'Rechazado'])->default('No solicitado');
            $table->string('path_archivo')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });

        // 6. Despacho y Mensajería
        Schema::create('call_center_despachos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_id')->constrained('call_center_registros');
            $table->string('direccion_entrega');
            $table->string('empresa_destino')->nullable();
            $table->string('persona_recibe')->nullable();
            $table->string('telefono_contacto')->nullable();
            $table->foreignId('mensajero_id')->nullable()->constrained('mensajeros');
            $table->timestamp('fecha_despacho')->nullable();
            $table->string('estado_despacho')->default('Pendiente de despacho');
            $table->text('observaciones')->nullable();
            $table->boolean('formulario_recibido')->default(false);
            $table->timestamps();
        });

        // 7. Bandeja de Salida (Integración)
        Schema::create('call_center_bandeja_salida', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_id')->constrained('call_center_registros');
            $table->timestamp('fecha_envio');
            $table->foreignId('enviado_por')->constrained('users');
            $table->boolean('procesado')->default(false);
            $table->timestamp('fecha_procesado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_center_bandeja_salida');
        Schema::dropIfExists('call_center_despachos');
        Schema::dropIfExists('call_center_documentos');
        Schema::dropIfExists('call_center_gestiones');
        Schema::dropIfExists('call_center_registros');
        Schema::dropIfExists('call_center_cargas');
        Schema::dropIfExists('call_center_estados');
    }
};
