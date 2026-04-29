<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add column to empresas
        Schema::table('empresas', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        // 2. Add column to afiliados
        Schema::table('afiliados', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        // 3. Populate UUIDs for existing records
        DB::table('empresas')->whereNull('uuid')->chunkById(100, function ($empresas) {
            foreach ($empresas as $empresa) {
                DB::table('empresas')->where('id', $empresa->id)->update(['uuid' => (string) Str::uuid()]);
            }
        });

        DB::table('afiliados')->whereNull('uuid')->chunkById(100, function ($afiliados) {
            foreach ($afiliados as $afiliado) {
                DB::table('afiliados')->where('id', $afiliado->id)->update(['uuid' => (string) Str::uuid()]);
            }
        });

        // 4. Make columns non-nullable after population
        Schema::table('empresas', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });

        Schema::table('afiliados', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
