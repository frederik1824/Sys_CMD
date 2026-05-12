<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (!Schema::hasColumn('empresas', 'sync_status')) {
                $table->string('sync_status', 20)->nullable()->default('pending')->after('uuid');
            }
            if (!Schema::hasColumn('empresas', 'firebase_synced_at')) {
                $table->timestamp('firebase_synced_at')->nullable()->after('sync_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['sync_status', 'firebase_synced_at']);
        });
    }
};
