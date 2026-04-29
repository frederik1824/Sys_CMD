<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Llamada;
use App\Models\AsignacionLlamada;
use App\Models\Afiliado;
use App\Models\User;
use App\Models\Lote;
use App\Models\Empresa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CallCenterController extends Controller
{
    public function storeBulkCalls(Request $request)
    {
        $request->validate([
            'uuids' => 'required|array',
            'observacion' => 'nullable|string',
            'documento_recibido' => 'boolean',
            'proximo_contacto' => 'nullable|date',
            'evidencia_foto' => 'nullable|image|max:4096'
        ]);

        $uuids = $request->uuids;
        $afiliados = Afiliado::whereIn('uuid', $uuids)->get();
        
        $evidenciaPath = null;
        if ($request->hasFile('evidencia_foto')) {
            $evidenciaPath = $request->file('evidencia_foto')->store('evidencias_llamadas', 'public');
        }

        DB::transaction(function() use ($afiliados, $request, $evidenciaPath) {
            foreach ($afiliados as $afiliado) {
                Llamada::create([
                    'afiliado_id' => $afiliado->id,
                    'usuario_id' => auth()->id(),
                    'fecha_llamada' => now(),
                    'estado_llamada' => $request->estado_llamada,
                    'observacion' => $request->observacion,
                    'documento_recibido' => $request->documento_recibido ?? false,
                    'proximo_contacto' => $request->proximo_contacto,
                    'evidencia_foto' => $evidenciaPath
                ]);

                // Actualizar estado del afiliado según la llamada
                if ($request->estado_llamada === 'Cédula efectiva') {
                    $afiliado->estado_id = ($request->documento_recibido) ? 13 : 12; // 13: Recibida, 12: Pendiente
                }
                $afiliado->save();
            }
        });

        return response()->json(['success' => true, 'message' => count($uuids) . ' registros gestionados correctamente.']);
    }

    public function dashboard(Request $request)
    {
        $fecha = $request->get('fecha', now()->toDateString());
        $usuario_id = $request->get('usuario_id') ?? auth()->id(); // Por defecto el usuario actual
        $operadores = User::role('Gestor de Llamadas')->get();

        // 1. Estadísticas de Producción Diaria (Basada en Llamadas)
        $llamadasHoyQuery = Llamada::whereDate('fecha_llamada', $fecha);
        if ($usuario_id) {
            $llamadasHoyQuery->where('usuario_id', $usuario_id);
        }
        $llamadasHoy = $llamadasHoyQuery->get();
        $totalLlamadasHoy = $llamadasHoy->count();

        // 2. Análisis de Estados de Llamada (Hoy)
        $resumenHoy = $llamadasHoy->groupBy('estado_llamada')->map->count()->toArray();
        
        // 3. KPIs Críticos
        // Localización: Llamadas que no fueron fallidas
        $fallidasMotivos = ['No contesta', 'Correo de voz', 'Fuera de servicio', 'Número equivocado', 'No labora en la empresa'];
        $localizadasCount = $llamadasHoy->whereNotIn('estado_llamada', $fallidasMotivos)->count();
        $tasaLocalizacion = $totalLlamadasHoy > 0 ? round(($localizadasCount / $totalLlamadasHoy) * 100, 1) : 0;

        // Efectividad: Cédulas efectivas / Total Localizadas
        $efectivasHoy = $llamadasHoy->where('estado_llamada', 'Cédula efectiva')->count();
        $tasaEfectividad = $localizadasCount > 0 ? round(($efectivasHoy / $localizadasCount) * 100, 1) : 0;

        // 4. Metas Diarias (Fase 1)
        $userTarget = User::find($usuario_id);
        $dailyGoal = $userTarget->daily_goal ?? 50;
        $goalProgress = $dailyGoal > 0 ? round(($efectivasHoy / $dailyGoal) * 100) : 0;

        // 5. Desglose de Motivos de Fallo (Fase 1)
        $motivosFallo = $llamadasHoy->whereIn('estado_llamada', $fallidasMotivos)
            ->groupBy('estado_llamada')
            ->map->count()
            ->toArray();

        // 6. Estado del Pipeline (Acumulado)
        $pipelineQuery = Afiliado::whereHas('asignacionesLlamadas', function($q) use ($usuario_id) {
            $q->where('usuario_id', $usuario_id)->where('activa', true);
        });

        $totalAsignados = (clone $pipelineQuery)->count();
        $pendientesLlamada = (clone $pipelineQuery)->whereIn('estado_id', [1, 11])->count();
        $pendientesDocumento = (clone $pipelineQuery)->where('estado_id', 12)->count(); // Cédula Pendiente
        $completados = (clone $pipelineQuery)->whereIn('estado_id', [13, 3])->count(); // Recibida o En ruta

        return view('admin.callcenter.dashboard', compact(
            'fecha', 'operadores', 'usuario_id', 
            'totalLlamadasHoy', 'resumenHoy', 'tasaLocalizacion', 'tasaEfectividad',
            'dailyGoal', 'goalProgress', 'efectivasHoy', 'motivosFallo',
            'totalAsignados', 'pendientesLlamada', 'pendientesDocumento', 'completados'
        ));
    }

    public function worklist(Request $request)
    {
        $user = auth()->user();
        
        $afiliadosAsignados = Afiliado::whereHas('asignacionesLlamadas', function($q) use ($user) {
                $q->where('usuario_id', $user->id)->where('activa', true);
            })
            ->with(['llamadas' => function($q) {
                $q->latest();
            }, 'empresaModel', 'estado'])
            ->get();

        // Asignar categorías explícitamente para el frontend
        $afiliadosAsignados->each(function($a) {
            if ($a->llamadas->isEmpty()) {
                $a->work_category = 'nuevos';
            } elseif (in_array($a->estado?->nombre, ['Cédula Recibida', 'Completado', 'En ruta'])) {
                $a->work_category = 'confirmados';
            } elseif ($a->estado?->nombre === 'Cédula Pendiente') {
                $a->work_category = 'documentacion';
            } else {
                $lastCall = $a->llamadas->first();
                if ($lastCall && in_array($lastCall->estado_llamada, ['No contesta', 'Correo de voz', 'Fuera de servicio', 'Número equivocado'])) {
                    $a->work_category = 'reintentos';
                } else {
                    $a->work_category = 'nuevos'; // Default fallback
                }
            }
        });

        $nuevos = $afiliadosAsignados->where('work_category', 'nuevos')->groupBy(function($a) {
            return $a->empresaModel->nombre ?? $a->empresa ?? 'Individuales';
        });
        
        $reintentos = $afiliadosAsignados->where('work_category', 'reintentos')->groupBy(function($a) {
            return $a->empresaModel->nombre ?? $a->empresa ?? 'Individuales';
        });
        
        $documentacion = $afiliadosAsignados->where('work_category', 'documentacion')->groupBy(function($a) {
            return $a->empresaModel->nombre ?? $a->empresa ?? 'Individuales';
        });
        
        $confirmados = $afiliadosAsignados->where('work_category', 'confirmados')->groupBy(function($a) {
            return $a->empresaModel->nombre ?? $a->empresa ?? 'Individuales';
        });

        // Mapear info de contacto de empresas para el modal de sesión
        $empresasInfo = $afiliadosAsignados->pluck('empresaModel')->whereNotNull()->unique('uuid')->mapWithKeys(function($e) {
            return [$e->nombre => [
                'contacto' => $e->contacto_nombre,
                'puesto' => $e->contacto_puesto,
                'telefono' => $e->contacto_telefono,
                'email' => $e->contacto_email,
            ]];
        });

        return view('admin.callcenter.worklist', compact('nuevos', 'reintentos', 'documentacion', 'confirmados', 'afiliadosAsignados', 'empresasInfo'));
    }

    public function assignList(Request $request)
    {
        $lotes = Lote::latest()->get();
        $empresas = Empresa::orderBy('nombre')->get();
        $operadores = User::role('Gestor de Llamadas')->get();

        $query = Afiliado::query()->with('empresaModel', 'lote');

        if ($request->get('solo_prospectos')) {
            $query->where('estado_id', 11);
        }

        if ($request->filled('lote_id')) {
            $query->where('lote_id', $request->lote_id);
        }
        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        // Filtro básico para la vista de asignación
        $afiliados = $request->hasAny(['lote_id', 'empresa_id', 'solo_prospectos']) ? $query->paginate(50) : collect([]);

        return view('admin.callcenter.assign', compact('lotes', 'empresas', 'operadores', 'afiliados'));
    }

    public function assignStore(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'afiliados' => 'required|array'
        ]);

        $usuarioId = $request->usuario_id;
        $afiliadosIds = $request->afiliados;

        DB::beginTransaction();
        try {
            // Desactivar asignaciones previas
            AsignacionLlamada::whereIn('afiliado_id', $afiliadosIds)->update(['activa' => false]);

            $asignaciones = [];
            foreach ($afiliadosIds as $afiliadoId) {
                $asignaciones[] = [
                    'afiliado_id' => $afiliadoId,
                    'usuario_id' => $usuarioId,
                    'asignador_id' => auth()->id(),
                    'fecha_asignacion' => now(),
                    'activa' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            AsignacionLlamada::insert($asignaciones);

            DB::commit();
            return back()->with('success', 'Afiliados asignados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al asignar: ' . $e->getMessage());
        }
    }

    public function storeCall(Request $request, Afiliado $afiliado)
    {
        $request->validate([
            'estado_llamada' => 'required|string',
            'observacion' => 'nullable|string',
            'documento_recibido' => 'nullable|boolean',
            'proximo_contacto' => 'nullable|date',
            'evidencia_foto' => 'nullable|image|max:4096'
        ]);

        $estado = $request->estado_llamada;
        
        // Logica de proximo contacto
        $proximoContacto = $request->proximo_contacto;
        if (!$proximoContacto && in_array($estado, ['No contesta', 'Correo de voz', 'Fuera de servicio'])) {
            $proximoContacto = Carbon::tomorrow()->toDateString();
        }

        $evidenciaPath = null;
        if ($request->hasFile('evidencia_foto')) {
            $evidenciaPath = $request->file('evidencia_foto')->store('evidencias_llamadas', 'public');
        }

        DB::beginTransaction();
        try {
            Llamada::create([
                'afiliado_id' => $afiliado->id,
                'usuario_id' => auth()->id(),
                'estado_llamada' => $estado,
                'observacion' => $request->observacion,
                'evidencia_foto' => $evidenciaPath,
                'fecha_llamada' => now(),
                'proximo_contacto' => $proximoContacto
            ]);

            // Integración con CMD Flow
            if ($estado === 'Cédula efectiva') {
                if ($request->documento_recibido) {
                    $nuevoEstado = \App\Models\Estado::where('nombre', 'Cédula Recibida')->first();
                } else {
                    $nuevoEstado = \App\Models\Estado::where('nombre', 'Cédula Pendiente')->first();
                }
                
                if ($nuevoEstado) {
                    $afiliado->update(['estado_id' => $nuevoEstado->id]);
                }
            } elseif ($estado === 'Tienen carnet') {
                $estadoCompletado = \App\Models\Estado::where('nombre', 'Completado')->first();
                if ($estadoCompletado) {
                    $afiliado->update(['estado_id' => $estadoCompletado->id]);
                }
            } elseif (in_array($estado, ['No labora en la empresa', 'Número equivocado'])) {
                $estadoCierre = \App\Models\Estado::where('nombre', 'No localizado')->first();
                if ($estadoCierre) {
                    $afiliado->update(['estado_id' => $estadoCierre->id]);
                }
            }

            // Si es efectivo o definitivo, quitamos de la lista activa
            if (in_array($estado, ['Cédula efectiva', 'Tienen carnet', 'No labora en la empresa', 'Número equivocado'])) {
                 AsignacionLlamada::where('afiliado_id', $afiliado->id)
                    ->where('usuario_id', auth()->id())
                    ->update(['activa' => false]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Gestión registrada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function managementTray(Request $request)
    {
        $query = Afiliado::whereIn('estado_id', [
            \App\Models\Estado::where('nombre', 'Cédula Pendiente')->first()?->id,
            \App\Models\Estado::where('nombre', 'Cédula Recibida')->first()?->id,
        ])->with('llamadas', 'empresaModel');

        if ($request->filled('estado')) {
            $query->where('estado_id', $request->estado);
        }

        $afiliados = $query->paginate(20);
        return view('admin.callcenter.management', compact('afiliados'));
    }

    public function updateDocumentStatus(Request $request, Afiliado $afiliado)
    {
        $request->validate(['status' => 'required|string']);
        
        $estadoNombre = $request->status === 'recibida' ? 'Cédula Recibida' : 'Cédula Pendiente';
        $estado = \App\Models\Estado::where('nombre', $estadoNombre)->first();
        
        if ($estado) {
            $afiliado->update(['estado_id' => $estado->id]);
            
            // Si ya está recibida, se considera listo para el carnet y se envía a ruta
            if ($request->status === 'recibida') {
                $enRuta = \App\Models\Estado::where('nombre', 'En ruta')->first();
                if ($enRuta) $afiliado->update(['estado_id' => $enRuta->id]);
            }
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 400);
    }

    public function promoteProspect(Request $request, Afiliado $afiliado)
    {
        $request->validate([
            'cedula' => 'required|string|size:13', // Suponiendo formato con guiones
        ]);

        // Verificar si la cédula ya existe en otro registro
        $duplicado = Afiliado::where('cedula', $request->cedula)->where('id', '!=', $afiliado->id)->first();
        if ($duplicado) {
            return response()->json(['success' => false, 'message' => 'Esta cédula ya existe en el sistema asociada a: ' . $duplicado->nombre_completo], 422);
        }

        DB::beginTransaction();
        try {
            $afiliado->update([
                'cedula' => $request->cedula,
                'estado_id' => 1, // Pendiente (ID estándar para flujo logístico)
            ]);

            // Registrar la primera llamada exitosa
            Llamada::create([
                'afiliado_id' => $afiliado->id,
                'usuario_id' => auth()->id(),
                'estado_llamada' => 'Cédula efectiva',
                'observacion' => 'Cédula obtenida mediante investigación y llamada a empresa.',
                'fecha_llamada' => now(),
            ]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function reception()
    {
        return view('admin.callcenter.reception');
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=plantilla_roberitza_callcenter.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Estructura exacta según DATA ROBERITZA.xlsx (sin NSS_ALT)
        $columns = ['NSS', 'CEDULA', 'POLIZA', 'NOMBRE', 'RNC', 'EMPRESA', 'TELEFONO', 'DIRECCION', 'CELULAR'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function checkProspects(Request $request)
    {
        $request->validate(['data' => 'required|string']);
        $lines = explode("\n", str_replace("\r", "", $request->data));
        
        $results = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $parts = explode("\t", $line); // Excel tabs
            if (count($parts) < 5) $parts = explode(",", $line); // CSV
            
            // Si es el encabezado, ignorar (Buscamos NOMBRE en la posición 3 ahora)
            if (isset($parts[3]) && (strtoupper($parts[3]) === 'NOMBRE' || strtoupper($parts[3]) === 'NOMBRE_COMPLETO')) continue;

            if (count($parts) >= 4) {
                // Mapeo ajustado (sin NSS_ALT): Index 1: CEDULA, Index 3: NOMBRE, Index 4: RNC, Index 5: EMPRESA, Index 6: TEL, Index 8: CEL
                $cedula = isset($parts[1]) ? trim($parts[1]) : null;
                if ($cedula) {
                    $cedula = Afiliado::formatCedula($cedula);
                }
                
                $nombre = trim($parts[3]);
                $rnc = isset($parts[4]) ? trim($parts[4]) : 'N/A';
                $empresa = isset($parts[5]) ? trim($parts[5]) : 'Desconocida';
                $telefono = isset($parts[6]) ? trim($parts[6]) : (isset($parts[8]) ? trim($parts[8]) : null);

                if (empty($nombre)) continue;

                // Buscar por CEDULA formateada
                $existente = null;
                if (!empty($cedula)) {
                    $existente = Afiliado::where('cedula', $cedula)->first();
                }
                
                if (!$existente) {
                    $existente = Afiliado::where('nombre_completo', 'LIKE', "%$nombre%")
                        ->where('rnc_empresa', $rnc)
                        ->first();
                }

                $results[] = [
                    'nombre' => $nombre,
                    'cedula' => $cedula,
                    'rnc' => $rnc,
                    'empresa' => $empresa,
                    'telefono' => $telefono,
                    'status' => $existente ? 'exists' : 'new',
                    'existente_id' => $existente?->id,
                    'existente_estado' => $existente?->estado?->nombre,
                    'asignado_a' => $existente?->asignacionesLlamadas()->where('activa', true)->first()?->usuario?->name,
                ];
            }
        }

        return response()->json($results);
    }

    public function storeProspects(Request $request)
    {
        $request->validate(['prospects' => 'required|array']);
        
        DB::beginTransaction();
        try {
            foreach ($request->prospects as $p) {
                $cedulaRaw = $p['cedula'] ?? null;
                if (!$cedulaRaw) continue;

                // El mutador setCedulaAttribute en el modelo Afiliado se encargará del formateo 000-0000000-0
                // Pero necesitamos la cédula formateada para buscar si existe
                $cedulaFormateada = Afiliado::formatCedula($cedulaRaw);

                // Buscar empresa por RNC para vinculación inteligente
                $empresa = null;
                $rncLimpiado = !empty($p['rnc']) ? preg_replace('/[^0-9]/', '', $p['rnc']) : 'N/A';
                if ($rncLimpiado !== 'N/A') {
                    $empresa = Empresa::where('rnc', $rncLimpiado)->first();
                }

                // El mutador setCedulaAttribute en el modelo Afiliado se encargará del formateo 000-0000000-0
                // Pero necesitamos la cédula formateada para buscar si existe
                $cedulaFormateada = Afiliado::formatCedula($cedulaRaw);

                // Buscar empresa por RNC para vinculación inteligente
                $empresa = null;
                $rncLimpiado = !empty($p['rnc']) ? preg_replace('/[^0-9]/', '', $p['rnc']) : 'N/A';
                if ($rncLimpiado !== 'N/A') {
                    $empresa = Empresa::where('rnc', $rncLimpiado)->first();
                }

                // INTELIGENCIA DE FUSIÓN:
                // 1. Intentar buscar por Cédula (Caso ideal)
                $afiliado = Afiliado::where('cedula', $cedulaFormateada)->first();

                // 2. Si no hay por cédula, intentar por Nombre + RNC donde la cédula sea nula
                // Esto evita duplicados si subieron nombres sin cédula antes
                if (!$afiliado) {
                    $afiliado = Afiliado::where('nombre_completo', 'LIKE', '%' . $p['nombre'] . '%')
                        ->where('rnc_empresa', $rncLimpiado)
                        ->whereNull('cedula')
                        ->first();
                }

                if ($afiliado) {
                    $afiliado->update([
                        'nombre_completo' => $p['nombre'],
                        'cedula' => $cedulaFormateada,
                        'rnc_empresa' => $rncLimpiado,
                        'empresa' => $empresa ? $empresa->nombre : $p['empresa'],
                        'empresa_id' => $empresa?->id,
                        'telefono' => $p['telefono'] ?? ($empresa?->telefono ?? $afiliado->telefono),
                        'estado_id' => !empty($cedulaFormateada) ? 1 : 11, // Si tiene cédula, pasa a Pendiente (1) para llamada
                        'responsable_id' => 1,
                    ]);
                } else {
                    $afiliado = Afiliado::create([
                        'nombre_completo' => $p['nombre'],
                        'cedula' => $cedulaFormateada,
                        'rnc_empresa' => $rncLimpiado,
                        'empresa' => $empresa ? $empresa->nombre : $p['empresa'],
                        'empresa_id' => $empresa?->id,
                        'telefono' => $p['telefono'] ?? ($empresa?->telefono ?? null),
                        'estado_id' => !empty($cedulaFormateada) ? 1 : 11,
                        'responsable_id' => 1,
                    ]);
                }

                // Lógica de Asignación Automática
                $currentUser = auth()->user();
                $isGestor = $currentUser->hasRole('Gestor de Llamadas');

                if ($isGestor) {
                    // Si es gestor, nos aseguramos de que la asignación ACTIVA sea para él
                    // Desactivamos cualquier otra asignación previa para este afiliado
                    AsignacionLlamada::where('afiliado_id', $afiliado->id)
                        ->where('usuario_id', '!=', $currentUser->id)
                        ->update(['activa' => false]);

                    // Buscamos si ya tiene una asignación activa propia
                    $miAsignacion = AsignacionLlamada::where('afiliado_id', $afiliado->id)
                        ->where('usuario_id', $currentUser->id)
                        ->where('activa', true)
                        ->first();

                    if (!$miAsignacion) {
                        AsignacionLlamada::create([
                            'afiliado_id' => $afiliado->id,
                            'usuario_id' => $currentUser->id,
                            'asignador_id' => $currentUser->id,
                            'fecha_asignacion' => now(),
                            'activa' => true
                        ]);
                    }
                } else {
                    // Si no es gestor (admin/supervisor), solo asignamos si no tiene nadie
                    $tieneAsignacionActiva = AsignacionLlamada::where('afiliado_id', $afiliado->id)
                        ->where('activa', true)
                        ->exists();

                    if (!$tieneAsignacionActiva) {
                        AsignacionLlamada::create([
                            'afiliado_id' => $afiliado->id,
                            'usuario_id' => $currentUser->id,
                            'asignador_id' => $currentUser->id,
                            'fecha_asignacion' => now(),
                            'activa' => true
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function prospectingWorklist()
    {
        $user = auth()->user();
        
        // Agrupar por Empresa/RNC
        $prospectos = Afiliado::where('estado_id', 11)
            ->whereHas('asignacionesLlamadas', function($q) use ($user) {
                $q->where('usuario_id', $user->id)->where('activa', true);
            })
            ->get()
            ->groupBy('rnc_empresa');

        return view('admin.callcenter.prospecting_worklist', compact('prospectos'));
    }

    public function getHistory(Afiliado $afiliado)
    {
        $history = $afiliado->llamadas()
            ->with('usuario')
            ->orderBy('fecha_llamada', 'desc')
            ->get()
            ->map(function($l) {
                return [
                    'fecha' => $l->fecha_llamada->format('d/m/Y h:i A'),
                    'estado' => $l->estado_llamada,
                    'observacion' => $l->observacion ?? 'Sin observación',
                    'usuario' => $l->usuario->name ?? 'Sistema',
                    'foto' => $l->evidencia_foto ? asset('storage/' . $l->evidencia_foto) : null,
                ];
            });

        return response()->json($history);
    }
}
