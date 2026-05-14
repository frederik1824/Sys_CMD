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
        Schema::table('call_center_registros', function (Blueprint $table) {
            $table->foreignId('created_by_id')->nullable()->after('carga_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_center_registros', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_id');
        });
    }
};
