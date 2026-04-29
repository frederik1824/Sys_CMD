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
            $table->string('periodo_efectivo')->nullable()->index()->after('fecha_efectivo');
        });
    }

    public function down(): void
    {
        Schema::table('traspasos', function (Blueprint $table) {
            $table->dropColumn('periodo_efectivo');
        });
    }
};
