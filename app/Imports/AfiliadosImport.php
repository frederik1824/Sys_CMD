<?php

namespace App\Imports;

use App\Models\Afiliado;
use App\Models\Estado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AfiliadosImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError, WithMapping, WithEvents, ShouldQueue, WithChunkReading
{
    use SkipsFailures, SkipsErrors, RemembersRowNumber;

    protected $corte_id;
    protected $empresa_tipo;
    protected $lote_id;
    protected $responsable_id;
    protected $estado_pendiente_id;

    public function __construct($corte_id, $empresa_tipo, $lote_id = null, $responsable_id = null)
    {
        $this->corte_id = $corte_id;
        $this->empresa_tipo = $empresa_tipo;
        $this->lote_id = $lote_id;
        $this->responsable_id = $responsable_id;
        $this->estado_pendiente_id = Estado::where('nombre', 'Pendiente')->value('id');
    }

    public function map($row): array
    {
        // El slugificador de Maatwebsite maneja acentos y espacios.
        // NÚMERO DE CONTRATO -> numero_de_contrato
        // PÓLIZA / CERTIFICADO -> poliza_certificado
        
        $row['nombre_completo'] = $row['nombre_completo'] ?? ($row['nombre_completo_'] ?? ($row['nombre'] ?? null));
        $row['cedula']          = isset($row['cedula']) ? (string)$row['cedula'] : ($row['cedula_'] ?? '');
        $row['rnc_empresa']      = $row['rnc_empresa'] ?? ($row['rnc_empresa_'] ?? null);
        
        $row['poliza']   = $row['poliza'] ?? ($row['poliza_certificado'] ?? ($row['poliza_'] ?? null));
        $row['contrato'] = $row['contrato'] ?? ($row['numero_de_contrato'] ?? ($row['contrato_'] ?? null));

        // Casting a string para evitar fallos de formato numérico de Excel
        $row['cedula']      = (string)$row['cedula'];
        $row['rnc_empresa'] = $row['rnc_empresa'] ? (string)$row['rnc_empresa'] : null;
        $row['poliza']      = $row['poliza'] ? (string)$row['poliza'] : null;
        $row['contrato']    = $row['contrato'] ? (string)$row['contrato'] : null;
        $row['telefono']    = isset($row['telefono']) ? (string)$row['telefono'] : null;

        return $row;
    }

    public function model(array $row)
    {
        // Protección contra filas vacías (zombis de Excel)
        if (empty($row['cedula']) && empty($row['nombre_completo'])) {
            return null;
        }

        // Actualizar progreso cada N filas para no sobrecargar
        if ($this->getRowNumber() % 5 === 0) {
            $this->updateProgress();
            $this->logEvent("Procesando bloque hasta fila " . $this->getRowNumber());
        }

        // El id de estado pendiente o un default si no existe
        $estado_id = $this->estado_pendiente_id ?? 1;

        // Regla: Actualizar si ya existe por Cédula (Global, no solo por Corte)
        $afiliado = Afiliado::where('cedula', $row['cedula'])
            ->orderBy('created_at', 'desc')
            ->first();

        $esNuevo = !$afiliado;

        if ($esNuevo) {
            $this->logEvent("- Cédula {$row['cedula']}: Nuevo registro");
            $afiliado = new Afiliado([
                'cedula' => $row['cedula'], 
                'corte_id' => $this->corte_id
            ]);
            $afiliado->estado_id = $estado_id;

            // REGLA 1: Sugerencia de Blindaje contra Pagos Duplicados Históricos
            $historia = Afiliado::historialEntrega($row['cedula'])->first();
            if ($historia) {
                $procesados = Cache::get("import_duplicados_{$this->lote_id}", []);
                $procesados[] = [
                    'fila'   => $this->getRowNumber(),
                    'cedula' => $row['cedula'],
                    'nombre' => $row['nombre_completo'] . ' [ALERTA: YA ENTREGADO EN CORTE ' . ($historia->corte->nombre ?? 'DESCONOCIDO') . ']'
                ];
                Cache::put("import_duplicados_{$this->lote_id}", $procesados, now()->addHours(1));
            }

        } else {
            // Evaluamos cambios específicos para reportar en el resumen
            $cambios = [];
            
            // Si el Corte es diferente, lo movemos al nuevo Corte (decisión de unificación de data)
            if ($afiliado->corte_id != $this->corte_id) {
                $afiliado->corte_id = $this->corte_id;
                $cambios[] = "Corte";
            }

            // Comparación de Responsable (Forzamos cast a int para evitar fallos de tipo string vs int)
            $newResp = $this->responsable_id ? (int)$this->responsable_id : null;
            $oldResp = $afiliado->responsable_id ? (int)$afiliado->responsable_id : null;
            
            if ($newResp && $oldResp !== $newResp) {
                $cambios[] = "Responsable";
            }
            
            // Comparación de Nombre (Normalizada)
            if (trim(strtolower($afiliado->nombre_completo)) !== trim(strtolower($row['nombre_completo']))) {
                $cambios[] = "Nombre";
            }

            // Comparación de Contrato/Póliza (Si se enviaron en el Excel)
            if (!empty($row['contrato']) && $afiliado->contrato != $row['contrato']) {
                $cambios[] = "Contrato";
            }
            
            $tipoMsg = (count($cambios) > 0) ? "Actualizado: " . implode(", ", $cambios) : "Sin cambios";

            $procesados = Cache::get("import_duplicados_{$this->lote_id}", []);
            $procesados[] = [
                'fila'   => $this->getRowNumber(),
                'cedula' => $row['cedula'],
                'nombre' => $row['nombre_completo'] . ' (' . $tipoMsg . ')'
            ];
            Cache::put("import_duplicados_{$this->lote_id}", $procesados, now()->addHours(1));
        }

        // Asignación de campos
        $afiliado->nombre_completo = $row['nombre_completo'];
        $afiliado->direccion       = $row['direccion'] ?? null;
        $afiliado->telefono        = $row['telefono'] ?? null;
        $afiliado->lote_id         = $this->lote_id;

        // Sugerencia 3: Estandarización de Direcciones
        $afiliado->normalizeAddress();
        
        if ($this->responsable_id) {
            $afiliado->responsable_id = $this->responsable_id;
        }
        
        // Lógica de Empresa (Detección o Creación) prioritizando RNC
        $rncClean = empty($row['rnc_empresa']) ? null : trim((string)$row['rnc_empresa']);
        $empresaExcel = $row['empresa'] ?? ($row['nombre_empresa'] ?? ($row['razon_social'] ?? null));
        $empresaNombre = '';
        
        if ($this->empresa_tipo === 'CMD') {
            // Si es CMD y no hay empresa, lo marcamos como pendiente en lugar de asumir ARS CMD
            $empresaNombre = $empresaExcel ? trim($empresaExcel) : 'PENDIENTE DE EMPRESA';
        } else {
            $empresaExcelBase = $empresaExcel ?? ($esNuevo ? 'SIN EMPRESA' : $afiliado->empresa);
            $empresaNombre = trim($empresaExcelBase);
        }

        // 1. Intentar por RNC si existe
        $empresaModel = null;
        if ($rncClean) {
            $empresaModel = \App\Models\Empresa::where('rnc', $rncClean)->first();
        }

        // 2. Si no hay por RNC (o no se proporcionó), buscar o crear por Nombre
        if (!$empresaModel) {
            $geo = $this->resolveGeographics($row['provincia'] ?? null, $row['municipio'] ?? null);
            
            $empresaModel = \App\Models\Empresa::firstOrCreate(
                ['nombre' => $empresaNombre],
                [
                    'rnc' => $rncClean,
                    'direccion' => $row['direccion'] ?? null,
                    'telefono' => $row['telefono'] ?? null,
                    'provincia' => $row['provincia'] ?? null,
                    'municipio' => $row['municipio'] ?? null,
                    'provincia_id' => $geo['provincia_id'],
                    'municipio_id' => $geo['municipio_id'],
                ]
            );
        } else {
            // Si la empresa EXISTE pero no tiene ubicación, la intentamos completar
            if (!$empresaModel->provincia_id) {
                $geo = $this->resolveGeographics($row['provincia'] ?? null, $row['municipio'] ?? null);
                if ($geo['provincia_id']) {
                    $empresaModel->update([
                        'provincia_id' => $geo['provincia_id'],
                        'municipio_id' => $geo['municipio_id'],
                        'provincia' => $row['provincia'] ?? $empresaModel->provincia,
                        'municipio' => $row['municipio'] ?? $empresaModel->municipio,
                    ]);
                }
            }
        }
        
        $afiliado->empresa_id  = $empresaModel->id;
        $afiliado->empresa     = $empresaModel->nombre; 
        $afiliado->rnc_empresa = $empresaModel->rnc;

        // Propagar ubicación a afiliado también
        $afiliado->provincia = $row['provincia'] ?? null;
        $afiliado->municipio = $row['municipio'] ?? null;

        $geoAf = $this->resolveGeographics($afiliado->provincia, $afiliado->municipio);
        $afiliado->provincia_id = $geoAf['provincia_id'];
        $afiliado->municipio_id = $geoAf['municipio_id'];

        // Auto-asignación de Operador/Representante geográfico si el admin no seleccionó uno
        if (!$afiliado->responsable_id && $afiliado->provincia_id) {
            $responsableGeo = \Illuminate\Support\Facades\DB::table('provincia_responsable')
                ->where('provincia_id', $afiliado->provincia_id)
                ->inRandomOrder() // Si hay varias en la misma provincia, Round-Robin simple
                ->first();
                
            if ($responsableGeo) {
                $afiliado->responsable_id = $responsableGeo->responsable_id;
            }
        }

        $afiliado->nombre_completo = $row['nombre_completo'];
        $afiliado->sexo            = isset($row['sexo']) ? strtoupper($row['sexo']) : ($esNuevo ? null : $afiliado->sexo);
        
        // Protección de datos sensibles/verificados si el afiliado ya existía
        if ($esNuevo) {
            $afiliado->telefono  = $row['telefono'] ?? null;
            $afiliado->direccion = $row['direccion'] ?? null;
        } else {
            // Solo actualizamos si en BD estaba vacío
            $afiliado->telefono  = empty($afiliado->telefono) ? ($row['telefono'] ?? null) : $afiliado->telefono;
            $afiliado->direccion = empty($afiliado->direccion) ? ($row['direccion'] ?? null) : $afiliado->direccion;
        }

        $afiliado->provincia       = $row['provincia'] ?? ($esNuevo ? null : $afiliado->provincia);
        $afiliado->municipio       = $row['municipio'] ?? ($esNuevo ? null : $afiliado->municipio);

        $afiliado->observaciones   = $row['observaciones'] ?? $afiliado->observaciones;
        $afiliado->contrato        = $row['contrato'] ?? $afiliado->contrato;
        $afiliado->poliza          = $row['poliza'] ?? $afiliado->poliza;

        $this->updateProgress();

        // Si ya existía, lo guardamos explícitamente y retornamos null para que Maatwebsite no intente un INSERT duplicado
        if (!$esNuevo) {
            /** @var Afiliado $afiliado */
            $afiliado->fill($row); // Actualizar datos
            $afiliado->corte_id = $this->corte_id; // Forzar el nuevo corte seleccionado
            $afiliado->lote_id = $this->lote_id; // Vincular al nuevo lote
            $afiliado->save();
            
            $this->logEvent("- Cédula {$row['cedula']}: Actualizado y Movido al nuevo Corte");
            return null;
        }

        return $afiliado;
    }

    private function updateProgress()
    {
        Cache::put("import_progress_{$this->lote_id}", $this->getRowNumber(), now()->addHours(1));
    }

    private function logEvent(string $message)
    {
        $logs = Cache::get("import_logs_{$this->lote_id}", []);
        $time = date('H:i:s');
        array_unshift($logs, "[$time] $message");
        $logs = array_slice($logs, 0, 15); // Mantener solo los últimos 15 eventos
        Cache::put("import_logs_{$this->lote_id}", $logs, now()->addHours(1));
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();
                $totalCount = array_values($totalRows)[0] - 1; 
                Cache::put("import_total_{$this->lote_id}", $totalCount, now()->addHours(1));
                Cache::put("import_progress_{$this->lote_id}", 0, now()->addHours(1));
                Cache::put("import_duplicados_{$this->lote_id}", [], now()->addHours(1));
                Cache::put("import_status_{$this->lote_id}", 'processing', now()->addHours(1));
            },
            AfterImport::class => function (AfterImport $event) {
                $processed = Cache::get("import_progress_{$this->lote_id}", 0);
                
                // Forzamos el estado completado y actualizamos el total real
                Cache::put("import_status_{$this->lote_id}", 'completed', now()->addHours(1));
                Cache::put("import_total_{$this->lote_id}", $processed, now()->addHours(1));
                
                $lote = \App\Models\Lote::find($this->lote_id);
                if ($lote) {
                    $lote->update(['total_registros' => $processed]);
                }
                
                $this->logEvent(">>> CARGA LOCAL FINALIZADA COMPLETAMENTE (" . $processed . " registros)");
                $this->logEvent(">>> Pendiente: Sincronización con Nube (Firebase) en segundo plano.");
            },
        ];
    }

    public function rules(): array
    {
        return [
            'nombre_completo' => ['required', 'string', 'max:255'],
            'cedula'          => ['required', 'string', 'max:20'],
            'sexo'            => ['nullable', 'in:M,F,m,f'],
            'rnc_empresa'     => ['nullable', 'string', 'max:20'],
            'contrato'        => ['nullable', 'string', 'max:100'],
            'poliza'          => ['nullable', 'string', 'max:100'],
        ];
    }

    private function resolveGeographics($provText, $muniText)
    {
        $provenciaId = null;
        $municipioId = null;

        // Limpieza básica de disparates comunes de Excel
        $ignore = ['#N/D', 'N/A', '', 'NULL', 'UNDEFINED', '0'];
        $provText = !in_array(strtoupper(trim($provText ?? '')), $ignore) ? trim($provText) : null;
        $muniText = !in_array(strtoupper(trim($muniText ?? '')), $ignore) ? trim($muniText) : null;

        if ($provText) {
            $provName = strtoupper($provText);
            // Búsqueda más flexible
            $provincia = \App\Models\Provincia::where('nombre', $provName)
                ->orWhere('nombre', 'like', $provName)
                ->first() ?: \App\Models\Provincia::create(['nombre' => $provName]);
                
            $provenciaId = $provincia->id;

            if ($muniText) {
                $muniName = strtoupper($muniText);
                $municipio = \App\Models\Municipio::where('nombre', $muniName)
                    ->where('provincia_id', $provenciaId)
                    ->first() ?: \App\Models\Municipio::create([
                        'provincia_id' => $provenciaId,
                        'nombre' => $muniName
                    ]);
                $municipioId = $municipio->id;
            }
        } elseif ($muniText) {
            // Inferencia: Intentar encontrar provincia por municipio
            $muniName = strtoupper($muniText);
            $existingMuni = \App\Models\Municipio::where('nombre', $muniName)->first();
            if ($existingMuni) {
                $municipioId = $existingMuni->id;
                $provenciaId = $existingMuni->provincia_id;
            }
        }

        return [
            'provincia_id' => $provenciaId,
            'municipio_id' => $municipioId
        ];
    }
}
