<?php

namespace App\Livewire\Pss;

use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Services\PssManagementService;
use App\Models\PssImportacion;
use App\Models\PssImportacionDetalle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ImportWizard extends Component
{
    use WithFileUploads;

    public $file;
    public $storedFilePath;
    public $originalFileName;
    public $step = 1;
    public $sheets = [];
    public $selectedSheet;
    public $type = 'medicos'; 
    public $headers = [];
    public $mapping = [];
    public $previewData = [];
    public $totalRows = 0;
    public $isImporting = false;
    
    // Stats para el paso final
    public $stats = [
        'procesados' => 0,
        'duplicados' => 0,
        'errores' => 0,
        'omitidos' => 0
    ];

    public $expectedFields = [
        'medicos' => [
            'nombre' => 'Nombre Completo',
            'telefono_1' => 'Teléfono 1',
            'telefono_2' => 'Teléfono 2',
            'ciudad' => 'Ciudad',
            'especialidad' => 'Especialidad',
            'clinica' => 'Clínica/Centro'
        ],
        'centros' => [
            'nombre' => 'Nombre del Centro',
            'telefono_1' => 'Teléfono 1',
            'telefono_2' => 'Teléfono 2',
            'ciudad' => 'Ciudad',
            'grupo' => 'Grupo/Tipo'
        ]
    ];

    public function updatedFile()
    {
        $this->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240']);
        
        try {
            $this->originalFileName = $this->file->getClientOriginalName();
            // Especificar disco local para control total
            $this->storedFilePath = $this->file->store('pss_imports', 'local');
            $fullPath = Storage::disk('local')->path($this->storedFilePath);

            $reader = IOFactory::createReaderForFile($fullPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($fullPath);
            
            $this->sheets = $spreadsheet->getSheetNames();
            $this->selectedSheet = $this->sheets[0];
            $this->loadHeaders();
            $this->step = 2;
        } catch (\Exception $e) {
            $this->dispatch('notify', ['msg' => 'Error al leer el archivo: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function loadHeaders()
    {
        if (!$this->storedFilePath) return;

        $fullPath = Storage::disk('local')->path($this->storedFilePath);
        $reader = IOFactory::createReaderForFile($fullPath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($fullPath);
        $sheet = $spreadsheet->getSheetByName($this->selectedSheet);
        
        $highestColumn = $sheet->getHighestColumn();
        $this->headers = $sheet->rangeToArray('A1:' . $highestColumn . '1')[0];
        $this->totalRows = $sheet->getHighestRow() - 1;

        // Auto-mapping inteligente
        $this->mapping = [];
        foreach ($this->expectedFields[$this->type] as $field => $label) {
            $this->mapping[$field] = '';
            foreach ($this->headers as $index => $header) {
                $h = Str::lower(trim($header));
                if ($h == Str::lower($field) || 
                    Str::contains($h, Str::lower(str_replace('_', ' ', $field))) ||
                    Str::contains($h, Str::lower($label)) ||
                    ($field == 'nombre' && $h == 'nombre') ||
                    (Str::contains($field, 'telefono') && (Str::contains($h, 'telef') || Str::contains($h, 'tel'))) ||
                    ($field == 'clinica' && (Str::contains($h, 'clinica') || Str::contains($h, 'centro'))) ||
                    ($field == 'especialidad' && Str::contains($h, 'especialidad')) ||
                    ($field == 'grupo' && Str::contains($h, 'grupo')) ||
                    ($field == 'ciudad' && Str::contains($h, 'ciudad'))) {
                    $this->mapping[$field] = $index;
                    break;
                }
            }
        }
    }

    public function updatedType()
    {
        $this->loadHeaders();
    }

    public function generatePreview()
    {
        $fullPath = Storage::disk('local')->path($this->storedFilePath);
        $reader = IOFactory::createReaderForFile($fullPath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($fullPath);
        $sheet = $spreadsheet->getSheetByName($this->selectedSheet);
        
        $highestColumn = $sheet->getHighestColumn();
        $data = $sheet->rangeToArray('A2:' . $highestColumn . '21'); // Preview de 20 filas
        
        $this->previewData = [];
        $service = app(PssManagementService::class);

        foreach ($data as $row) {
            if (!array_filter($row)) continue;

            $mappedRow = [];
            foreach ($this->mapping as $field => $index) {
                if ($index !== '' && isset($row[$index])) {
                    $val = $row[$index];
                    if ($field == 'nombre') $val = $service->normalizeName($val);
                    if (Str::contains($field, 'telefono')) $val = $service->normalizePhone($val);
                    $mappedRow[$field] = $val;
                } else {
                    $mappedRow[$field] = null;
                }
            }
            $this->previewData[] = $mappedRow;
        }

        $this->step = 3;
    }

    public function processImport()
    {
        $this->isImporting = true;
        ini_set('memory_limit', '512M');
        set_time_limit(600);

        try {
            $fullPath = Storage::disk('local')->path($this->storedFilePath);

            $import = PssImportacion::create([
                'nombre_archivo' => $this->originalFileName,
                'tipo' => $this->type,
                'total_registros' => $this->totalRows,
                'user_id' => auth()->id(),
                'configuracion' => $this->mapping
            ]);

            $reader = IOFactory::createReaderForFile($fullPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($fullPath);
            $sheet = $spreadsheet->getSheetByName($this->selectedSheet);
            
            $highestColumn = $sheet->getHighestColumn();
            $highestRow = $sheet->getHighestRow();
            
            $service = app(PssManagementService::class);
            $this->stats = ['procesados' => 0, 'duplicados' => 0, 'errores' => 0, 'omitidos' => 0];

            for ($rowIdx = 2; $rowIdx <= $highestRow; $rowIdx++) {
                $row = $sheet->rangeToArray('A' . $rowIdx . ':' . $highestColumn . $rowIdx)[0];
                $filaNum = $rowIdx;

                if (!array_filter($row)) {
                    $this->stats['omitidos']++;
                    continue;
                }

                try {
                    $itemData = [];
                    foreach ($this->mapping as $field => $idx) {
                        if ($idx !== '' && isset($row[$idx])) {
                            $itemData[$field] = $row[$idx];
                        }
                    }

                    if ($this->type == 'medicos') {
                        $result = $service->importMedico($itemData, $import->id);
                    } else {
                        $result = $service->importCentro($itemData, $import->id);
                    }

                    PssImportacionDetalle::create([
                        'importacion_id' => $import->id,
                        'fila' => $filaNum,
                        'estado' => $result['status'],
                        'datos_originales' => json_encode($itemData),
                        'error_mensaje' => $result['status'] === 'error' ? ($result['message'] ?? 'Error desconocido') : ($result['status'] === 'duplicate' ? 'Registro duplicado' : null)
                    ]);

                    if ($result['status'] === 'success') $this->stats['procesados']++;
                    if ($result['status'] === 'duplicate') $this->stats['duplicados']++;
                    if ($result['status'] === 'error') $this->stats['errores']++;

                } catch (\Exception $e) {
                    $this->stats['errores']++;
                    PssImportacionDetalle::create([
                        'importacion_id' => $import->id,
                        'fila' => $filaNum,
                        'estado' => 'error',
                        'error_mensaje' => $e->getMessage()
                    ]);
                }
            }

            $import->update([
                'procesados' => $this->stats['procesados'],
                'duplicados' => $this->stats['duplicados'],
                'errores' => $this->stats['errores'],
                'omitidos' => $this->stats['omitidos'],
            ]);

            // Limpiar archivo
            Storage::disk('local')->delete($this->storedFilePath);
            $this->storedFilePath = null;

            $this->isImporting = false;
            $this->step = 4;
            $this->dispatch('notify', ['msg' => "Importación finalizada con éxito.", 'type' => 'success']);

        } catch (\Exception $e) {
            $this->isImporting = false;
            $this->dispatch('notify', ['msg' => "Error crítico: " . $e->getMessage(), 'type' => 'error']);
            \Log::error("PSS Import Error: " . $e->getMessage());
        }
    }

    public function resetWizard()
    {
        if ($this->storedFilePath) {
            Storage::disk('local')->delete($this->storedFilePath);
        }
        $this->reset(['file', 'storedFilePath', 'originalFileName', 'step', 'previewData', 'mapping', 'sheets', 'selectedSheet', 'stats']);
    }

    public function render()
    {
        return view('livewire.pss.import-wizard');
    }
}
