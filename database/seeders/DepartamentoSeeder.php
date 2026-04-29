<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        $deps = [
            [
                'nombre' => 'Afiliación',
                'codigo' => 'AFIL',
                'descripcion' => 'Departamento responsable de la gestión de solicitudes de afiliación, inclusiones y exclusiones.'
            ],
            [
                'nombre' => 'Servicio al Cliente',
                'codigo' => 'SC',
                'descripcion' => 'Punto de contacto inicial para recibir solicitudes de afiliados y empresas.'
            ],
            [
                'nombre' => 'Autorizaciones Médicas',
                'codigo' => 'AUTOR',
                'descripcion' => 'Gestión de autorizaciones de servicios de salud y procedimientos.'
            ],
            [
                'nombre' => 'Tecnología (IT)',
                'codigo' => 'IT',
                'descripcion' => 'Soporte técnico y desarrollo de sistemas internos.'
            ],
            [
                'nombre' => 'Cuentas Médicas',
                'codigo' => 'CMED',
                'descripcion' => 'Auditoría y procesamiento de facturación de prestadores.'
            ],
        ];

        foreach ($deps as $dep) {
            Departamento::updateOrCreate(['codigo' => $dep['codigo']], $dep);
        }
    }
}
