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
            $table->foreignId('responsable_id')->nullable()->constrained('responsables')->nullOnDelete();
            $table->string('estado_contacto')->default('Nuevo')->comment('Nuevo, Contactado, En Negociación, Afiliada, No Contactar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->dropColumn(['responsable_id', 'estado_contacto']);
        });
    }
};
