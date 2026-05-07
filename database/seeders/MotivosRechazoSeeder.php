<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MotivoRechazoTraspaso;

class MotivosRechazoSeeder extends Seeder
{
    public function run(): void
    {
        $motivos = [
            ['37', '1915', 'TRASPASO RECHAZADO POR AFILIADO ENCONTRARSE EN UN PROCESO DE AFILIACION COMO PENSIONADO'],
            ['50', '10657', 'Afiliado(a) no expresó su consentimiento de traspaso en el video'],
            ['51', '10658', 'Video sin audio'],
            ['52', '10659', 'Número telefónico o correo electrónico no pertenece al afiliado(a)'],
            ['53', '10660', 'Número de teléfono del afiliado(a) inválido'],
            ['54', '10661', 'Falta imagen de cédula'],
            ['55', '10662', 'Cédula no coincide datos del afiliado(a)'],
            ['56', '10663', 'Cédula no se visualiza correctamente (ilegible)'],
            ['58', '10665', 'Sexo del representante que orienta al afiliado en el video, es diferente al del representante que ingresó a la aplicación.'],
            ['59', '10666', 'Carnet Migración vencido (no válido).'],
            ['61', '10707', 'SCRIPT DEL CONSENTIMIENTO UTILIZADO POR EL AFILIADO(A) ES INCORRECTO']
        ];

        MotivoRechazoTraspaso::truncate();

        foreach ($motivos as $row) {
            MotivoRechazoTraspaso::create([
                'codigo_sisalril' => $row[0],
                'codigo_unsigima' => $row[1],
                'descripcion' => $row[2],
                'activo' => true
            ]);
        }
    }
}
