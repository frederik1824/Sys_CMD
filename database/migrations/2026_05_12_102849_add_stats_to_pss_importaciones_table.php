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
        Schema::table('pss_importaciones', function (Blueprint $table) {
            $table->integer('omitidos')->default(0)->after('errores');
            $table->integer('duplicados')->default(0)->after('omitidos');
        });
    }

    public function down(): void
    {
        Schema::table('pss_importaciones', function (Blueprint $table) {
            $table->dropColumn(['omitidos', 'duplicados']);
        });
    }
};
