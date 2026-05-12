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
        Schema::table('pyp_seguimientos', function (Blueprint $table) {
            $table->string('tipo_contacto')->change();
            $table->string('resultado')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pyp_seguimientos', function (Blueprint $table) {
            $table->enum('tipo_contacto', ['Llamada', 'WhatsApp', 'Visita Domiciliaria', 'Telemedicina', 'Oficina'])->change();
            $table->enum('resultado', ['Exitoso', 'No contestó', 'Cita Programada', 'Rechazado'])->change();
        });
    }
};
