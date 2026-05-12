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
        Schema::create('pss_medicos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('telefono_1')->nullable();
            $table->string('telefono_2')->nullable();
            $table->foreignId('ciudad_id')->nullable()->constrained('pss_ciudades');
            $table->foreignId('especialidad_id')->nullable()->constrained('pss_especialidades');
            $table->foreignId('clinica_id')->nullable()->constrained('pss_clinicas');
            $table->string('estado')->default('activo'); // activo, inactivo, depuración
            $table->string('origen_importacion')->nullable();
            $table->timestamp('fecha_importacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Index para búsquedas rápidas
            $table->index(['nombre', 'ciudad_id', 'especialidad_id']);
        });

        Schema::create('pss_centros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('telefono_1')->nullable();
            $table->string('telefono_2')->nullable();
            $table->foreignId('ciudad_id')->nullable()->constrained('pss_ciudades');
            $table->foreignId('grupo_id')->nullable()->constrained('pss_grupos');
            $table->string('estado')->default('activo');
            $table->string('origen_importacion')->nullable();
            $table->timestamp('fecha_importacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['nombre', 'ciudad_id', 'grupo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pss_centros');
        Schema::dropIfExists('pss_medicos');
    }
};
