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
        // 1. Cabecera de la Carga (Resumen de Dispersión)
        Schema::create('dispersion_pensionados_cargas', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('periodo', 7)->index(); // YYYY-MM
            $table->timestamp('fecha_carga');
            $table->foreignId('user_id')->constrained('users');
            $table->string('nombre_archivo');
            $table->string('archivo_path');
            $table->string('hash_archivo')->index();
            $table->integer('total_registros')->default(0);
            $table->integer('total_titulares')->default(0);
            $table->integer('total_dependientes')->default(0);
            $table->decimal('monto_total_dispersado', 15, 2)->default(0);
            $table->decimal('monto_total_salud', 15, 2)->default(0);
            $table->decimal('monto_total_capita', 15, 2)->default(0);
            $table->enum('estado', ['Pendiente', 'Procesando', 'Completado', 'Error', 'Anulado'])->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Detalle de Titulares
        Schema::create('dispersion_pensionados_titulares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carga_id')->constrained('dispersion_pensionados_cargas')->onDelete('cascade');
            $table->string('tipo_afiliado', 2)->nullable();
            $table->string('cedula')->index();
            $table->string('nss', 20)->nullable();
            $table->string('codigo_pensionado')->index();
            $table->decimal('salario', 15, 2)->default(0);
            $table->decimal('monto_descuento_salud', 15, 2)->default(0);
            $table->decimal('monto_capita_adicional', 15, 2)->default(0);
            $table->string('tipo_pago', 50)->nullable();
            $table->string('cuenta_banco', 100)->nullable();
            $table->string('tipo_pensionado', 100)->nullable();
            $table->string('origen_pension', 100)->nullable();
            $table->decimal('monto_total', 15, 2)->default(0);
            $table->string('periodo', 10)->index();
            $table->text('raw_line');
            $table->string('hash_integridad')->nullable();
            $table->timestamps();
        });

        // 3. Detalle de Dependientes
        Schema::create('dispersion_pensionados_dependientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carga_id')->constrained('dispersion_pensionados_cargas')->onDelete('cascade');
            $table->foreignId('titular_id')->nullable()->constrained('dispersion_pensionados_titulares')->onDelete('cascade');
            $table->string('cedula_titular')->index();
            $table->string('nss_titular', 20)->nullable();
            $table->string('codigo_pensionado')->index();
            $table->string('cedula_dependiente')->index();
            $table->string('nss_dependiente', 20)->nullable();
            $table->string('tipo_pensionado', 100)->nullable();
            $table->string('origen_pension', 100)->nullable();
            $table->string('periodo', 10)->index();
            $table->text('raw_line');
            $table->string('hash_integridad')->nullable();
            $table->timestamps();
        });

        // 4. Logs de Procesamiento
        Schema::create('dispersion_pensionados_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carga_id')->constrained('dispersion_pensionados_cargas')->onDelete('cascade');
            $table->string('tipo')->index(); // warning, error, info
            $table->text('mensaje');
            $table->longText('detalles')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispersion_pensionados_logs');
        Schema::dropIfExists('dispersion_pensionados_dependientes');
        Schema::dropIfExists('dispersion_pensionados_titulares');
        Schema::dropIfExists('dispersion_pensionados_cargas');
    }
};
