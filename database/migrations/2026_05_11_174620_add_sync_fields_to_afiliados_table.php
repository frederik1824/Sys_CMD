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
            $table->string('sync_status')->default('synced')->after('firebase_synced_at');
            $table->timestamp('firebase_updated_at')->nullable()->after('sync_status');
            $table->timestamp('last_sync_attempt_at')->nullable()->after('firebase_updated_at');
            $table->text('sync_error_message')->nullable()->after('last_sync_attempt_at');
            $table->integer('sync_attempts')->default(0)->after('sync_error_message');
            $table->string('updated_from')->default('CMD')->after('sync_attempts'); // CMD, SAFE, system
            $table->string('hash_checksum', 64)->nullable()->after('updated_from');
            $table->integer('sync_version')->default(1)->after('hash_checksum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn([
                'sync_status',
                'firebase_updated_at',
                'last_sync_attempt_at',
                'sync_error_message',
                'sync_attempts',
                'updated_from',
                'hash_checksum',
                'sync_version'
            ]);
        });
    }
};
