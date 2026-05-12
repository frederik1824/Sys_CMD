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
        Schema::table('traspasos', function (Blueprint $table) {
            $table->boolean('verificado')->default(false)->after('es_emitido');
            $table->timestamp('verificado_at')->nullable()->after('verificado');
            $table->foreignId('verificado_por')->nullable()->constrained('users')->after('verificado_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traspasos', function (Blueprint $table) {
            $table->dropForeign(['verificado_por']);
            $table->dropColumn(['verificado', 'verificado_at', 'verificado_por']);
        });
    }
};
