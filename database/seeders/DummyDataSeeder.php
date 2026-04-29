<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Corte;
use App\Models\Afiliado;
use App\Models\Estado;
use App\Models\Responsable;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $corte1 = Corte::create(['nombre' => 'Corte Febrero 2026', 'fecha_inicio' => '2026-02-01', 'fecha_fin' => '2026-02-28', 'activo' => true]);
        $corte2 = Corte::create(['nombre' => 'Corte Marzo 2026', 'fecha_inicio' => '2026-03-01', 'fecha_fin' => '2026-03-31', 'activo' => true]);

        $estados = Estado::all();
        $responsables = Responsable::all();

        for ($i = 1; $i <= 50; $i++) {
            $estado = $estados->random();
            $responsable = rand(0, 1) ? $responsables->random() : null;

            Afiliado::create([
                'corte_id' => rand(0, 1) ? $corte1->id : $corte2->id,
                'responsable_id' => $responsable ? $responsable->id : null,
                'estado_id' => $estado->id,
                'nombre_completo' => 'Afiliado Prueba ' . $i,
                'cedula' => rand(100, 999) . '-' . rand(1000000, 9999999) . '-' . rand(0, 9),
                'telefono' => '809-' . rand(100, 999) . '-' . rand(1000, 9999),
                'direccion' => 'Calle ' . $i . ' Numero ' . rand(1, 100),
                'provincia' => 'Santo Domingo',
                'municipio' => 'Distrito Nacional',
                'empresa' => 'Empresa Test S.A.',
                'contrato' => 'CT-' . rand(1000, 9999),
                'poliza' => 'PL-' . rand(1000, 9999),
            ]);
        }
    }
}
