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
            $table->foreignId('provincia_id')->nullable()->after('provincia')->constrained('provincias')->onDelete('set null');
            $table->foreignId('municipio_id')->nullable()->after('municipio')->constrained('municipios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['provincia_id']);
            $table->dropForeign(['municipio_id']);
            $table->dropColumn(['provincia_id', 'municipio_id']);
        });
    }
};
