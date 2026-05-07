<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DispersionIndicator;
use App\Models\DispersionBajaType;

class DispersionCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Catálogo de Indicadores de Dispersión
        $indicators = [
            ['name' => 'Nuevos Titulares', 'code' => 'NUEVOS_TITULARES', 'category' => 'Afiliados', 'is_total' => false, 'order' => 1],
            ['name' => 'Nuevos Dependientes', 'code' => 'NUEVOS_DEPENDIENTES', 'category' => 'Afiliados', 'is_total' => false, 'order' => 2],
            ['name' => 'Total de Nuevos Afiliados', 'code' => 'TOTAL_NUEVOS', 'category' => 'Afiliados', 'is_total' => true, 'order' => 3],
            ['name' => 'Total de Titulares Dispersados', 'code' => 'TITULARES_DISPERSADOS', 'category' => 'Afiliados', 'is_total' => false, 'order' => 4],
            ['name' => 'Pagos por Pérdida de Empleos', 'code' => 'PAGOS_PERDIDA_EMPLEO', 'category' => 'Afiliados', 'is_total' => false, 'order' => 5],
            ['name' => 'Total Dependientes Dispersados', 'code' => 'DEPENDIENTES_DISPERSADOS', 'category' => 'Afiliados', 'is_total' => false, 'order' => 6],
            ['name' => 'Pago Separación o Divorcio', 'code' => 'PAGO_SEPARACION_DIVORCIO', 'category' => 'Afiliados', 'is_total' => false, 'order' => 7],
            ['name' => 'Total de Afiliados Dispersados', 'code' => 'TOTAL_DISPERSADOS', 'category' => 'Afiliados', 'is_total' => true, 'order' => 8],
            ['name' => 'Total General de Afiliados PDSS', 'code' => 'TOTAL_GENERAL_PDSS', 'category' => 'Afiliados', 'is_total' => false, 'order' => 9],
            ['name' => 'Monto de Dispersión 1er Corte', 'code' => 'MONTO_1ER_CORTE', 'category' => 'Montos', 'is_total' => false, 'order' => 10],
            ['name' => 'Monto de Dispersión 2do Corte', 'code' => 'MONTO_2DO_CORTE', 'category' => 'Montos', 'is_total' => false, 'order' => 11],
            ['name' => 'Total de Dispersión', 'code' => 'TOTAL_MONTO_DISPERSION', 'category' => 'Montos', 'is_total' => true, 'order' => 12],
        ];

        foreach ($indicators as $ind) {
            DispersionIndicator::updateOrCreate(
                ['code' => $ind['code']],
                [
                    'name' => $ind['name'],
                    'category' => $ind['category'],
                    'is_total' => $ind['is_total'],
                    'order_weight' => $ind['order'],
                    'is_active' => true
                ]
            );
        }

        // 2. Catálogo de Tipos de Bajas
        $bajas = [
            ['name' => 'Traspaso Ordinario', 'code' => 'TRASPASO_ORDINARIO', 'order' => 1],
            ['name' => 'Traspasos Unificación', 'code' => 'TRASPASOS_UNIFICACION', 'order' => 2],
            ['name' => 'Cambio por Excepción', 'code' => 'CAMBIO_EXCEPCION', 'order' => 3],
            ['name' => 'Pérdida de Empleo', 'code' => 'PERDIDA_EMPLEO', 'order' => 4],
            ['name' => 'Separación o Divorcio', 'code' => 'SEPARACION_DIVORCIO', 'order' => 5],
            ['name' => 'Otros', 'code' => 'OTROS', 'order' => 6],
        ];

        foreach ($bajas as $baja) {
            DispersionBajaType::updateOrCreate(
                ['code' => $baja['code']],
                [
                    'name' => $baja['name'],
                    'order_weight' => $baja['order'],
                    'is_active' => true
                ]
            );
        }
    }
}
