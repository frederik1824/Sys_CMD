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
        Schema::table('applications', function (Blueprint $table) {
            // Renombrar key a slug si existe
            if (Schema::hasColumn('applications', 'key')) {
                $table->renameColumn('key', 'slug');
            }
            
            // Renombrar status a is_active y cambiar a boolean
            // Nota: En SQLite/MySQL el rename y change pueden requerir pasos separados o doctrine/dbal
            if (Schema::hasColumn('applications', 'status')) {
                $table->renameColumn('status', 'is_active');
            }
        });

        // Cambio de tipo separado para evitar problemas de compatibilidad en algunos drivers
        Schema::table('applications', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'slug')) {
                $table->renameColumn('slug', 'key');
            }
            if (Schema::hasColumn('applications', 'is_active')) {
                $table->renameColumn('is_active', 'status');
            }
        });

        Schema::table('applications', function (Blueprint $table) {
             $table->enum('status', ['active', 'development', 'maintenance'])->default('active')->change();
        });
    }
};
