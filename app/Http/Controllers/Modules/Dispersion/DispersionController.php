<?php

namespace App\Http\Controllers\Modules\Dispersion;

use App\Http\Controllers\Controller;
use App\Models\Modules\Dispersion\DispersionCarga;
use App\Models\Modules\Dispersion\DispersionTitular;
use App\Models\Modules\Dispersion\DispersionDependiente;
use App\Models\Modules\Dispersion\DispersionLog;
use App\Services\Modules\Dispersion\DispersionParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Imports\PensionadoMasterImport;
use App\Exports\PensionadoMasterExport;
use Maatwebsite\Excel\Facades\Excel;

class DispersionController extends Controller
{
    protected $parser;

    public function __construct(DispersionParserService $parser)
    {
        $this->parser = $parser;
    }

    public function index()
    {
        $ultimasCargas = DispersionCarga::with('user')
            ->orderBy('fecha_carga', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'total_dispersado' => DispersionCarga::where('estado', 'Completado')->sum('monto_total_dispersado'),
            'total_pensionados' => DispersionCarga::where('estado', 'Completado')->sum('total_titulares'),
            'total_dependientes' => DispersionCarga::where('estado', 'Completado')->sum('total_dependientes'),
            'cargas_pendientes' => DispersionCarga::where('estado', 'Pendiente')->count(),
        ];

        return view('modules.admin.dispersion.pensionados.index', compact('ultimasCargas', 'stats'));
    }

    public function history()
    {
        $cargas = DispersionCarga::with('user')
            ->orderBy('periodo', 'desc')
            ->paginate(15);

        return view('modules.admin.dispersion.pensionados.history', compact('cargas'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'archivo_txt' => 'required|file|mimes:txt|max:51200', // 50MB max
            'periodo' => 'required|string|size:7', // YYYY-MM
        ]);

        $file = $request->file('archivo_txt');
        $path = $file->store('dispersion/pensionados', 'public');
        $hash = hash_file('sha256', $file->getRealPath());

        // Evitar duplicados por Hash
        $duplicate = DispersionCarga::where('hash_archivo', $hash)->first();
        if ($duplicate) {
            return back()->with('error', "Este archivo ya fue cargado previamente el {$duplicate->fecha_carga->format('d/m/Y')}.");
        }

        try {
            $carga = DispersionCarga::create([
                'periodo' => $request->periodo,
                'fecha_carga' => now(),
                'user_id' => auth()->id(),
                'nombre_archivo' => $file->getClientOriginalName(),
                'archivo_path' => $path,
                'hash_archivo' => $hash,
                'estado' => 'Procesando',
            ]);

            // Iniciar procesamiento (Batching)
            $this->processFile($carga);

            return redirect()->route('dispersion.pensionados.show', $carga->uuid)
                ->with('success', 'Archivo procesado correctamente.');

        } catch (Exception $e) {
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    protected function processFile(DispersionCarga $carga)
    {
        $filePath = storage_path('app/public/' . $carga->archivo_path);
        $content = file_get_contents($filePath);
        
        // Normalize line endings and split
        $lines = preg_split('/\r\n|\r|\n/', $content);
        
        $titularesCount = 0;
        $dependientesCount = 0;
        $montoTotal = 0;
        $montoSalud = 0;
        $montoCapita = 0;

        DB::transaction(function() use ($lines, $carga, &$titularesCount, &$dependientesCount, &$montoTotal, &$montoSalud, &$montoCapita) {
            $lastTitular = null;

            foreach ($lines as $index => $line) {
                if (empty(trim($line))) continue;

                $parsed = $this->parser->parseLine($line);
                if (!$parsed) {
                    DispersionLog::create([
                        'carga_id' => $carga->id,
                        'tipo' => 'warning',
                        'mensaje' => "Línea " . ($index + 1) . " ignorada: Formato de registro no reconocido.",
                        'detalles' => substr($line, 0, 50) . "..."
                    ]);
                    continue;
                }

                if ($parsed['type'] === 'D') {
                    $data = $parsed['data'];
                    $tipoAfiliado = $data['tipo_afiliado']; // T, D, A

                    if ($tipoAfiliado === 'T') {
                        $titular = DispersionTitular::create([
                            'carga_id' => $carga->id,
                            'tipo_afiliado' => 'T',
                            'nss' => $data['nss_titular'],
                            'cedula' => $data['cedula_titular'],
                            'codigo_pensionado' => $data['codigo_pensionado'],
                            'tipo_pensionado' => $data['tipo_pensionado'],
                            'origen_pension' => $data['origen_pension'],
                            'periodo' => $carga->periodo,
                            'raw_line' => $line,
                            'hash_integridad' => hash('sha1', $line),
                            // Valores financieros por defecto (No presentes en layout)
                            'salario' => 0,
                            'monto_descuento_salud' => 0,
                            'monto_capita_adicional' => 0,
                            'monto_total' => 0
                        ]);
                        
                        $lastTitular = $titular;
                        $titularesCount++;

                    } elseif ($tipoAfiliado === 'D' || $tipoAfiliado === 'A') {
                        DispersionDependiente::create([
                            'carga_id' => $carga->id,
                            'titular_id' => $lastTitular?->id,
                            'cedula_titular' => $data['cedula_titular'],
                            'nss_titular' => $data['nss_titular'],
                            'codigo_pensionado' => $data['codigo_pensionado'],
                            'cedula_dependiente' => $data['cedula_dependiente'],
                            'nss_dependiente' => $data['nss_dependiente'],
                            'tipo_pensionado' => $data['tipo_pensionado'],
                            'origen_pension' => $data['origen_pension'],
                            'periodo' => $carga->periodo,
                            'raw_line' => $line,
                            'hash_integridad' => hash('sha1', $line)
                        ]);
                        $dependientesCount++;
                    }
                }
            }

            $carga->update([
                'total_registros' => $titularesCount + $dependientesCount,
                'total_titulares' => $titularesCount,
                'total_dependientes' => $dependientesCount,
                'monto_total_salud' => 0,
                'monto_total_capita' => 0,
                'monto_total_dispersado' => 0,
                'estado' => 'Completado'
            ]);

            // Disparar validación automática de solicitudes de afiliación
            $valService = app(\App\Services\Modules\Dispersion\PensionadoValidationService::class);
            $valService->validatePendingRequests();
            $valService->syncMasterWithDispersion($carga->id);
        });
    }

    public function storeMaster(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string|unique:dispersion_pensionados_master,cedula',
            'nombre_completo' => 'required|string',
            'tipo_pension' => 'required|string',
            'nss' => 'nullable|string',
            'institucion_pension' => 'nullable|string'
        ]);

        \App\Models\Modules\Dispersion\PensionadoMaster::create([
            'cedula' => $request->cedula,
            'nombre_completo' => $request->nombre_completo,
            'tipo_pension' => $request->tipo_pension,
            'nss' => $request->nss,
            'institucion_pension' => $request->institucion_pension,
            'estado_sistema' => 'ACTIVO',
            'data_adicional' => [
                'telefono' => $request->telefono
            ]
        ]);

        return back()->with('success', 'Pensionado registrado correctamente en la Cartera Maestra.');
    }

    public function importMaster(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240', // 10MB max
        ]);

        try {
            Excel::import(new PensionadoMasterImport, $request->file('file'));
            return back()->with('success', 'Importación masiva completada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error durante la importación: ' . $e->getMessage());
        }
    }

    public function exportMaster()
    {
        return Excel::download(new PensionadoMasterExport, 'cartera_pensionados_' . date('Ymd') . '.xlsx');
    }

    public function show($uuid)
    {
        $carga = DispersionCarga::where('uuid', $uuid)->with(['titulares', 'dependientes', 'logs'])->firstOrFail();
        return view('modules.admin.dispersion.pensionados.show', compact('carga'));
    }

    public function reprocess($uuid)
    {
        $carga = DispersionCarga::where('uuid', $uuid)->firstOrFail();
        
        try {
            DB::transaction(function() use ($carga) {
                // Limpiar datos previos del lote
                $carga->titulares()->delete();
                $carga->dependientes()->delete();
                $carga->logs()->delete();
                
                $carga->update(['estado' => 'Procesando']);
            });

            $this->processFile($carga);

            return redirect()->route('dispersion.pensionados.show', $carga->uuid)
                ->with('success', 'Lote reprocesado correctamente con la nueva lógica.');

        } catch (Exception $e) {
            return back()->with('error', 'Error al reprocesar: ' . $e->getMessage());
        }
    }

    public function destroy($uuid)
    {
        $carga = DispersionCarga::where('uuid', $uuid)->firstOrFail();
        
        try {
            DB::transaction(function() use ($carga) {
                $carga->titulares()->delete();
                $carga->dependientes()->delete();
                $carga->logs()->delete();
                $carga->delete();
            });

            return redirect()->route('dispersion.pensionados.history')
                ->with('success', 'Lote eliminado correctamente. Ahora puede subir el archivo nuevamente.');

        } catch (Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function reports()
    {
        return view('modules.admin.dispersion.pensionados.reports');
    }

    public function config()
    {
        return view('modules.admin.dispersion.pensionados.config');
    }

    public function historyMaster(\App\Models\Modules\Dispersion\PensionadoMaster $pensionado)
    {
        $historial = $pensionado->historial_pagos->map(function($pago) {
            return [
                'periodo' => $pago->periodo,
                'tipo_pensionado' => $pago->tipo_pensionado ?? 'N/D',
                'origen_pension' => $pago->origen_pension ?? 'N/D',
                'fecha_deteccion' => $pago->created_at->format('d/m/Y'),
            ];
        });

        return response()->json([
            'pensionado' => $pensionado,
            'historial' => $historial
        ]);
    }

    public function markNotified(\App\Models\Modules\Dispersion\PensionadoMaster $pensionado)
    {
        $pensionado->update(['notificado_at' => now()]);
        return response()->json(['success' => true]);
    }
}
