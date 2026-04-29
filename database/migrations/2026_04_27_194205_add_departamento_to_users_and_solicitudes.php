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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('departamento_id')->after('avatar')->nullable()->constrained('departamentos')->nullOnDelete();
        });

        Schema::table('solicitudes_afiliacion', function (Blueprint $table) {
            $table->foreignId('departamento_id')->after('tipo_solicitud_id')->nullable()->constrained('departamentos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_afiliacion', function (Blueprint $table) {
            $table->dropForeign(['departamento_id']);
            $table->dropColumn('departamento_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['departamento_id']);
            $table->dropColumn('departamento_id');
        });
    }
};
