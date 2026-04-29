<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->date('fecha_entrega_safesure')->nullable()->after('estado_id')->comment('Fecha cuando ARS entrega carnet a SAFESURE');
            $table->decimal('costo_entrega', 10, 2)->default(0.00)->after('fecha_entrega_safesure');
            $table->boolean('liquidado')->default(false)->after('costo_entrega');
            $table->date('fecha_liquidacion')->nullable()->after('liquidado');
            $table->string('recibo_liquidacion')->nullable()->after('fecha_liquidacion');
        });
    }

    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn(['fecha_entrega_safesure', 'costo_entrega', 'liquidado', 'fecha_liquidacion', 'recibo_liquidacion']);
        });
    }
};
