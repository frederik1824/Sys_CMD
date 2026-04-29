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
        Schema::create('afiliados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corte_id')->constrained('cortes');
            $table->foreignId('responsable_id')->nullable()->constrained('responsables');
            $table->foreignId('estado_id')->constrained('estados');
            $table->string('nombre_completo');
            $table->string('cedula');
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('provincia')->nullable();
            $table->string('municipio')->nullable();
            $table->string('empresa')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('contrato')->nullable();
            $table->string('poliza')->nullable();
            $table->timestamps();
            
            // Un afiliado solo debería cargar una vez por corte según la cédula
            $table->unique(['cedula', 'corte_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afiliados');
    }
};
