<?php

namespace App\Http\Controllers\Modules\Admin;

use App\Http\Controllers\Controller;
use App\Models\DispersionIndicator;
use App\Models\DispersionBajaType;
use App\Models\DispersionValue;
use App\Models\DispersionBajaValue;
use Illuminate\Support\Facades\DB;

class DispersionMigrationController extends Controller
{
    public function migrateData()
    {
        return DB::transaction(function () {
            // 1. Mapear Indicadores a Tipos de Baja
            $mappings = [
                'Pérdida de Empleo' => 'Pérdida de Empleo',
                'Separación o Divorcio' => 'Separación o Divorcio'
            ];

            $count = 0;

            foreach ($mappings as $indName => $bajaName) {
                $indicator = DispersionIndicator::where('name', 'like', "%$indName%")->first();
                $bajaType = DispersionBajaType::where('name', 'like', "%$bajaName%")->first();

                if ($indicator && $bajaType) {
                    // Buscar valores en la tabla de indicadores
                    $oldValues = DispersionValue::where('indicator_id', $indicator->id)->get();

                    foreach ($oldValues as $old) {
                        // Mover a la tabla de bajas si no existe ya
                        DispersionBajaValue::updateOrCreate(
                            [
                                'corte_id' => $old->corte_id,
                                'baja_type_id' => $bajaType->id
                            ],
                            [
                                'quantity' => $old->quantity
                            ]
                        );
                        
                        // Opcional: Eliminar el viejo para no duplicar en DB (aunque ya está oculto)
                        // $old->delete();
                        
                        $count++;
                    }
                }
            }

            return "Migración completada: $count registros movidos.";
        });
    }
}
