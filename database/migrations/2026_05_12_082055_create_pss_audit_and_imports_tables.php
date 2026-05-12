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
        Schema::create('pss_importaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_archivo');
            $table->string('tipo'); // medicos, centros, mixto
            $table->integer('total_registros')->default(0);
            $table->integer('procesados')->default(0);
            $table->integer('errores')->default(0);
            $table->foreignId('user_id')->constrained('users');
            $table->json('configuracion')->nullable(); // Mapeo de columnas
            $table->text('resultado_json')->nullable();
            $table->timestamps();
        });

        Schema::create('pss_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type'); // PssMedico o PssCentro
            $table->unsignedBigInteger('auditable_id');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('accion'); // create, update, delete, deactivate
            $table->string('campo')->nullable();
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pss_audit_logs');
        Schema::dropIfExists('pss_importaciones');
    }
};
