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
            $table->string('poliza')->nullable()->after('cedula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_center_registros', function (Blueprint $table) {
            $table->dropColumn('poliza');
        });
    }
};
