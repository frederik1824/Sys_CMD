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
        Schema::create('lote_liquidacions', function (Blueprint $table) {
            $table->id();
            $table->string('recibo')->index();
            $table->date('fecha');
            $table->decimal('monto_total', 15, 2);
            $table->integer('conteo_registros');
            $table->foreignId('responsable_id')->nullable()->constrained('responsables');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote_liquidacions');
    }
};
