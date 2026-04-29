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
        Schema::table('afiliados', function (Blueprint $table) {
            $conn = Schema::getConnection();
            $dbName = $conn->getDatabaseName();
            
            // Verificación simple de índices existentes para evitar errores de duplicidad
            $indexes = $conn->select("SHOW INDEX FROM afiliados");
            $indexNames = array_map(fn($i) => $i->Key_name, $indexes);

            if (!in_array('afiliados_cedula_index', $indexNames)) $table->index('cedula');
            if (!in_array('afiliados_contrato_index', $indexNames)) $table->index('contrato');
            if (!in_array('afiliados_rnc_empresa_index', $indexNames)) $table->index('rnc_empresa');
            if (!in_array('afiliados_estado_id_index', $indexNames)) $table->index('estado_id');
            if (!in_array('afiliados_corte_id_index', $indexNames)) $table->index('corte_id');
            if (!in_array('afiliados_responsable_id_index', $indexNames)) $table->index('responsable_id');
            if (!in_array('afiliados_lote_id_index', $indexNames)) $table->index('lote_id');
            if (!in_array('afiliados_empresa_id_index', $indexNames)) $table->index('empresa_id');
            if (!in_array('afiliados_provincia_id_index', $indexNames)) $table->index('provincia_id');
            if (!in_array('afiliados_municipio_id_index', $indexNames)) $table->index('municipio_id');
            if (!in_array('afiliados_sexo_index', $indexNames)) $table->index('sexo');
            if (!in_array('afiliados_reasignado_index', $indexNames)) $table->index('reasignado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropIndex(['cedula']);
            $table->dropIndex(['contrato']);
            $table->dropIndex(['rnc_empresa']);
            $table->dropIndex(['estado_id']);
            $table->dropIndex(['corte_id']);
            $table->dropIndex(['responsable_id']);
            $table->dropIndex(['lote_id']);
            $table->dropIndex(['empresa_id']);
            $table->dropIndex(['provincia_id']);
            $table->dropIndex(['municipio_id']);
            $table->dropIndex(['sexo']);
            $table->dropIndex(['reasignado']);
        });
    }
};
