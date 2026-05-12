<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Índice compuesto para queries de sincronización en afiliados
        Schema::table('afiliados', function (Blueprint $table) {
            if (!Schema::hasIndex('afiliados', 'idx_afiliados_sync_query')) {
                $table->index(['sync_status', 'updated_at', 'firebase_synced_at'], 'idx_afiliados_sync_query');
            }
        });

        // Índice compuesto para queries de sincronización en empresas  
        Schema::table('empresas', function (Blueprint $table) {
            if (!Schema::hasIndex('empresas', 'idx_empresas_sync_query')) {
                $table->index(['sync_status', 'updated_at', 'firebase_synced_at'], 'idx_empresas_sync_query');
            }
        });
    }

    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropIndex('idx_afiliados_sync_query');
        });
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropIndex('idx_empresas_sync_query');
        });
    }
};
