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
        Schema::create('mensajeros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cedula')->unique();
            $table->string('telefono')->nullable();
            $table->string('vehiculo_placa')->nullable();
            $table->string('vehiculo_tipo')->default('Motor'); // Motor, Carro, etc.
            $table->boolean('activo')->default(true);
            $table->string('color')->default('#3b82f6'); // Para visualizaciones en mapa/dashboard
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajeros');
    }
};
