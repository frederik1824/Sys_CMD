<?php

namespace App\Livewire\Modules\Admin\Dispersion;

use Livewire\Component;
use App\Models\DispersionCorte;
use App\Models\DispersionIndicator;
use App\Models\DispersionBajaType;
use App\Models\DispersionValue;
use App\Models\DispersionBajaValue;
use App\Imports\DispersionImport;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class CorteCapture extends Component
{
    use WithFileUploads;

    public $corteId;
    public $corteNumber;
    public $excelFile;
    public $values = []; // indicator_id => ['quantity' => x, 'amount' => y]
    public $bajaValues = []; // baja_type_id => quantity
    public $totals = [];
    public $receptionDate;
    public $notes;
    public $isClosed = false;

    public function mount($corteId, $corteNumber = 1)
    {
        $this->corteId = $corteId;
        $this->corteNumber = $corteNumber;
        $this->loadData();
    }

    public function loadData()
    {
        $corte = DispersionCorte::with(['values', 'bajaValues', 'period'])->findOrFail($this->corteId);
        $this->isClosed = $corte->status === 'closed' || $corte->period->status === 'closed';
        $this->receptionDate = $corte->reception_date?->format('Y-m-d');
        $this->notes = $corte->notes;

        // Cargar indicadores
        $indicators = DispersionIndicator::all();
        foreach ($indicators as $ind) {
            $val = $corte->values->where('indicator_id', $ind->id)->first();
            $key = 'id_' . $ind->id;
            $this->values[$key] = [
                'id' => $ind->id,
                'quantity' => $val->quantity ?? 0,
                'amount' => $val->amount ?? 0,
                'code' => $ind->code,
                'is_total' => $ind->is_total
            ];
        }

        // Cargar bajas
        $bajaTypes = DispersionBajaType::all();
        foreach ($bajaTypes as $bt) {
            $val = $corte->bajaValues->where('baja_type_id', $bt->id)->first();
            $key = 'id_' . $bt->id;
            $this->bajaValues[$key] = [
                'id' => $bt->id,
                'quantity' => $val->quantity ?? 0
            ];
        }

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        // 1. Total Nuevos Afiliados = Nuevos Titulares + Nuevos Dependientes
        $nuevosTitulares = (int)$this->getValueByCode('NUEVOS_TITULARES', 'quantity');
        $nuevosDependientes = (int)$this->getValueByCode('NUEVOS_DEPENDIENTES', 'quantity');
        $this->setValueByCode('TOTAL_NUEVOS', 'quantity', $nuevosTitulares + $nuevosDependientes);

        // 2. Total de Afiliados Dispersados = Titulares Dispersados + Dependientes Dispersados
        $titularesDisp = (int)$this->getValueByCode('TITULARES_DISPERSADOS', 'quantity');
        $dependientesDisp = (int)$this->getValueByCode('DEPENDIENTES_DISPERSADOS', 'quantity');
        $this->setValueByCode('TOTAL_DISPERSADOS', 'quantity', $titularesDisp + $dependientesDisp);

        // 3. Monto Total = Monto 1er Corte + Monto 2do Corte
        $monto1 = (float)$this->getValueByCode('MONTO_1ER_CORTE', 'amount');
        $monto2 = (float)$this->getValueByCode('MONTO_2DO_CORTE', 'amount');
        $this->setValueByCode('TOTAL_MONTO_DISPERSION', 'amount', $monto1 + $monto2);

        // 4. Total Bajas
        $totalBajas = 0;
        foreach($this->bajaValues as $bv) {
            $totalBajas += (int)($bv['quantity'] ?? 0);
        }
        $this->totals['bajas'] = $totalBajas;
    }

    private function getValueByCode($code, $field)
    {
        foreach ($this->values as $v) {
            if (($v['code'] ?? '') === $code) return $v[$field] ?? 0;
        }
        return 0;
    }

    private function setValueByCode($code, $field, $val)
    {
        foreach ($this->values as $key => $v) {
            if (($v['code'] ?? '') === $code) {
                $this->values[$key][$field] = $val;
            }
        }
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'values') || str_starts_with($propertyName, 'bajaValues')) {
            $this->calculateTotals();
        }
    }

    public function save()
    {
        if ($this->isClosed) return;

        try {
            $corte = DispersionCorte::findOrFail($this->corteId);
            $corte->update([
                'reception_date' => $this->receptionDate ?: null,
                'notes' => $this->notes,
                'status' => 'in_progress'
            ]);

            // Guardar valores dispersión
            foreach ($this->values as $data) {
                if (!isset($data['id'])) continue;
                
                DispersionValue::updateOrCreate(
                    ['corte_id' => $this->corteId, 'indicator_id' => $data['id']],
                    [
                        'quantity' => (int)($data['quantity'] ?? 0), 
                        'amount' => (float)($data['amount'] ?? 0)
                    ]
                );
            }

            // Guardar bajas
            foreach ($this->bajaValues as $data) {
                if (!isset($data['id'])) continue;

                DispersionBajaValue::updateOrCreate(
                    ['corte_id' => $this->corteId, 'baja_type_id' => $data['id']],
                    ['quantity' => (int)($data['quantity'] ?? 0)]
                );
            }

            session()->flash('message', 'Corte guardado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function importExcel()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            Excel::import(new DispersionImport($this->corteId), $this->excelFile->getRealPath());
            
            $this->loadData();
            $this->excelFile = null;
            
            session()->flash('message', 'Datos importados exitosamente desde Excel.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al importar Excel: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $indicators = DispersionIndicator::orderBy('order_weight')->get();
        $bajaTypes = DispersionBajaType::orderBy('order_weight')->get();

        return view('livewire.modules.admin.dispersion.corte-capture', [
            'indicators' => $indicators,
            'bajaTypes' => $bajaTypes
        ]);
    }
}
