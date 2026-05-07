<?php

namespace App\Http\Controllers\Modules\CallCenter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CallCenterCarga;
use App\Models\CallCenterRegistro;
use App\Models\CallCenterEstado;
use App\Models\Empresa;
use App\Models\Afiliado;
use App\Models\Corte;
use App\Models\Lote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CallCenterController extends Controller
{
    public function index()
    {
        $cargas = CallCenterCarga::with('user')->latest()->paginate(10);
        return view('modules.call_center.index', compact('cargas'));
    }

    public function create()
    {
        return view('modules.call_center.import');
    }

    public function startBatch(Request $request)
    {
        $request->validate(['nombre_carga' => 'required|string|max:255']);
        
        $carga = CallCenterCarga::create([
            'nombre' => $request->nombre_carga,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'carga_id' => $carga->id]);
    }

    public function processChunk(Request $request)
    {
        $request->validate([
            'carga_id' => 'required|exists:call_center_cargas,id',
            'lines' => 'required|array',
        ]);

        $carga = CallCenterCarga::find($request->carga_id);
        $estadoPendiente = CallCenterEstado::where('nombre', 'Pendiente de gestión')->first();
        
        $stats = [
            'nuevos' => 0,
            'actualizados' => 0,
            'omitidos' => 0,
            'total' => 0
        ];

        DB::beginTransaction();
        try {
            foreach ($request->lines as $line) {
                if (empty(trim($line))) continue;
                
                $parts = explode("\t", $line);
                if (count($parts) < 2) continue;

                $stats['total']++;
                
                $cedula = trim($parts[0] ?? '');
                $poliza = trim($parts[1] ?? '');
                $nombre = trim($parts[2] ?? '');
                
                if (empty($cedula) || empty($nombre)) continue;

                $cedulaLimpia = preg_replace('/[^0-9]/', '', $cedula);
                $afiliadoMaestro = Afiliado::where('cedula', $cedula)->orWhere('cedula', $cedulaLimpia)->first();

                if ($afiliadoMaestro && $afiliadoMaestro->estado_id == 9) {
                    $stats['omitidos']++;
                    continue;
                }

                $empresaRnc = trim($parts[3] ?? '');
                $empresaNombre = trim($parts[4] ?? '');
                $empresa = null;
                if (!empty($empresaRnc)) {
                    $empresa = Empresa::where('rnc', $empresaRnc)->first();
                }
                if (!$empresa && !empty($empresaNombre)) {
                    $empresa = Empresa::where('nombre', 'LIKE', "%$empresaNombre%")->first();
                }

                $registro = CallCenterRegistro::where('cedula', $cedulaLimpia)->first();
                if ($registro) {
                    $registro->update([
                        'nombre' => $nombre,
                        'poliza' => $poliza,
                        'telefono' => trim($parts[5] ?? $registro->telefono),
                        'empresa_direccion' => trim($parts[6] ?? $registro->empresa_direccion),
                        'celular' => trim($parts[7] ?? $registro->celular),
                        'empresa_id' => $empresa?->id,
                        'empresa_rnc' => $empresaRnc,
                        'empresa_nombre' => $empresaNombre,
                    ]);
                    $stats['actualizados']++;
                } else {
                    CallCenterRegistro::create([
                        'carga_id' => $carga->id,
                        'estado_id' => $estadoPendiente->id,
                        'cedula' => $cedulaLimpia,
                        'poliza' => $poliza,
                        'nombre' => $nombre,
                        'telefono' => trim($parts[5] ?? ''),
                        'empresa_direccion' => trim($parts[6] ?? ''),
                        'celular' => trim($parts[7] ?? ''),
                        'empresa_id' => $empresa?->id,
                        'empresa_rnc' => $empresaRnc,
                        'empresa_nombre' => $empresaNombre,
                        'uuid' => (string) Str::uuid(),
                    ]);
                    $stats['nuevos']++;
                }
            }

            // Actualizar acumulados de la carga
            $carga->increment('total_registros', $stats['nuevos'] + $stats['actualizados']);
            $carga->increment('registros_nuevos', $stats['nuevos']);
            $carga->increment('registros_actualizados', $stats['actualizados']);

            DB::commit();
            return response()->json(['success' => true, 'stats' => $stats]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function importData(Request $request)
    {
        $request->validate([
            'nombre_carga' => 'required|string|max:255',
            'data_raw' => 'required|string',
        ]);

        $lines = explode("\n", str_replace("\r", "", $request->data_raw));
        $header = true;
        
        $stats = [
            'total' => 0,
            'nuevos' => 0,
            'actualizados' => 0,
            'duplicados' => 0,
            'omitidos' => 0,
        ];

        DB::beginTransaction();
        try {
            $carga = CallCenterCarga::create([
                'nombre' => $request->nombre_carga,
                'user_id' => auth()->id(),
            ]);

            $estadoPendiente = CallCenterEstado::where('nombre', 'Pendiente de gestión')->first();

            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                $parts = explode("\t", $line); // Soporte para copiar de Excel
                if (count($parts) < 2) continue; // Línea inválida

                // Saltar encabezado si existe
                if ($header && (str_contains(strtolower($parts[0]), 'cedula') || str_contains(strtolower($parts[1]), 'nombre'))) {
                    $header = false;
                    continue;
                }

                $stats['total']++;
                
                // Nuevo Mapeo solicitado por el usuario:
                // 0: CEDULA, 1: POLIZA, 2: NOMBRE, 3: RNC_EMPRESA, 4: NOMBRE_EMPRESA, 5: TELEFONO, 6: DIRECCION, 7: CELULAR
                $cedula = trim($parts[0] ?? '');
                $poliza = trim($parts[1] ?? '');
                $nombre = trim($parts[2] ?? '');
                
                if (empty($cedula) || empty($nombre)) continue;

                // 1. Limpiar y validar cédula
                $cedulaLimpia = preg_replace('/[^0-9]/', '', $cedula);
                
                // 2. Verificar duplicados en la base maestra de Afiliados
                $afiliadoMaestro = Afiliado::where('cedula', $cedula)->orWhere('cedula', $cedulaLimpia)->first();

                // 2.1 VALIDACIÓN EN TIEMPO REAL: Si ya está completado en el sistema maestro, omitimos.
                if ($afiliadoMaestro && $afiliadoMaestro->estado_id == 9) {
                    $stats['omitidos']++;
                    continue;
                }

                // 3. Verificar si la empresa existe por RNC o Nombre
                $empresaRnc = trim($parts[3] ?? '');
                $empresaNombre = trim($parts[4] ?? '');
                
                $empresa = null;
                if (!empty($empresaRnc)) {
                    $empresa = Empresa::where('rnc', $empresaRnc)->first();
                }
                if (!$empresa && !empty($empresaNombre)) {
                    $empresa = Empresa::where('nombre', 'LIKE', "%$empresaNombre%")->first();
                }

                // 4. Buscar si ya existe en registros de Call Center (para actualizar)
                $registroExistente = CallCenterRegistro::where('cedula', $cedula)
                    ->orWhere('cedula', $cedulaLimpia)
                    ->first();

                if ($registroExistente) {
                    $registroExistente->update([
                        'poliza' => $poliza ?: $registroExistente->poliza,
                        'nombre' => $nombre,
                        'telefono' => trim($parts[5] ?? $registroExistente->telefono),
                        'celular' => trim($parts[7] ?? $registroExistente->celular),
                        'empresa_nombre' => $empresaNombre,
                        'empresa_rnc' => $empresaRnc,
                        'empresa_direccion' => trim($parts[6] ?? $registroExistente->empresa_direccion),
                        'empresa_id' => $empresa?->id ?? $registroExistente->empresa_id,
                        'afiliado_id' => $afiliadoMaestro?->uuid ?? $registroExistente->afiliado_id,
                        'ultima_gestion_at' => now(),
                        'operador_id' => $registroExistente->operador_id ?? (auth()->user()->hasRole('Operador Call Center') ? auth()->id() : null),
                    ]);
                    $stats['actualizados']++;
                } else {
                    CallCenterRegistro::create([
                        'carga_id' => $carga->id,
                        'estado_id' => $estadoPendiente->id,
                        'cedula' => $cedula,
                        'poliza' => $poliza,
                        'nombre' => $nombre,
                        'telefono' => trim($parts[5] ?? null),
                        'celular' => trim($parts[7] ?? null),
                        'empresa_nombre' => $empresaNombre,
                        'empresa_rnc' => $empresaRnc,
                        'empresa_direccion' => trim($parts[6] ?? null),
                        'empresa_id' => $empresa?->id,
                        'afiliado_id' => $afiliadoMaestro?->uuid,
                        'operador_id' => auth()->user()->hasRole('Operador Call Center') ? auth()->id() : null,
                    ]);
                    $stats['nuevos']++;
                }
            }

            $carga->update([
                'total_registros' => $stats['total'],
                'registros_nuevos' => $stats['nuevos'],
                'registros_actualizados' => $stats['actualizados'],
            ]);

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => "Carga completada: {$stats['nuevos']} nuevos, {$stats['actualizados']} actualizados.",
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function worklist(Request $request)
    {
        $query = CallCenterRegistro::with(['estado', 'carga', 'operador', 'empresa'])
            ->orderBy('prioridad', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nombre', 'LIKE', "%$s%")
                  ->orWhere('cedula', 'LIKE', "%$s%")
                  ->orWhere('poliza', 'LIKE', "%$s%");
            });
        }

        // Filtros obligatorios
        if (!auth()->user()->hasRole('Supervisor Call Center')) {
            $query->where('operador_id', auth()->id());
        } elseif ($request->filled('operador_id')) {
            $query->where('operador_id', $request->operador_id);
        }

        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('provincia')) {
            $query->where('provincia', $request->provincia);
        }

        if ($request->filled('municipio')) {
            $query->where('municipio', $request->municipio);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $registros = $query->paginate(20)->withQueryString();
        
        $estados = CallCenterEstado::orderBy('orden')->get();
        $empresas = Empresa::orderBy('nombre')->get();
        $operadores = \App\Models\User::role('Operador Call Center')->get();

        return view('modules.call_center.worklist', compact('registros', 'estados', 'empresas', 'operadores'));
    }

    public function manage(CallCenterRegistro $registro)
    {
        $registro->load(['estado', 'gestiones.operador', 'gestiones.estadoAnterior', 'gestiones.estadoNuevo', 'documentos', 'despacho']);
        $estados = CallCenterEstado::orderBy('orden')->get();
        
        // Buscar otros prospectos de la misma empresa para dar contexto al operador
        $companeros = [];
        if ($registro->empresa_id || $registro->empresa_rnc) {
            $companeros = CallCenterRegistro::with('estado')
                ->where(function($q) use ($registro) {
                    if ($registro->empresa_id) $q->where('empresa_id', $registro->empresa_id);
                    else $q->where('empresa_rnc', $registro->empresa_rnc);
                })
                ->where('id', '!=', $registro->id)
                ->limit(10)
                ->get();
        }
        
        return view('modules.call_center.manage', compact('registro', 'estados', 'companeros'));
    }

    public function storeGestion(Request $request, CallCenterRegistro $registro)
    {
        $request->validate([
            'estado_nuevo_id' => 'required|exists:call_center_estados,id',
            'resultado_contacto' => 'required|string',
            'observacion' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $estadoAnteriorId = $registro->estado_id;
            
            // Registrar Gestión
            $registro->gestiones()->create([
                'operador_id' => auth()->id(),
                'estado_anterior_id' => $estadoAnteriorId,
                'estado_nuevo_id' => $request->estado_nuevo_id,
                'tipo_contacto' => $request->tipo_contacto ?? 'Llamada',
                'resultado_contacto' => $request->resultado_contacto,
                'telefono_contactado' => $request->telefono_contactado,
                'persona_contactada' => $request->persona_contactada,
                'observacion' => $request->observacion,
                'fecha_proximo_contacto' => $request->fecha_proximo_contacto,
            ]);

            // Enriquecimiento de Datos: Actualizar Registro y opcionalmente la Empresa
            $updateData = [
                'estado_id' => $request->estado_nuevo_id,
                'intentos_llamada' => $registro->intentos_llamada + 1,
                'ultima_gestion_at' => now(),
                'proximo_contacto_at' => $request->fecha_proximo_contacto,
                'telefono' => $request->telefono_contactado ?: $registro->telefono,
                'celular' => $request->celular_contactado ?: $registro->celular,
            ];

            if ($request->filled('direccion_contactada')) {
                $updateData['empresa_direccion'] = $request->direccion_contactada;
                
                // Si se marcó el checkbox de actualizar empresa
                if ($request->actualizar_empresa && $registro->empresa_id) {
                    $registro->empresa->update([
                        'direccion' => $request->direccion_contactada,
                        'telefono' => $request->telefono_contactado ?: $registro->empresa->telefono
                    ]);
                }
            }

            $registro->update($updateData);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Gestión registrada correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateDocument(Request $request, CallCenterRegistro $registro)
    {
        $request->validate([
            'nombre_documento' => 'required|string',
            'estado' => 'required|in:No solicitado,Solicitado,Recibido parcial,Recibido completo,Rechazado',
            'archivo' => 'nullable|file|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('call_center_docs', 'public');
        }

        $registro->documentos()->updateOrCreate(
            ['nombre_documento' => $request->nombre_documento],
            [
                'estado' => $request->estado,
                'path_archivo' => $path ?? DB::raw('path_archivo'),
                'observacion' => $request->observacion,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Documento actualizado.']);
    }

    public function promoteToCarnet(Request $request, CallCenterRegistro $registro)
    {
        DB::beginTransaction();
        try {
            // 0. Preparar contexto de Carnetización (Corte y Lote Dedicado)
            $corte = Corte::where('nombre', 'Promociones Call Center')->first();
            if (!$corte) {
                // Fallback de seguridad por si se eliminó el corte
                $corte = Corte::create([
                    'nombre' => 'Promociones Call Center',
                    'fecha_inicio' => '2026-01-01',
                    'fecha_fin' => '2030-12-31',
                    'activo' => true
                ]);
            }

            // Buscar o crear un lote para promociones del Call Center
            $lote = Lote::firstOrCreate(
                ['nombre' => "Bandeja de Entrada - Call Center", 'corte_id' => $corte->id],
                [
                    'empresa_tipo' => 'CMD',
                    'user_id' => auth()->id(),
                    'total_registros' => 0,
                    'observaciones' => 'Bandeja centralizada para registros promovidos desde Call Center.'
                ]
            );

            // 1. Gestionar Empresa (Priorizar RNC)
            $empresa = $registro->empresa;
            if (!$empresa && (!empty($registro->empresa_rnc) || !empty($registro->empresa_nombre))) {
                if (!empty($registro->empresa_rnc)) {
                    $empresa = Empresa::where('rnc', $registro->empresa_rnc)->first();
                }
                
                if (!$empresa && !empty($registro->empresa_nombre)) {
                    $empresa = Empresa::where('nombre', 'LIKE', "%{$registro->empresa_nombre}%")->first();
                }

                if (!$empresa) {
                    $empresa = Empresa::create([
                        'nombre' => $registro->empresa_nombre ?? 'Empresa Nueva (Importada)',
                        'rnc' => $registro->empresa_rnc ?? 'N/A',
                        'direccion' => $registro->empresa_direccion,
                        'provincia' => $registro->provincia,
                        'municipio' => $registro->municipio,
                        'contacto_nombre' => $registro->empresa_contacto,
                    ]);
                }
            }

            // 2. Gestionar Afiliado (Mapeo completo para evitar pérdida de datos)
            $afiliado = Afiliado::where('cedula', $registro->cedula)->first();
            
            $dataAfiliado = [
                'nombre_completo' => $registro->nombre,
                'telefono' => $registro->telefono ?: $registro->celular,
                'direccion' => $registro->empresa_direccion,
                'poliza' => $registro->poliza,
                'provincia' => $registro->provincia,
                'municipio' => $registro->municipio,
                'empresa_id' => $empresa?->id,
                'corte_id' => $corte->id,
                'lote_id' => $lote->id,
                'responsable_id' => auth()->user()->responsable_id ?? 1, // Default a 1 (CMD) si no hay responsable
            ];

            if ($afiliado) {
                $afiliado->update($dataAfiliado);
            } else {
                $dataAfiliado['cedula'] = $registro->cedula;
                $dataAfiliado['estado_id'] = 1; // Pendiente de carnetización
                $afiliado = Afiliado::create($dataAfiliado);
            }

            // Incrementar contador del lote
            $lote->increment('total_registros');

            // 3. Registrar en Bandeja de Salida
            $registro->bandejaSalida()->create([
                'fecha_envio' => now(),
                'enviado_por' => auth()->id(),
            ]);

            // 4. Actualizar estado del registro
            $estadoEnviado = CallCenterEstado::where('nombre', 'Enviado a carnetización')->first();
            $registro->update([
                'estado_id' => $estadoEnviado->id,
                'afiliado_id' => $afiliado->uuid,
                'empresa_id' => $empresa?->id,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Promovido a carnetización correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function stats()
    {
        // Funnel de Conversión
        $funnel = [
            'total' => CallCenterRegistro::count(),
            'contactados' => CallCenterRegistro::whereHas('gestiones')->count(),
            'promovidos' => CallCenterRegistro::whereHas('bandejaSalida')->count(),
        ];

        // Rendimiento por Operador
        $operadores = \App\Models\User::role('Operador Call Center')
            ->withCount(['gestionesCallCenter as gestiones_totales'])
            ->withCount(['promocionesCallCenter as promociones_totales'])
            ->get()
            ->sortByDesc('gestiones_totales');

        return view('modules.call_center.stats', compact('funnel', 'operadores'));
    }
}
