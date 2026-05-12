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
        // 1. Tabla de Auditoría de Sincronización (Trazabilidad Detallada)
        Schema::create('cloud_sync_audits', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('auditable_type');
            $blueprint->string('auditable_id');
            $blueprint->string('field');
            $blueprint->text('old_value')->nullable();
            $blueprint->text('new_value')->nullable();
            $blueprint->string('company_origin')->nullable(); // CMD o SAFE
            $blueprint->string('user_name')->nullable();
            $blueprint->timestamp('synced_at')->useCurrent();
            $blueprint->timestamps();
            
            $blueprint->index(['auditable_type', 'auditable_id']);
        });

        // 2. Campos de Sync para Evidencias
        Schema::table('evidencia_afiliados', function (Blueprint $table) {
            if (!Schema::hasColumn('evidencia_afiliados', 'firebase_synced_at')) {
                $table->timestamp('firebase_synced_at')->nullable();
                $table->string('sync_status')->default('pending');
                $table->string('hash_checksum')->nullable();
            }
        });

        // 3. Campos de Sync para Notas
        Schema::table('notas_afiliados', function (Blueprint $table) {
            if (!Schema::hasColumn('notas_afiliados', 'firebase_synced_at')) {
                $table->timestamp('firebase_synced_at')->nullable();
                $table->string('sync_status')->default('pending');
                $table->string('hash_checksum')->nullable();
            }
        });

        // 4. Campos adicionales de trazabilidad en Afiliados
        Schema::table('afiliados', function (Blueprint $table) {
            if (!Schema::hasColumn('afiliados', 'last_updated_by')) {
                $table->string('last_updated_by')->nullable();
                $table->string('conflict_status')->nullable();
                $table->integer('local_version')->default(1);
                $table->integer('remote_version')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_sync_audits');
        
        Schema::table('evidencia_afiliados', function (Blueprint $table) {
            $table->dropColumn(['firebase_synced_at', 'sync_status', 'hash_checksum']);
        });

        Schema::table('notas_afiliados', function (Blueprint $table) {
            $table->dropColumn(['firebase_synced_at', 'sync_status', 'hash_checksum']);
        });

        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn(['last_updated_by', 'conflict_status', 'local_version', 'remote_version']);
        });
    }
};
