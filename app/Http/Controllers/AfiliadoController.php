<?php

namespace App\Http\Controllers;

use App\Services\AfiliadoService;
use App\Services\EvidenciaService;
use App\Models\Afiliado;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAfiliadoRequest;
use App\Http\Requests\UpdateAfiliadoRequest;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AfiliadosExport;

class AfiliadoController extends Controller
{
    protected $afiliadoService;
    protected $evidenciaService;

    public function __construct(AfiliadoService $afiliadoService, EvidenciaService $evidenciaService)
    {
        $this->afiliadoService = $afiliadoService;
        $this->evidenciaService = $evidenciaService;
    }

    public function syncSingle(Afiliado $afiliado)
    {
        try {
            $afiliado->pullFromFirebase();
            return response()->json([
                'success' => true, 
                'estado' => $afiliado->estado?->nombre ?? 'Pendiente',
                'estado_id' => $afiliado->estado_id
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verifica duplicados en Firebase en tiempo real
     */
    public function checkDuplicate(Request $request, \App\Services\FirebaseSyncService $syncService)
    {
        $cedula = $request->cedula; // Mantenemos el formato original con guiones
        
        $docIdRaw = preg_replace('/[^0-9]/', '', $cedula);
        
        if (strlen($docIdRaw) < 9) {
            return response()->json(['exists' => false]);
        }

        // Intentamos con guiones (nuevo estándar) y sin guiones (legacy)
        $result = $syncService->checkDocumentExistence('afiliados', $cedula);
        if (!$result['exists']) {
            $result = $syncService->checkDocumentExistence('afiliados', $docIdRaw);
        }
        
        return response()->json($result);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('afiliados.index', $this->processIndex($request));
    }

    public function indexCmd(Request $request)
    {
        // Para operadoras, la vista "Afiliados CMD" les permite ver todo el catálogo de la empresa
        // sin importar a quién esté delegado, pero restringido solo a la empresa CMD.
        $bypass = auth()->check() && auth()->user()->hasRole('Operador');
        return view('afiliados.index', $this->processIndex($request, 'CMD', $bypass));
    }

    public function indexMios(Request $request)
    {
        // Vista específica para ver solo lo que tiene asignado el usuario actual
        return view('afiliados.index', $this->processIndex($request, null, false));
    }

    public function indexOtros(Request $request)
    {
        return view('afiliados.index', $this->processIndex($request, 'Otros'));
    }

    public function indexCallCenter(Request $request)
    {
        // Nueva vista para trabajar lo que llega desde Call Center
        return view('afiliados.index', $this->processIndex($request, 'CallCenter'));
    }

    public function indexSalidaInmediata(Request $request)
    {
        $data = $this->processIndex($request, 'SalidaInmediata');
        
        if ($request->ajax()) {
            return view('afiliados.partials.salida_inmediata_table', $data)->render();
        }

        return view('afiliados.salida_inmediata', $data);
    }

    protected function processIndex(Request $request, $segment = null, $bypassDelegation = false)
    {
        $query = \App\Models\Afiliado::with(['corte', 'responsable', 'estado', 'empresaModel', 'evidenciasAfiliado']);

        // 1. Manejo de Segregación de Data por Segmento (Vista Maestro)
        if ($segment) {
            $query->withoutGlobalScope(\App\Models\Scopes\ResponsableScope::class);
            
            if ($segment === 'CMD') {
                $query->ars(); // Solo CMD
            } elseif ($segment === 'Otros') {
                $query->noArs(); // Solo Extra Empresa
            } elseif ($segment === 'SalidaInmediata') {
                $query->whereHas('empresaModel', function($q) {
                    $q->where('es_verificada', true);
                })->whereNull('fecha_entrega_safesure');
            } elseif ($segment === 'CallCenter') {
                $query->whereHas('corte', function($q) {
                    $q->where('nombre', 'Promociones Call Center');
                });
            }
        }

        // 2. Filtro de Delegación Personal (Solo si no estamos en una vista de segmento maestro)
        if (!$segment && !$bypassDelegation) {
            if (!auth()->user()->hasRole(['Admin'])) {
                $query->where('responsable_id', auth()->user()->responsable_id ?? 0);
            }
        }

        // 1.5 Aplicar restricción de Rol Operador (Nunca deben ver OTRAS cargas)
        if (auth()->check() && auth()->user()->hasRole('Operador')) {
            $query->ars();
        }

        // 2. Filtros de Búsqueda y Estado
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre_completo', 'like', '%' . $request->search . '%')
                  ->orWhere('cedula', 'like', '%' . $request->search . '%')
                  ->orWhere('contrato', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('corte_id')) {
            $query->where('corte_id', $request->corte_id);
        }
        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        // Filtros de Ubicación Normalizada (Herencia Afiliado -> Empresa)
        if ($request->filled('provincia_id')) {
            $query->where(function($q) use ($request) {
                $q->where('provincia_id', $request->provincia_id)
                  ->orWhereHas('empresaModel', function($qe) use ($request) {
                      $qe->where('provincia_id', $request->provincia_id);
                  });
            });
        }
        if ($request->filled('municipio_id')) {
            $query->where(function($q) use ($request) {
                $q->where('municipio_id', $request->municipio_id)
                  ->orWhereHas('empresaModel', function($qe) use ($request) {
                      $qe->where('municipio_id', $request->municipio_id);
                  });
            });
        }

        if ($request->filled('rnc_empresa')) {
            $query->where(function($q) use ($request) {
                $q->where('rnc_empresa', 'like', '%' . $request->rnc_empresa . '%')
                  ->orWhereHas('empresaModel', function($qe) use ($request) {
                      $qe->where('rnc', 'like', '%' . $request->rnc_empresa . '%');
                  });
            });
        }
        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }
        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }
        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }
        if ($request->filled('reasignado')) {
            $query->where('reasignado', $request->reasignado);
        }
        if ($request->get('company_status') === 'none') {
            $query->whereNull('empresa_id');
        } elseif ($request->get('company_status') === 'assigned') {
            $query->whereNotNull('empresa_id');
        }
        if ($request->get('asignacion') === 'pendiente') {
            $query->whereNull('responsable_id');
        }

        // Ordenamiento
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        // Mapeo seguro de columnas para evitar inyección SQL
        $allowedSorts = [
            'nombre'      => 'nombre_completo',
            'cedula'      => 'cedula',
            'contrato'    => 'contrato',
            'empresa'     => 'empresa',
            'entrega'     => 'fecha_entrega_safesure',
            'creado'      => 'created_at',
            'estado'      => 'estado_id',
            'responsable' => 'responsable_id'
        ];

        $orderCol = $allowedSorts[$sort] ?? 'created_at';
        $query->orderBy($orderCol, $direction);

        // Optimización de columnas: Solo traer lo necesario para el listado si es posible
        // Pero para el listado actual traemos todo por simplicidad y porque los modelos son anchos
        $afiliados = $query->paginate(30)->withQueryString();

        // 3. Caché de Métricas (60 segundos para mantener frescura pero aliviar carga)
        $cacheKey = "stats_periodo_{$segment}_" . (auth()->id() ?? 'guest');
        $statsPorPeriodo = \Cache::remember($cacheKey, 60, function() use ($segment) {
            return \App\Models\Corte::withCount([
                'afiliados as total' => function($q) use ($segment) {
                    if ($segment === 'CMD') {
                        $q->ars()->whereNotNull('responsable_id');
                    } elseif ($segment === 'Otros') {
                        $q->noArs()->whereNotNull('responsable_id');
                    } elseif ($segment === 'CallCenter') {
                        $q->whereHas('corte', function($qc) { $qc->where('nombre', 'Promociones Call Center'); });
                    } else {
                        $q->whereNull('responsable_id');
                    }
                },
                'afiliados as completados' => function($q) use ($segment) {
                    if ($segment === 'CMD') {
                        $q->ars()->whereNotNull('responsable_id');
                    } elseif ($segment === 'Otros') {
                        $q->noArs()->whereNotNull('responsable_id');
                    } elseif ($segment === 'CallCenter') {
                        $q->whereHas('corte', function($qc) { $qc->where('nombre', 'Promociones Call Center'); });
                    } else {
                        $q->whereNull('responsable_id');
                    }
                    $q->whereHas('estado', function($e) { $e->where('nombre', 'Completado'); });
                }
            ])->get()->map(function($corte) {
                $corte->pendiente = $corte->total - $corte->completados;
                $corte->porcentaje = $corte->total > 0 ? round(($corte->completados / $corte->total) * 100) : 0;
                return $corte;
            })->filter(fn($c) => $c->total > 0);
        });

        // 4. Pasar listas estáticas cacheadas para los filtros
        $filtros = \Cache::remember('listas_filtros_afiliados', 3600, function() {
            return [
                'estados' => \App\Models\Estado::all(),
                'cortes' => \App\Models\Corte::all(),
                'lotes' => \App\Models\Lote::orderBy('created_at', 'desc')->take(20)->get(),
                'responsables' => \App\Models\Responsable::all(),
                'empresas' => \App\Models\Empresa::orderBy('nombre')->get(['id', 'nombre', 'rnc', 'es_verificada']),
            ];
        });

        return array_merge(compact('afiliados', 'segment', 'statsPorPeriodo'), $filtros);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $cortes = \App\Models\Corte::all();
        $estados = \App\Models\Estado::all();
        $responsables = \App\Models\Responsable::all();
        $empresas = \App\Models\Empresa::orderBy('nombre')->get();
        $provincias = \App\Models\Provincia::orderBy('nombre')->get();
        $municipios = collect();
        $segment = $request->get('segment');

        return view('afiliados.create', compact('cortes', 'estados', 'responsables', 'empresas', 'segment', 'provincias', 'municipios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAfiliadoRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            if ($request->filled('provincia_id')) {
                $validated['provincia'] = \App\Models\Provincia::find($request->provincia_id)->nombre;
            }
            if ($request->filled('municipio_id')) {
                $validated['municipio'] = \App\Models\Municipio::find($request->municipio_id)->nombre;
            }

            if ($request->filled('empresa_id')) {
                $empresa = \App\Models\Empresa::find($request->empresa_id);
                $validated['empresa'] = $empresa->nombre;
                $validated['rnc_empresa'] = $empresa->rnc;
            }

            // Create affiliate
            $afiliado = \App\Models\Afiliado::create($validated);

            // Assign initial status (audit / record history)
            $this->afiliadoService->updateStatus(
                $afiliado, 
                $request->estado_id, 
                'Registro creado manualmente con estado inicial: ' . (\App\Models\Estado::find($request->estado_id)?->nombre ?? 'N/A'),
                auth()->id()
            );

            DB::commit();

            $afiliado->refresh();

            $isSynced = $afiliado->firebase_synced_at && $afiliado->firebase_synced_at->isAfter(now()->subSeconds(10));
            $msg = $isSynced ? 'Afiliado creado y respaldado en la nube.' : 'Afiliado creado exitosamente (Local).';
            $msgType = $isSynced ? 'cloud_success' : 'success';

            // Lógica de Redirección (Semana 3: Flujos Continuos)
            if ($request->action === 'save_and_new') {
                return redirect()->route('carnetizacion.afiliados.create', ['segment' => $request->segment])->with($msgType, $msg);
            }

            $route = $request->segment === 'CMD' ? 'carnetizacion.afiliados.cmd' : ($request->segment === 'Otros' ? 'carnetizacion.afiliados.otros' : 'carnetizacion.afiliados.index');
            return redirect()->route($route)->with($msgType, $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = $e->getMessage();
            if ($e->getCode() == 23000 || str_contains($msg, 'Duplicate entry')) {
                $msg = "Error: El afiliado ya existe en este corte (Cédula duplicada para este periodo).";
            }
            return back()->withInput()->with('error', $msg);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Afiliado $afiliado, \App\Services\FirebaseSyncService $syncService)
    {
        // Gestión de Navegación (Breadcrumbs Dinámicos)
        $previousUrl = url()->previous();
        // Solo actualizar si venimos de un listado, no de un detalle, edición o guardado
        $isList = str_contains($previousUrl, '/afiliados') && 
                  !preg_match('/[a-f0-9-]{36}/', $previousUrl) && 
                  !str_contains($previousUrl, '/edit') &&
                  !str_contains($previousUrl, '/create');

        if ($isList) {
            $label = 'Afiliados';
            if (str_contains($previousUrl, '/afiliados/cmd')) $label = 'Afiliados CMD';
            elseif (str_contains($previousUrl, '/afiliados/otros')) $label = 'Afiliados Otros';
            elseif (str_contains($previousUrl, '/afiliados/mios')) $label = 'Mis Afiliados';
            elseif (str_contains($previousUrl, '/afiliados/call-center')) $label = 'Call Center';
            elseif (str_contains($previousUrl, '/afiliados/salida-inmediata')) $label = 'Salida Inmediata';
            
            session(['afiliados_return_to' => $previousUrl]);
            session(['afiliados_return_label' => $label]);
        }

        $this->syncFromFirebase($afiliado, $syncService);
        $afiliado->load([
            'corte', 'responsable', 'estado', 'empresaModel', 'evidenciasAfiliado', 
            'historialEstados.user', 'historialEstados.estadoAnterior', 
            'historialEstados.estadoNuevo', 'notas.user'
        ]);

        // Cargar auditoría de sincronización
        $auditLogs = \App\Models\CloudSyncAudit::where('auditable_type', get_class($afiliado))
            ->where('auditable_id', $afiliado->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $estados = \App\Models\Estado::all();
        $responsables = \App\Models\Responsable::all();

        $returnUrl = session('afiliados_return_to', route('carnetizacion.afiliados.index'));
        $returnLabel = session('afiliados_return_label', 'Afiliados');
        
        return view('afiliados.show', compact('afiliado', 'estados', 'responsables', 'returnUrl', 'returnLabel', 'auditLogs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Afiliado $afiliado, \App\Services\FirebaseSyncService $syncService)
    {
        // Gestión de Navegación (Breadcrumbs Dinámicos)
        $previousUrl = url()->previous();
        // Solo actualizar si venimos de un listado, no de un detalle, edición o guardado
        $isList = str_contains($previousUrl, '/afiliados') && 
                  !preg_match('/[a-f0-9-]{36}/', $previousUrl) && 
                  !str_contains($previousUrl, '/edit') &&
                  !str_contains($previousUrl, '/create');

        if ($isList) {
            $label = 'Afiliados';
            if (str_contains($previousUrl, '/afiliados/cmd')) $label = 'Afiliados CMD';
            elseif (str_contains($previousUrl, '/afiliados/otros')) $label = 'Afiliados Otros';
            elseif (str_contains($previousUrl, '/afiliados/mios')) $label = 'Mis Afiliados';
            elseif (str_contains($previousUrl, '/afiliados/call-center')) $label = 'Call Center';
            elseif (str_contains($previousUrl, '/afiliados/salida-inmediata')) $label = 'Salida Inmediata';
            
            session(['afiliados_return_to' => $previousUrl]);
            session(['afiliados_return_label' => $label]);
        }

        $this->syncFromFirebase($afiliado, $syncService);
        $afiliado->load('empresaModel');
        $cortes = \App\Models\Corte::all();
        $estados = \App\Models\Estado::all();
        $responsables = \App\Models\Responsable::all();
        $empresas = \App\Models\Empresa::orderBy('nombre')->get();
        $provincias = \App\Models\Provincia::orderBy('nombre')->get();
        $municipios = $afiliado->provincia_id ? \App\Models\Municipio::where('provincia_id', $afiliado->provincia_id)->orderBy('nombre')->get() : collect();

        $returnUrl = session('afiliados_return_to', route('carnetizacion.afiliados.index'));
        $returnLabel = session('afiliados_return_label', 'Afiliados');

        return view('afiliados.edit', compact('afiliado', 'cortes', 'estados', 'responsables', 'empresas', 'provincias', 'municipios', 'returnUrl', 'returnLabel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAfiliadoRequest $request, \App\Models\Afiliado $afiliado)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Si se seleccionó una empresa del módulo, actualizamos también los campos legacy por compatibilidad
            if ($request->filled('provincia_id')) {
            $validated['provincia'] = \App\Models\Provincia::find($request->provincia_id)->nombre;
        }
        if ($request->filled('municipio_id')) {
            $validated['municipio'] = \App\Models\Municipio::find($request->municipio_id)->nombre;
        }

        if ($request->filled('empresa_id')) {
                $empresa = \App\Models\Empresa::find($request->empresa_id);
                $validated['empresa'] = $empresa->nombre;
                $validated['rnc_empresa'] = $empresa->rnc;
            }

            // Handle metadata update
            $afiliado->update(collect($validated)->except('estado_id')->toArray());

            // Handle status change via Service Layer (for business rules)
            $this->afiliadoService->updateStatus(
                $afiliado, 
                $request->estado_id, 
                'Cambio de estado desde edición manual.',
                auth()->id()
            );

            DB::commit();
            
            $afiliado->refresh(); // Asegurar que capturamos el firebase_synced_at del observer

            if ($afiliado->firebase_synced_at && $afiliado->firebase_synced_at->isAfter(now()->subSeconds(10))) {
                return redirect()->route('carnetizacion.afiliados.show', $afiliado)->with('cloud_success', 'Afiliado actualizado y respaldado en la nube.');
            }

            return redirect()->route('carnetizacion.afiliados.show', $afiliado)->with('success', 'Afiliado actualizado correctamente (Local).');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function reassign(Request $request, Afiliado $afiliado)
    {
        $request->validate([
            'responsable_id' => 'required|exists:responsables,id',
        ]);

        try {
            DB::beginTransaction();
            /* Regla 5.3 Desactivada por solicitud de usuario para permitir reasignación de sincronizados */

            $oldResponsable = $afiliado->responsable?->nombre ?? 'Sin Asignar';
            $afiliado->responsable_id = $request->responsable_id;
            $afiliado->reasignado = true; // Flag for audit
            $afiliado->save();
            $newResponsable = $afiliado->fresh()->responsable?->nombre ?? 'Sin Asignar';

            \App\Models\HistorialEstado::create([
                'afiliado_id' => $afiliado->id,
                'estado_anterior_id' => $afiliado->estado_id,
                'estado_nuevo_id' => $afiliado->estado_id,
                'user_id' => auth()->id() ?? 1,
                'observacion' => "Reasignación de Responsable: {$oldResponsable} -> {$newResponsable}"
            ]);

            DB::commit();
            return back()->with('success', "Responsable cambiado a {$newResponsable} exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
            'responsable_id' => 'required|exists:responsables,id',
            'segment' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            $afiliados = \App\Models\Afiliado::with(['responsable', 'estado', 'proveedor'])->whereIn('uuid', $request->selected)->get();
            $segment = $request->input('segment');
            
            foreach($afiliados as $afiliado) {
                /** @var Afiliado $afiliado */
                /* Regla 5.3 Desactivada para procesos masivos */

                $afiliado->responsable_id = $request->responsable_id;
                $afiliado->reasignado = true; // Flag for audit
                
                // La segmentación ahora depende del Responsable asignado, no forzamos la empresa.
                $afiliado->save();

                \App\Models\HistorialEstado::create([
                    'afiliado_id' => $afiliado->id,
                    'estado_anterior_id' => $afiliado->estado_id,
                    'estado_nuevo_id' => $afiliado->estado_id,
                    'user_id' => auth()->id() ?? 1,
                    'observacion' => 'Asignado a responsable masivamente.'
                ]);
            }
            DB::commit();
            return back()->with('success', count($request->selected) . ' afiliados asignados exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function bulkCompany(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
            'empresa_id' => 'required|exists:empresas,id',
            'segment' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            $afiliados = \App\Models\Afiliado::whereIn('uuid', $request->selected)->get();
            $empresa = \App\Models\Empresa::findOrFail($request->empresa_id);
            
            foreach($afiliados as $afiliado) {
                /** @var \App\Models\Afiliado $afiliado */
                $oldEmpresa = $afiliado->empresa ?? 'Sin Empresa';
                $afiliado->empresa_id = $empresa->id;
                $afiliado->empresa = $empresa->nombre;
                $afiliado->rnc_empresa = $empresa->rnc;
                $afiliado->save();

                \App\Models\HistorialEstado::create([
                    'afiliado_id' => $afiliado->id,
                    'estado_anterior_id' => $afiliado->estado_id,
                    'estado_nuevo_id' => $afiliado->estado_id,
                    'user_id' => auth()->id() ?? 1,
                    'observacion' => "Asignación Masiva de Empresa: {$oldEmpresa} -> {$empresa->nombre}"
                ]);
            }

            DB::commit();
            
            return back()->with('success', count($request->selected) . ' empresas actualizadas masivamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error en asignación masiva de empresa: ' . $e->getMessage());
        }
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
            'estado_id' => 'required|exists:estados,id',
            'motivo_rapido' => 'nullable|string',
            'observacion' => 'nullable|string',
            'segment' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            $afiliados = \App\Models\Afiliado::whereIn('uuid', $request->selected)->get();
            
            // Determinar la observación final (prioridad al motivo rápido)
            $observacionFinal = $request->motivo_rapido ?: ($request->observacion ?? 'Cambio de estado masivo.');

            foreach($afiliados as $afiliado) {
                /** @var Afiliado $afiliado */
                $this->afiliadoService->updateStatus(
                    $afiliado,
                    $request->estado_id,
                    $observacionFinal,
                    auth()->id()
                );
            }
            DB::commit();
            return back()->with('success', count($request->selected) . ' afiliados actualizados exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Afiliado $afiliado)
    {
        $request->validate([
            'estado_id' => 'required|exists:estados,id',
            'motivo_rapido' => 'nullable|string',
            'observacion' => 'nullable|string|max:1000'
        ]);

        try {
            $observacionFinal = $request->motivo_rapido ?: ($request->observacion ?? 'Estado actualizado individualmente.');
            $this->afiliadoService->updateStatus($afiliado, $request->estado_id, $observacionFinal, auth()->id());
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'estado' => $afiliado->fresh()->estado->nombre]);
            }
            return back()->with('success', 'Estado del afiliado actualizado correctamente.');
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function uploadEvidencia(Request $request, Afiliado $afiliado)
    {
        $request->validate([
            'tipo_documento' => 'required|in:acuse_recibo,formulario_firmado',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        try {
            $this->evidenciaService->upload(
                $afiliado, 
                $request->tipo_documento, 
                $request->file('file'), 
                auth()->id() ?? 1,
                $request->observaciones
            );
            return back()->with('success', 'Evidencia subida y procesada correctamente.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function searchAjax(Request $request)
    {
        $search = $request->get('q');
        
        if (strlen($search) < 3) {
            return response()->json([]);
        }

        $afiliados = \App\Models\Afiliado::where(function($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%")
                  ->orWhere('poliza', 'like', "%{$search}%")
                  ->orWhere('rnc_empresa', 'like', "%{$search}%")
                  ->orWhere('empresa', 'like', "%{$search}%");
            })
            ->with(['estado', 'responsable'])
            ->limit(8)
            ->get()
            ->map(function($af) {
                return [
                    'id' => $af->id,
                    'nombre' => $af->nombre_completo,
                    'cedula' => $af->cedula,
                    'poliza' => $af->poliza ?? 'N/A',
                    'estado' => $af->estado->nombre ?? 'Sin Estado',
                    'responsable' => $af->responsable->nombre ?? 'Sin Asignar',
                    'url' => route('carnetizacion.afiliados.show', $af)
                ];
            });

        return response()->json($afiliados);
    }
    public function export(Request $request)
    {
        return Excel::download(new AfiliadosExport($request->all()), 'afiliados_syscarnet_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function sanitizeAddresses()
    {
        try {
            DB::beginTransaction();

            $afiliados = \App\Models\Afiliado::whereNotNull('direccion')->get();
            $count = 0;

            foreach ($afiliados as $afiliado) {
                $original = $afiliado->direccion;
                $afiliado->normalizeAddress();
                
                if ($original !== $afiliado->direccion) {
                    $afiliado->save();
                    $count++;
                }
            }

            DB::commit();
            return back()->with('success', "Se han normalizado {$count} direcciones exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Sincroniza la colección de afiliados desde Firebase (Triggered from Index)
     */
    public function syncFirebase()
    {
        try {
            \App\Jobs\FirebaseSyncJob::dispatch(['--affiliates' => true]);
            return back()->with('success', 'Sincronización iniciada en segundo plano. Los datos aparecerán gradualmente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al iniciar sincronización: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene el progreso de la sincronización de Firebase para la UI
     */
    public function getProgress()
    {
        return response()->json([
            'active' => (bool) \Illuminate\Support\Facades\Cache::get('firebase_sync_active', false),
            'progress' => (int) \Illuminate\Support\Facades\Cache::get('firebase_sync_progress', 0),
            'label' => (string) \Illuminate\Support\Facades\Cache::get('firebase_sync_label', ''),
        ]);
    }

    public function confirmReceipt(Request $request, Afiliado $afiliado)
    {
        try {
            DB::beginTransaction();

            $completadoId = \App\Models\Estado::where('nombre', 'Completado')->first()->id ?? 9;
            
            $this->afiliadoService->updateStatus(
                $afiliado, 
                $completadoId, 
                'Recepción de formulario físico confirmada por CMD. Proceso finalizado.',
                auth()->id()
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'estado' => 'Completado']);
            }
            return back()->with('success', "Recepción de {$afiliado->nombre_completo} confirmada y sincronizada.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Confirma solo la recepción del acuse de recibo (Fase intermedia)
     */
    public function confirmAcuse(Request $request, Afiliado $afiliado)
    {
        try {
            DB::beginTransaction();

            $this->evidenciaService->validatePhysical($afiliado, 'acuse_recibo', auth()->id(), 'Acuse de recibo firmado por tercero (recepción parcial).');

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'estado' => $afiliado->fresh()->estado->nombre]);
            }
            return back()->with('success', "Acuse de recibo de {$afiliado->nombre_completo} registrado. El expediente queda pendiente de formulario.");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    private function syncFromFirebase($afiliado, $syncService)
    {
        // Solo descargar si han pasado más de 2 horas desde la última sync
        $afiliado->pullIfStale(2);
    }
}
