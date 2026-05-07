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
            $table->unsignedBigInteger('motivo_rechazo_id')->nullable()->after('estado_id');
            $table->foreign('motivo_rechazo_id')->references('id')->on('motivo_rechazo_traspasos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traspasos', function (Blueprint $table) {
            $table->dropForeign(['motivo_rechazo_id']);
            $table->dropColumn('motivo_rechazo_id');
        });
    }
};
