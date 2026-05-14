<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;
use App\Models\SystemUpdate;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class NexusReleaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            echo "--- Iniciando Despliegue de Release 3.1.0 ---\n";

            // 1. Departamentos Requeridos
            $depts = [
                ['nombre' => 'Call Center', 'codigo' => 'CC'],
                ['nombre' => 'Autorizaciones Médicas', 'codigo' => 'AUTOR']
            ];

            foreach ($depts as $d) {
                Departamento::firstOrCreate(['nombre' => $d['nombre']], ['codigo' => $d['codigo']]);
                echo "- Departamento '{$d['nombre']}': Configurado.\n";
            }

            // 2. Roles Requeridos
            $roles = [
                'Representante de Autorizaciones',
                'Operador de Enlaces',
                'Supervisor Call Center'
            ];

            foreach ($roles as $r) {
                Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
                echo "- Rol '{$r}': Configurado.\n";
            }

            // 3. Registro de Versión del Sistema
            $version = '3.1.0';
            $build = (int) date('Ymd');
            
            SystemUpdate::create([
                'version' => $version,
                'build_number' => $build,
                'type' => 'Major Release',
                'changelog' => "- Rediseño total del Portal (Nexus CMD V3).\n" .
                               "- Implementación del Módulo de Enlaces Médicos.\n" .
                               "- Unificación de Póliza como identificador maestro.\n" .
                               "- Sistema de privacidad individual para representantes.\n" .
                               "- Panel de Notas Críticas para Call Center.",
                'status' => 'completed',
                'started_at' => now(),
                'completed_at' => now(),
                'executed_by' => 1 // Administrador
            ]);

            echo "- Versión del Sistema: Actualizada a {$version} (Build {$build}).\n";

            DB::commit();
            echo "--- Release 3.1.0 Desplegada con Éxito ---\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "ERROR EN RELEASE: " . $e->getMessage() . "\n";
        }
    }
}
