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
        // Expandir Turnos para soportar días laborables
        Schema::table('asistencia_turnos', function (Blueprint $table) {
            $table->boolean('lunes')->default(true)->after('minutos_almuerzo');
            $table->boolean('martes')->default(true)->after('lunes');
            $table->boolean('miercoles')->default(true)->after('martes');
            $table->boolean('jueves')->default(true)->after('miercoles');
            $table->boolean('viernes')->default(true)->after('jueves');
            $table->boolean('sabado')->default(false)->after('viernes');
            $table->boolean('domingo')->default(false)->after('sabado');
        });

        // Configuración Global del Módulo
        Schema::create('asistencia_configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor')->nullable();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_turnos', function (Blueprint $table) {
            $table->dropColumn(['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo']);
        });
        Schema::dropIfExists('asistencia_configuracion');
    }
};
