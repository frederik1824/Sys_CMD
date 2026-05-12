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
            $table->timestamp('unipago_revisado_at')->nullable()->after('verificado_at');
            $table->string('unipago_status')->default('pendiente')->after('unipago_revisado_at');
            $table->text('unipago_observaciones')->nullable()->after('unipago_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traspasos', function (Blueprint $table) {
            $table->dropColumn(['unipago_revisado_at', 'unipago_status', 'unipago_observaciones']);
        });
    }
};
