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
        Schema::create('despachos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mensajero_id')->constrained('mensajeros');
            $table->foreignId('ruta_id')->nullable()->constrained('rutas'); // Opcional si es despacho directo
            $table->string('status')->default('pendiente'); // pendiente, en_transito, finalizado, cancelado
            $table->dateTime('fecha_salida')->nullable();
            $table->dateTime('fecha_retorno')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despachos');
    }
};
