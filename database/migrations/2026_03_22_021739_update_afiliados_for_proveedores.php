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
            $table->renameColumn('fecha_entrega_safesure', 'fecha_entrega_proveedor');
            $table->foreignId('proveedor_id')->nullable()->after('estado_id')->constrained('proveedors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropForeign(['proveedor_id']);
            $table->dropColumn('proveedor_id');
            $table->renameColumn('fecha_entrega_proveedor', 'fecha_entrega_safesure');
        });
    }
};
