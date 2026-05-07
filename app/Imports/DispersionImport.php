<?php

namespace App\Imports;

use App\Models\DispersionCorte;
use App\Models\DispersionIndicator;
use App\Models\DispersionBajaType;
use App\Models\DispersionValue;
use App\Models\DispersionBajaValue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;

class DispersionImport implements ToCollection
{
    protected $corteId;
    protected $indicators;
    protected $bajaTypes;

    public function __construct($corteId)
    {
        $this->corteId = $corteId;
        $this->indicators = DispersionIndicator::all();
        $this->bajaTypes = DispersionBajaType::all();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $label = trim($row[0] ?? '');
            if (empty($label)) continue;

            // 1. Buscar en Indicadores de Dispersión
            $indicator = $this->findIndicator($label);
            if ($indicator) {
                $value = $row[1] ?? 0;
                
                // Limpiar valor (quitar $, comas, etc.)
                $cleanValue = preg_replace('/[^0-9.]/', '', str_replace(',', '', (string)$value));
                
                if ($indicator->category === 'Montos') {
                    DispersionValue::updateOrCreate(
                        ['corte_id' => $this->corteId, 'indicator_id' => $indicator->id],
                        ['amount' => (float)$cleanValue, 'quantity' => 0]
                    );
                } else {
                    DispersionValue::updateOrCreate(
                        ['corte_id' => $this->corteId, 'indicator_id' => $indicator->id],
                        ['quantity' => (int)$cleanValue, 'amount' => 0]
                    );
                }
                continue;
            }

            // 2. Buscar en Tipos de Bajas
            $bajaType = $this->findBajaType($label);
            if ($bajaType) {
                $value = $row[1] ?? 0;
                $cleanValue = preg_replace('/[^0-9]/', '', str_replace(',', '', (string)$value));
                
                DispersionBajaValue::updateOrCreate(
                    ['corte_id' => $this->corteId, 'baja_type_id' => $bajaType->id],
                    ['quantity' => (int)$cleanValue]
                );
            }
        }
    }

    protected function findIndicator($label)
    {
        $labelNorm = Str::slug($label);
        foreach ($this->indicators as $ind) {
            if (Str::slug($ind->name) === $labelNorm || str_contains($labelNorm, Str::slug($ind->name))) {
                return $ind;
            }
        }
        return null;
    }

    protected function findBajaType($label)
    {
        $labelNorm = Str::slug($label);
        foreach ($this->bajaTypes as $bt) {
            if (Str::slug($bt->name) === $labelNorm || str_contains($labelNorm, Str::slug($bt->name))) {
                return $bt;
            }
        }
        return null;
    }
}
