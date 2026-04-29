<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SolicitudAfiliacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'nombre' => 'Afiliación de titular',
                'descripcion' => 'Proceso para dar de alta a un nuevo titular en el sistema.',
                'sla_horas' => 48,
                'documentos' => [
                    ['nombre' => 'Cédula del titular', 'obligatorio' => true],
                    ['nombre' => 'Formulario de afiliación firmado', 'obligatorio' => true],
                    ['nombre' => 'Certificación laboral o soporte', 'obligatorio' => true],
                    ['nombre' => 'Documento de identidad adicional', 'obligatorio' => false],
                ]
            ],
            [
                'nombre' => 'Afiliación de dependiente',
                'descripcion' => 'Inclusión de nuevos dependientes a un titular existente.',
                'sla_horas' => 24,
                'documentos' => [
                    ['nombre' => 'Cédula del titular', 'obligatorio' => true],
                    ['nombre' => 'Cédula o acta del dependiente', 'obligatorio' => true],
                    ['nombre' => 'Soporte de parentesco', 'obligatorio' => true],
                    ['nombre' => 'Formulario de inclusión firmado', 'obligatorio' => true],
                ]
            ],
            [
                'nombre' => 'Novedades de afiliación',
                'descripcion' => 'Cambios generales en los datos del afiliado.',
                'sla_horas' => 24,
                'documentos' => [
                    ['nombre' => 'Cédula', 'obligatorio' => true],
                    ['nombre' => 'Formulario de novedad', 'obligatorio' => true],
                    ['nombre' => 'Soporte documental de la novedad', 'obligatorio' => true],
                ]
            ],
            [
                'nombre' => 'Corrección de datos',
                'descripcion' => 'Subsanación de errores en la información registrada.',
                'sla_horas' => 12,
                'documentos' => [
                    ['nombre' => 'Cédula', 'obligatorio' => true],
                    ['nombre' => 'Soporte del dato correcto', 'obligatorio' => true],
                ]
            ],
            [
                'nombre' => 'Exclusión de dependiente',
                'descripcion' => 'Retiro de dependientes de la póliza del titular.',
                'sla_horas' => 24,
                'documentos' => [
                    ['nombre' => 'Solicitud firmada', 'obligatorio' => true],
                    ['nombre' => 'Cédula del titular', 'obligatorio' => true],
                ]
            ]
        ];

        foreach ($tipos as $t) {
            $tipo = \App\Models\TipoSolicitudAfiliacion::create([
                'nombre' => $t['nombre'],
                'descripcion' => $t['descripcion'],
                'sla_horas' => $t['sla_horas']
            ]);

            foreach ($t['documentos'] as $d) {
                \App\Models\DocumentoRequeridoSolicitud::create([
                    'tipo_solicitud_id' => $tipo->id,
                    'nombre_documento' => $d['nombre'],
                    'obligatorio' => $d['obligatorio']
                ]);
            }
        }
    }
}
