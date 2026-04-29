<?php

namespace App\Http\Controllers\Modules\Afiliacion;

use App\Http\Controllers\Controller;
use App\Models\SolicitudAfiliacion;
use App\Models\TipoSolicitudAfiliacion;
use App\Models\DocumentoSolicitudAfiliacion;
use App\Models\HistorialSolicitudAfiliacion;
use App\Models\User;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = SolicitudAfiliacion::with(['tipoSolicitud', 'solicitante.roles', 'asignado.roles'])
            ->orderBy('created_at', 'desc');

        // Aislamiento por Departamento / Rol
        if ($user->hasRole('Admin')) {
            // El admin ve todo
        } elseif ($user->departamento && in_array($user->departamento->codigo, ['AFIL', 'AUTOR', 'AUDIT'])) {
            // Analistas operativos ven lo que les llega a su área
            $query->where('departamento_id', $user->departamento_id);
        } elseif ($user->departamento && str_contains($user->departamento->nombre, 'Servicio al Cliente')) {
            // Si es de Servicio al Cliente, ve lo de su equipo (si es supervisor) o solo lo suyo
            if ($user->hasRole(['Supervisor de Servicio al Cliente'])) {
                $query->whereHas('solicitante', function($q) use ($user) {
                    $q->where('departamento_id', $user->departamento_id);
                });
            } else {
                $query->where('solicitante_user_id', $user->id);
            }
        } else {
            // Otros solo ven lo que ellos crearon
            $query->where('solicitante_user_id', $user->id);
        }

        // Filtros adicionales
        if ($request->estado) {
            $query->where('estado', $request->estado);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('cedula', 'like', "%{$request->search}%")
                  ->orWhere('nombre_completo', 'like', "%{$request->search}%")
                  ->orWhere('codigo_solicitud', 'like', "%{$request->search}%");
            });
        }

        $solicitudes = $query->paginate(15);

        // Si se solicita vista de tablero, agrupamos por estado
        $tablero = null;
        if ($request->view === 'kanban') {
            $tableroQuery = clone $query;
            $tablero = $tableroQuery->get()->groupBy('estado');
        }
        
        // Stats filtrados por el mismo criterio de visibilidad
        $statsQuery = SolicitudAfiliacion::query();
        if (!$user->hasRole('Admin')) {
            if ($user->departamento && in_array($user->departamento->codigo, ['AFIL', 'AUTOR', 'AUDIT'])) {
                $statsQuery->where('departamento_id', $user->departamento_id);
            } elseif ($user->departamento && str_contains($user->departamento->nombre, 'Servicio al Cliente')) {
                if ($user->hasRole(['Supervisor de Servicio al Cliente'])) {
                    $statsQuery->whereHas('solicitante', function($q) use ($user) {
                        $q->where('departamento_id', $user->departamento_id);
                    });
                } else {
                    $statsQuery->where('solicitante_user_id', $user->id);
                }
            } else {
                $statsQuery->where('solicitante_user_id', $user->id);
            }
        }

        $stats = [
            'pendientes' => (clone $statsQuery)->where('estado', 'Pendiente')->count(),
            'en_revision' => (clone $statsQuery)->where('estado', 'En revisión')->count(),
            'aprobadas' => (clone $statsQuery)->where('estado', 'Aprobada')->count(),
            'devueltas' => (clone $statsQuery)->where('estado', 'Devuelta')->count(),
        ];

        $usuarios = User::role(['Analista de Afiliación', 'Supervisor de Afiliación', 'Admin'])->get();

        return view('modules.afiliacion.index', compact('solicitudes', 'stats', 'tablero', 'usuarios'));
    }

    public function searchAfiliado(Request $request)
    {
        $cedula = $request->cedula;
        if (!$cedula) return response()->json(null);

        // Buscar en la tabla maestra de afiliados
        $afiliado = Afiliado::where('cedula', $cedula)->first();

        if ($afiliado) {
            return response()->json([
                'nombre_completo' => $afiliado->nombre_completo,
                'telefono' => $afiliado->telefono,
                'empresa' => $afiliado->empresa,
                'rnc_empresa' => $afiliado->rnc_empresa,
            ]);
        }

        return response()->json(null);
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'user_id' => 'required|exists:users,id'
        ]);

        $ids = explode(',', $request->ids);
        $user = User::findOrFail($request->user_id);

        return DB::transaction(function() use ($ids, $user) {
            SolicitudAfiliacion::whereIn('id', $ids)->update([
                'asignado_user_id' => $user->id,
                'estado' => 'Asignada'
            ]);

            foreach($ids as $id) {
                HistorialSolicitudAfiliacion::create([
                    'solicitud_id' => $id,
                    'user_id' => auth()->id(),
                    'accion' => 'Asignación Masiva',
                    'comentario' => 'Asignada masivamente a ' . $user->name,
                    'estado_nuevo' => 'Asignada'
                ]);
            }

            return back()->with('success', count($ids) . ' solicitudes asignadas correctamente.');
        });
    }

    public function create()
    {
        $tipos = TipoSolicitudAfiliacion::where('activo', true)->with('documentosRequeridos')->get();
        return view('modules.afiliacion.create', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_solicitud_id' => 'required|exists:tipos_solicitud_afiliacion,id',
            'cedula' => 'required',
            'nombre_completo' => 'required',
            'prioridad' => 'required',
            'expediente_pdf' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480', // 20MB
        ]);

        try {
            return DB::transaction(function() use ($request) {
                $tipo = TipoSolicitudAfiliacion::find($request->tipo_solicitud_id);
                $depAfiliacion = Departamento::where('codigo', 'AFIL')->first();
                
                // Control de Duplicidad
                $duplicate = SolicitudAfiliacion::where('cedula', $request->cedula)
                    ->whereNotIn('estado', ['Aprobada', 'Rechazada', 'Cancelada', 'Cerrada'])
                    ->first();

                if ($duplicate) {
                    throw new \Exception("Ya existe una solicitud activa para esta cédula ({$duplicate->codigo_solicitud}). Por favor, verifique el estado del trámite anterior.");
                }

                // Generar Código
                $count = SolicitudAfiliacion::whereYear('created_at', now()->year)->count() + 1;
                $codigo = 'AFIL-' . now()->format('ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                $solicitud = SolicitudAfiliacion::create([
                    'codigo_solicitud' => $codigo,
                    'tipo_solicitud_id' => $request->tipo_solicitud_id,
                    'departamento_id' => $depAfiliacion?->id,
                    'solicitante_user_id' => auth()->id(),
                    'cedula' => $request->cedula,
                    'nombre_completo' => $request->nombre_completo,
                    'telefono' => $request->telefono,
                    'correo' => $request->correo,
                    'empresa' => $request->empresa,
                    'rnc_empresa' => $request->rnc_empresa,
                    'prioridad' => $request->prioridad,
                    'observacion_solicitante' => $request->observacion_solicitante,
                    'estado' => $request->save_as_draft ? 'Borrador' : 'Pendiente',
                    'sla_fecha_limite' => now()->addHours($tipo->sla_horas),
                ]);

                // Procesar Expediente PDF Único
                if ($request->hasFile('expediente_pdf')) {
                    $file = $request->file('expediente_pdf');
                    $path = $file->store('solicitudes/' . $solicitud->id, 'public');
                    
                    DocumentoSolicitudAfiliacion::create([
                        'solicitud_id' => $solicitud->id,
                        'documento_requerido_id' => null, // No vinculado a un requisito específico
                        'archivo_path' => $path,
                        'nombre_original' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }

                // Historial
                HistorialSolicitudAfiliacion::create([
                    'solicitud_id' => $solicitud->id,
                    'user_id' => auth()->id(),
                    'accion' => $request->save_as_draft ? 'Creada como Borrador' : 'Enviada para Revisión',
                    'estado_nuevo' => $solicitud->estado,
                ]);

                // Notificar a analistas y supervisores del departamento
                if (!$request->save_as_draft) {
                    $notificables = User::where('departamento_id', $solicitud->departamento_id)
                        ->where('id', '!=', auth()->id())
                        ->get();
                    
                    \Illuminate\Support\Facades\Notification::send($notificables, new \App\Notifications\Modules\Afiliacion\SolicitudCreada($solicitud));
                }

                return redirect()->route('solicitudes-afiliacion.index')->with('success', 'Solicitud registrada correctamente.');
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(SolicitudAfiliacion $solicitud)
    {
        // Solo el solicitante puede editar si está en Borrador o Devuelta
        if ($solicitud->solicitante_user_id != auth()->id()) {
            abort(403, 'No tienes permiso para editar esta solicitud.');
        }

        if (!in_array($solicitud->estado, ['Borrador', 'Devuelta'])) {
            return redirect()->route('solicitudes-afiliacion.show', $solicitud)
                ->with('error', 'Esta solicitud no puede ser editada en su estado actual.');
        }

        $tipos = TipoSolicitudAfiliacion::where('activo', true)->with('documentosRequeridos')->get();
        return view('modules.afiliacion.edit', compact('solicitud', 'tipos'));
    }

    public function update(Request $request, SolicitudAfiliacion $solicitud)
    {
        $request->validate([
            'tipo_solicitud_id' => 'required|exists:tipos_solicitud_afiliacion,id',
            'expediente_pdf' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
        ]);

        try {
            return DB::transaction(function() use ($request, $solicitud) {
                $estadoAnterior = $solicitud->estado;
                
                // Auditoría Detallada
                $camposAudit = ['cedula', 'nombre_completo', 'prioridad', 'tipo_solicitud_id', 'empresa', 'correo'];
                $cambios = [];
                foreach($camposAudit as $campo) {
                    if($request->has($campo) && $request->$campo != $solicitud->$campo) {
                        $cambios[$campo] = [
                            'anterior' => $solicitud->$campo,
                            'nuevo' => $request->$campo
                        ];
                    }
                }

                // Gestión de Pausa de SLA si vuelve de Devuelta -> Pendiente
                if ($solicitud->estado == 'Devuelta' && !$request->save_as_draft) {
                    if ($solicitud->pausado_at) {
                        $segundosPausados = now()->diffInSeconds($solicitud->pausado_at);
                        $solicitud->sla_acumulado_segundos += $segundosPausados;
                        $solicitud->sla_fecha_limite = $solicitud->sla_fecha_limite->addSeconds($segundosPausados);
                        $solicitud->pausado_at = null;
                    }
                }

                $nuevoEstado = $request->save_as_draft ? $solicitud->estado : 'Pendiente';
                $nuevaPrioridad = $request->prioridad;

                // 🚀 FAST TRACK: Si viene de devolución, entra con prioridad máxima
                if ($estadoAnterior == 'Devuelta' && !$request->save_as_draft) {
                    $nuevaPrioridad = 'Urgente';
                    if ($solicitud->asignado_user_id) {
                        $nuevoEstado = 'En revisión';
                    }
                }

                $solicitud->update([
                    'tipo_solicitud_id' => $request->tipo_solicitud_id,
                    'cedula' => $request->cedula,
                    'nombre_completo' => $request->nombre_completo,
                    'telefono' => $request->telefono,
                    'correo' => $request->correo,
                    'empresa' => $request->empresa,
                    'rnc_empresa' => $request->rnc_empresa,
                    'prioridad' => $nuevaPrioridad,
                    'observacion_solicitante' => $request->observacion_solicitante,
                    'estado' => $nuevoEstado,
                ]);

                if ($request->hasFile('expediente_pdf')) {
                    $file = $request->file('expediente_pdf');
                    $path = $file->store('solicitudes/' . $solicitud->id, 'public');
                    
                    DocumentoSolicitudAfiliacion::create([
                        'solicitud_id' => $solicitud->id,
                        'documento_requerido_id' => null,
                        'archivo_path' => $path,
                        'nombre_original' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }

                // Historial con lógica de Fast Track
                $accion = $request->save_as_draft ? 'Actualizada (Borrador)' : 'Enviada para Revisión';
                if ($estadoAnterior == 'Devuelta' && !$request->save_as_draft) {
                    $accion = 'Re-entrada Prioritaria (Fast Track)';
                }

                HistorialSolicitudAfiliacion::create([
                    'solicitud_id' => $solicitud->id,
                    'user_id' => auth()->id(),
                    'accion' => $accion,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo' => $solicitud->estado,
                    'detalles' => !empty($cambios) ? $cambios : null
                ]);

                return redirect()->route('solicitudes-afiliacion.index')->with('success', 'Solicitud actualizada correctamente.');
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(SolicitudAfiliacion $solicitud)
    {
        $solicitud->load(['tipoSolicitud', 'solicitante.roles', 'asignado.roles', 'documentos.requerimiento', 'historial.user']);
        $departamentos = Departamento::where('activo', true)->get();
        return view('modules.afiliacion.show', compact('solicitud', 'departamentos'));
    }

    public function assign(SolicitudAfiliacion $solicitud)
    {
        $this->authorize('solicitudes_afiliacion.asignarse');
        $user = auth()->user();

        // Validar que el usuario pertenece al departamento responsable de la solicitud
        if ($solicitud->departamento_id != $user->departamento_id && !$user->hasRole('Admin')) {
            return back()->with('error', 'No tienes permiso para gestionar solicitudes de otro departamento.');
        }

        $solicitud->update([
            'asignado_user_id' => auth()->id(),
            'fecha_asignacion' => now(),
            'estado' => 'En revisión'
        ]);

        HistorialSolicitudAfiliacion::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => auth()->id(),
            'accion' => 'Auto-asignación y cambio a revisión',
            'estado_anterior' => 'Pendiente',
            'estado_nuevo' => 'En revisión',
        ]);

        return back()->with('success', 'Caso asignado correctamente.');
    }

    public function approve(SolicitudAfiliacion $solicitud)
    {
        $solicitud->update([
            'estado' => 'Aprobada',
            'fecha_cierre' => now(),
        ]);

        HistorialSolicitudAfiliacion::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => auth()->id(),
            'accion' => 'Aprobación final del trámite',
            'estado_anterior' => 'En revisión',
            'estado_nuevo' => 'Aprobada',
        ]);

        return back()->with('success', 'Solicitud aprobada correctamente.');
    }

    public function reject(Request $request, SolicitudAfiliacion $solicitud)
    {
        $request->validate(['motivo' => 'required']);

        $solicitud->update([
            'estado' => 'Rechazada',
            'fecha_cierre' => now(),
            'motivo_rechazo' => $request->motivo
        ]);

        HistorialSolicitudAfiliacion::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => auth()->id(),
            'accion' => 'Solicitud Rechazada',
            'comentario' => $request->motivo,
            'estado_nuevo' => 'Rechazada',
        ]);

        return back()->with('success', 'Solicitud rechazada.');
    }

    public function return(Request $request, SolicitudAfiliacion $solicitud)
    {
        $request->validate(['motivo' => 'required']);

        $solicitud->update([
            'estado' => 'Devuelta',
            'motivo_devolucion' => $request->motivo,
            'pausado_at' => now() // Pausar SLA al devolver
        ]);

        HistorialSolicitudAfiliacion::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => auth()->id(),
            'accion' => 'Solicitud Devuelta para corrección',
            'comentario' => $request->motivo,
            'estado_nuevo' => 'Devuelta',
        ]);

        return back()->with('success', 'Solicitud devuelta al remitente.');
    }

    public function validateDocument(Request $request, SolicitudAfiliacion $solicitud, DocumentoSolicitudAfiliacion $documento)
    {
        $documento->update([
            'validacion_estado' => $request->estado,
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function escalate(Request $request, SolicitudAfiliacion $solicitud)
    {
        $request->validate([
            'departamento_id' => 'required|exists:departamentos,id',
            'motivo' => 'required|string|min:10'
        ]);

        $deptoDestino = Departamento::find($request->departamento_id);

        $solicitud->update([
            'departamento_id' => $request->departamento_id,
            'asignado_user_id' => null, // Se libera para que el nuevo departamento la tome
            'estado' => 'Escalada',
        ]);

        HistorialSolicitudAfiliacion::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => auth()->id(),
            'accion' => "Escalada a {$deptoDestino->nombre}",
            'comentario' => $request->motivo,
            'estado_nuevo' => 'Escalada',
        ]);

        return redirect()->route('solicitudes-afiliacion.index')->with('success', "Solicitud escalada a {$deptoDestino->nombre}.");
    }

    public function viewDocumento(DocumentoSolicitudAfiliacion $documento)
    {
        if (!$documento->archivo_path || !Storage::disk('public')->exists($documento->archivo_path)) {
            abort(404, 'Documento no encontrado.');
        }

        return Storage::disk('public')->response($documento->archivo_path);
    }

    public function checkStats()
    {
        $user = auth()->user();
        $statsQuery = SolicitudAfiliacion::query();
        
        if (!$user->hasRole('Admin')) {
            if ($user->departamento && in_array($user->departamento->codigo, ['AFIL', 'AUTOR'])) {
                $statsQuery->where('departamento_id', $user->departamento_id);
            } else {
                $statsQuery->where('solicitante_user_id', $user->id);
            }
        }

        return response()->json([
            'pendientes' => (clone $statsQuery)->where('estado', 'Pendiente')->count(),
        ]);
    }
}
