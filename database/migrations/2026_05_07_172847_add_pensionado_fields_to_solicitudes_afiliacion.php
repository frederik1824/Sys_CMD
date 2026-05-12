<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_afiliacion', function (Blueprint $table) {
            $table->string('numero_resolucion')->nullable()->after('rnc_empresa');
            $table->string('tipo_pension')->nullable()->after('numero_resolucion');
            $table->string('institucion_pension')->nullable()->after('tipo_pension');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_afiliacion', function (Blueprint $table) {
            $table->dropColumn(['numero_resolucion', 'tipo_pension', 'institucion_pension']);
        });
    }
};
