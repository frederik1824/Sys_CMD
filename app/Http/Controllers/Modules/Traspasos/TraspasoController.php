<?php

namespace App\Http\Controllers\Modules\Traspasos;

use App\Http\Controllers\Controller;
use App\Models\Traspaso;
use App\Models\AgenteTraspaso;
use App\Models\SupervisorTraspaso;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TraspasoController extends Controller
{
    /**
     * Display a listing of the transfers.
     */
    public function index(Request $request)
    {
        $query = Traspaso::query();

        // Basic Filtering
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_afiliado', 'like', "%{$search}%")
                  ->orWhere('cedula_afiliado', 'like', "%{$search}%")
                  ->orWhere('numero_solicitud_epbd', 'like', "%{$search}%");
            });
        }

        if ($request->has('estado') && $request->estado != 'all') {
            $query->where('estado', $request->estado);
        }

        if ($request->has('agente') && $request->agente != 'all') {
            $query->where('agente', $request->agente);
        }

        if ($request->has('supervisor') && $request->supervisor != 'all') {
            $agentesDelSup = AgenteTraspaso::where('supervisor_id', $request->supervisor)->pluck('nombre');
            $query->whereIn('agente', $agentesDelSup);
        }

        $currentMonth = now()->format('Y-m');
        
        // Meta del Mes (Suma de metas de todos los agentes para este periodo)
        $metaMesTotal = \App\Models\MetaTraspaso::where('periodo', $currentMonth)->sum('meta_cantidad');

        // Stats Generales
        $stats = [
            'total' => Traspaso::count(),
            'efectivos' => Traspaso::whereNotNull('fecha_efectivo')->count(),
            'rechazados' => Traspaso::where('estado', 'like', '%RE%')->count(),
            'generados_mes' => Traspaso::where('fecha_solicitud', 'like', now()->format('Y-m') . '%')->count(),
            'efectivos_mes' => Traspaso::where('periodo_efectivo', $currentMonth)->count(),
            'pendientes_datos' => Traspaso::whereNull('fecha_efectivo')->orWhere('cantidad_dependientes', 0)->count(),
            'meta_mes' => $metaMesTotal,
        ];

        // Ranking por Agente (Top 10)
        $rankingAgentes = Traspaso::select('agente')
            ->selectRaw('count(*) as total')
            ->selectRaw('count(fecha_efectivo) as efectivos')
            ->groupBy('agente')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Ranking por Equipo (Supervisor)
        $rankingEquipos = SupervisorTraspaso::select('supervisor_traspasos.id', 'supervisor_traspasos.nombre')
            ->leftJoin('agente_traspasos', 'supervisor_traspasos.id', '=', 'agente_traspasos.supervisor_id')
            ->leftJoin('traspasos', 'agente_traspasos.nombre', '=', 'traspasos.agente')
            ->selectRaw('count(traspasos.id) as total_solicitudes')
            ->groupBy('supervisor_traspasos.id', 'supervisor_traspasos.nombre')
            ->get();

        // Motivos de Rechazo
        $motivosRechazo = Traspaso::where('estado', 'like', '%RE%')
            ->whereNotNull('motivos_estado')
            ->select('motivos_estado')
            ->selectRaw('count(*) as total')
            ->groupBy('motivos_estado')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Datos para el Gráfico de Tendencia (Últimos 6 meses)
        $labels = [];
        $dataGenerados = [];
        $dataEfectivos = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('F');
            $period = $month->format('Y-m');

            $dataGenerados[] = Traspaso::where('fecha_solicitud', 'like', "{$period}%")->count();
            $dataEfectivos[] = Traspaso::where('periodo_efectivo', $period)->count();
        }

        $chartData = [
            'labels' => $labels,
            'generados' => $dataGenerados,
            'efectivos' => $dataEfectivos,
        ];

        $traspasos = $query->orderBy('fecha_solicitud', 'desc')->paginate(15);
        $agentes = Traspaso::distinct()->pluck('agente');
        $estados = Traspaso::distinct()->pluck('estado');
        $supervisores = SupervisorTraspaso::where('activo', true)->get();

        return view('modules.traspasos.index', compact(
            'traspasos', 'stats', 'agentes', 'estados', 'supervisores',
            'rankingAgentes', 'rankingEquipos', 'motivosRechazo', 'chartData'
        ));
    }

    /**
     * Export filtered transfers to CSV.
     */
    public function export(Request $request)
    {
        $query = Traspaso::query();

        // Replicamos filtros de index
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_afiliado', 'like', "%{$search}%")
                  ->orWhere('cedula_afiliado', 'like', "%{$search}%")
                  ->orWhere('numero_solicitud_epbd', 'like', "%{$search}%");
            });
        }
        if ($request->has('estado') && $request->estado != 'all') {
            $query->where('estado', $request->estado);
        }
        if ($request->has('agente') && $request->agente != 'all') {
            $query->where('agente', $request->agente);
        }

        $traspasos = $query->orderBy('fecha_solicitud', 'desc')->get();

        $filename = "reporte_traspasos_" . now()->format('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Nombre Afiliado', 'Cedula', 'Solicitud EPBD', 'Fecha Solicitud', 
            'Agente', 'Estado', 'Motivo', 'Fecha Efectiva', 'Periodo', 'Dependientes'
        ];

        $callback = function() use($traspasos, $columns) {
            $file = fopen('php://output', 'w');
            // Bom para Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($traspasos as $t) {
                fputcsv($file, [
                    $t->nombre_afiliado,
                    $t->cedula_afiliado,
                    $t->numero_solicitud_epbd,
                    $t->fecha_solicitud ? $t->fecha_solicitud->format('d/m/Y') : 'N/A',
                    $t->agente,
                    $t->estado,
                    $t->motivos_estado,
                    $t->fecha_efectivo ? $t->fecha_efectivo->format('d/m/Y') : '',
                    $t->periodo_efectivo,
                    $t->cantidad_dependientes,
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for importing transfers.
     */
    public function importView()
    {
        return view('modules.traspasos.import');
    }

    /**
     * Handle the data import (CSV/TSV/Paste).
     */
    public function import(Request $request)
    {
        $request->validate([
            'data' => 'required|string'
        ]);

        $lines = explode("\n", $request->data);
        $headers = [];
        $count = 0;
        $updated = 0;

        foreach ($lines as $index => $line) {
            $columns = explode("\t", trim($line)); // Assuming TSV from Excel copy-paste
            
            if ($index === 0) {
                $headers = $columns;
                continue;
            }

            if (count($columns) < 5) continue;

            // Map columns based on the provided names
            // Nombre | Numero Identificacion | Nombre Solicitante | Numero Identificacion Solicitante | Fecha Solicitud | Fecha Envio EPBD | Numero Solicitud EPBD | Pendiente Carga Documento | Pendiente Aprobar Consentimiento | Agente | Estado | Motivos Estado
            
            $data = [
                'nombre_afiliado' => $columns[0] ?? '',
                'cedula_afiliado' => $columns[1] ?? '',
                'nombre_solicitante' => $columns[2] ?? '',
                'cedula_solicitante' => $columns[3] ?? '',
                'fecha_solicitud' => $this->parseDate($columns[4] ?? null),
                'fecha_envio_epbd' => $this->parseDate($columns[5] ?? null),
                'numero_solicitud_epbd' => $columns[6] ?? null,
                'pendiente_carga_documento' => strtolower($columns[7] ?? '') === 'si' || strtolower($columns[7] ?? '') === 'true',
                'pendiente_aprobar_consentimiento' => strtolower($columns[8] ?? '') === 'si' || strtolower($columns[8] ?? '') === 'true',
                'agente' => $columns[9] ?? 'Desconocido',
                'estado' => $columns[10] ?? 'Sin Estado',
                'motivos_estado' => $columns[11] ?? '',
            ];

            if ($data['numero_solicitud_epbd']) {
                $traspaso = Traspaso::where('numero_solicitud_epbd', $data['numero_solicitud_epbd'])->first();

                if ($traspaso) {
                    // Solo actualizar campos específicos si ya existe
                    $traspaso->update([
                        'pendiente_carga_documento' => $data['pendiente_carga_documento'],
                        'pendiente_aprobar_consentimiento' => $data['pendiente_aprobar_consentimiento'],
                        'estado' => $data['estado'],
                        'motivos_estado' => $data['motivos_estado'],
                    ]);
                    $updated++;
                } else {
                    // Crear nuevo si no existe
                    Traspaso::create($data);
                    $count++;
                }
            }
        }

        return redirect()->route('traspasos.index')->with('success', "Importación completada. Creados: $count, Actualizados: $updated");
    }

    /**
     * Update enrichment data (effectiveness and dependents).
     */
    /**
     * Show the bulk effective marking view.
     */
    public function bulkEffectiveView()
    {
        return view('modules.traspasos.bulk_effective');
    }

    /**
     * Process the bulk effective marking.
     */
    public function processBulkEffective(Request $request)
    {
        $request->validate([
            'cedulas' => 'required|string',
            'fecha_efectivo' => 'required|date',
            'periodo_efectivo' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        // Extraer cédulas y sanitizar (solo números)
        $rawCedulas = preg_split('/[\n\r\t,]+/', $request->cedulas, -1, PREG_SPLIT_NO_EMPTY);
        $cedulas = array_unique(array_map(function($c) {
            return preg_replace('/[^0-9]/', '', $c);
        }, $rawCedulas));

        // 1. Obtener todos los traspasos potenciales para estas cédulas
        // Buscamos comparando cédulas sanitizadas (sin guiones)
        $traspasosPotenciales = Traspaso::whereIn(\DB::raw("REPLACE(cedula_afiliado, '-', '')"), $cedulas)->get();
        $agrupados = $traspasosPotenciales->groupBy(function($item) {
            return preg_replace('/[^0-9]/', '', $item->cedula_afiliado);
        });

        $idsParaActualizar = [];
        $duplicadosConflictivos = [];

        foreach ($agrupados as $cedula => $registros) {
            if ($registros->count() === 1) {
                // Caso simple: Solo hay uno
                $idsParaActualizar[] = $registros->first()->id;
            } else {
                // Caso duplicado: Aplicar regla de negocio (Priorizar EN sobre RE)
                $filtradosEN = $registros->filter(fn($r) => str_contains($r->numero_solicitud_epbd, 'EN'));
                
                if ($filtradosEN->count() === 1) {
                    // La regla de negocio resolvió el conflicto
                    $idsParaActualizar[] = $filtradosEN->first()->id;
                } else {
                    // Sigue habiendo ambigüedad (o no hay ninguno EN, o hay varios EN)
                    $duplicadosConflictivos[] = $cedula;
                }
            }
        }

        // 2. Ejecutar la actualización masiva para los IDs resueltos
        if (!empty($idsParaActualizar)) {
            Traspaso::whereIn('id', $idsParaActualizar)
                ->update([
                    'fecha_efectivo' => $request->fecha_efectivo,
                    'periodo_efectivo' => $request->periodo_efectivo,
                ]);
        }

        $totalProcesados = count($idsParaActualizar);

        if (count($duplicadosConflictivos) > 0) {
            return back()->with('success', "Se han procesado {$totalProcesados} traspasos correctamente.")
                ->withErrors([
                    'cedulas' => "Las siguientes cédulas tienen múltiples solicitudes (Sin patrón EN/RE claro) y deben revisarse: " . implode(', ', $duplicadosConflictivos)
                ])->withInput();
        }

        return back()->with('success', "Se han marcado {$totalProcesados} traspasos como efectivos correctamente.");
    }

    public function updateEnrichment(Request $request, Traspaso $traspaso)
    {
        $request->validate([
            'fecha_efectivo' => 'nullable|date',
            'periodo_efectivo' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
            'cantidad_dependientes' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['fecha_efectivo', 'periodo_efectivo', 'cantidad_dependientes']);

        // Autocalcular periodo si se envía fecha pero no periodo
        if ($data['fecha_efectivo'] && !$data['periodo_efectivo']) {
            $data['periodo_efectivo'] = Carbon::parse($data['fecha_efectivo'])->format('Y-m');
        }

        $traspaso->update($data);

        return response()->json(['success' => true, 'message' => 'Datos actualizados correctamente']);
    }

    /**
     * Emitir carnet (crear afiliado) desde un traspaso efectivo.
     */
    public function emitirCarnet(Traspaso $traspaso)
    {
        // Verificar si ya existe un afiliado con esa cédula
        $exists = \App\Models\Afiliado::where('cedula', $traspaso->cedula_afiliado)->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ya existe un afiliado registrado con esta cédula.']);
        }

        // Obtener el corte más reciente (es obligatorio para crear un afiliado)
        $ultimoCorte = \App\Models\Corte::latest()->first();

        if (!$ultimoCorte) {
            return response()->json(['success' => false, 'message' => 'No hay periodos de corte creados en el sistema para asignar el afiliado.']);
        }

        // Crear el afiliado
        $afiliado = \App\Models\Afiliado::create([
            'nombre_completo' => $traspaso->nombre_afiliado,
            'cedula' => $traspaso->cedula_afiliado,
            'estado_id' => 1, // Pendiente
            'corte_id' => $ultimoCorte->id,
            // Podríamos añadir más lógica de asignación aquí
        ]);

        // Marcar como emitido
        $traspaso->update(['es_emitido' => true]);

        return response()->json([
            'success' => true, 
            'message' => 'Afiliado creado correctamente en el módulo de carnetización.',
            'uuid' => $afiliado->uuid
        ]);
    }

    private function parseDate($dateString)
    {
        if (!$dateString || trim($dateString) == '') return null;
        try {
            // Unipago dates are often DD/MM/YYYY
            return Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($dateString)->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
}
