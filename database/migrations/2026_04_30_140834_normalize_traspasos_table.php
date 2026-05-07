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
        // 1. Crear los estados básicos primero si no existen (Seed manual en migración para integridad)
        $estados = [
            ['nombre' => 'En Proceso', 'slug' => 'proceso', 'color' => 'blue', 'icono' => 'potted_plant'],
            ['nombre' => 'Rechazado', 'slug' => 'rechazado', 'color' => 'rose', 'icono' => 'cancel'],
            ['nombre' => 'Efectivo', 'slug' => 'efectivo', 'color' => 'emerald', 'icono' => 'check_circle'],
            ['nombre' => 'Emitido', 'slug' => 'emitido', 'color' => 'violet', 'icono' => 'badge'],
        ];

        foreach ($estados as $est) {
            \DB::table('estado_traspasos')->updateOrInsert(['slug' => $est['slug']], $est);
        }

        Schema::table('traspasos', function (Blueprint $table) {
            $table->foreignId('agente_id')->nullable()->after('agente')->constrained('agente_traspasos');
            $table->foreignId('estado_id')->nullable()->after('estado')->constrained('estado_traspasos');
            
            // Renombrar columnas originales para no perder data durante la transición
            $table->renameColumn('agente', 'agente_legacy');
            $table->renameColumn('estado', 'estado_legacy');
        });

        // 2. MIGRACIÓN DE DATOS (DATA MIGRATION)
        // Mapear agentes por nombre
        $agentes = \DB::table('agente_traspasos')->pluck('id', 'nombre');
        $estadosMap = \DB::table('estado_traspasos')->pluck('id', 'slug');

        $traspasos = \DB::table('traspasos')->get();
        foreach ($traspasos as $t) {
            $update = [];
            
            // Mapear Agente
            if (isset($agentes[$t->agente_legacy])) {
                $update['agente_id'] = $agentes[$t->agente_legacy];
            }

            // Mapear Estado
            $statusLegacy = strtoupper($t->estado_legacy);
            if (str_contains($statusLegacy, 'RE')) {
                $update['estado_id'] = $estadosMap['rechazado'];
            } elseif (str_contains($statusLegacy, 'EN')) {
                $update['estado_id'] = $estadosMap['proceso'];
            } elseif (str_contains($statusLegacy, 'EFEC')) {
                $update['estado_id'] = $estadosMap['efectivo'];
            }

            if (!empty($update)) {
                \DB::table('traspasos')->where('id', $t->id)->update($update);
            }
        }
    }

    public function down(): void
    {
        Schema::table('traspasos', function (Blueprint $table) {
            $table->renameColumn('agente_legacy', 'agente');
            $table->renameColumn('estado_legacy', 'estado');
            $table->dropForeign(['agente_id']);
            $table->dropForeign(['estado_id']);
            $table->dropColumn(['agente_id', 'estado_id']);
        });
    }
};
