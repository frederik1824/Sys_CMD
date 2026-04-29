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
        Schema::table('afiliados', function (Blueprint $table) {
            if (!Schema::hasColumn('afiliados', 'fecha_entrega_safesure')) {
                $table->date('fecha_entrega_safesure')->nullable()->after('recibo_liquidacion');
            }
            if (!Schema::hasColumn('afiliados', 'codigo')) {
                $table->string('codigo')->nullable()->after('cedula');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn(['fecha_entrega_safesure', 'codigo']);
        });
    }
};
