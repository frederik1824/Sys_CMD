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
        Schema::table('empresas', function (Blueprint $table) {
            // Contacto
            $table->string('contacto_nombre')->nullable()->after('telefono');
            $table->string('contacto_puesto')->nullable()->after('contacto_nombre');
            $table->string('contacto_telefono')->nullable()->after('contacto_puesto');
            $table->string('contacto_email')->nullable()->after('contacto_telefono');
            
            // Comisiones
            $table->string('comision_tipo')->nullable()->comment('Fixed, Percentage')->after('contacto_email');
            $table->decimal('comision_valor', 10, 2)->nullable()->after('comision_tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'contacto_nombre', 'contacto_puesto', 'contacto_telefono', 'contacto_email',
                'comision_tipo', 'comision_valor'
            ]);
        });
    }
};
