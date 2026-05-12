<?php

namespace App\Http\Controllers\Modules\Asistencia;

use App\Http\Controllers\Controller;
use App\Models\Asistencia\Empleado;
use App\Models\Asistencia\Permiso;
use App\Models\Asistencia\TipoPermiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PermisoController extends Controller
{
    /**
     * Lista de permisos del empleado actual
     */
    public function index()
    {
        $user = Auth::user();
        $empleado = Empleado::where('user_id', $user->id)->firstOrFail();
        $permisos = Permiso::with('tipo')->where('empleado_id', $empleado->id)->orderBy('fecha_desde', 'desc')->get();
        $tipos = TipoPermiso::all();

        return view('modules.asistencia.permisos.index', compact('permisos', 'tipos', 'empleado'));
    }

    /**
     * Procesa la solicitud de permiso
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_permiso_id' => 'required|exists:asistencia_tipos_permiso,id',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'motivo' => 'required|string',
            'evidencia' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $user = Auth::user();
        $empleado = Empleado::where('user_id', $user->id)->firstOrFail();

        $data = $request->all();
        $data['empleado_id'] = $empleado->id;
        $data['estado'] = 'pendiente';

        if ($request->hasFile('evidencia')) {
            $data['evidencia_path'] = $request->file('evidencia')->store('asistencia/evidencias', 'public');
        }

        Permiso::create($data);

        return back()->with('success', 'Solicitud de permiso enviada correctamente.');
    }

    /**
     * Bandeja de aprobación para supervisores
     */
    public function bandeja()
    {
        $user = Auth::user();
        // Ver permisos de los empleados que tienen a este usuario como supervisor
        $solicitudes = Permiso::with(['empleado', 'tipo'])
            ->whereHas('empleado', function($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            })
            ->where('estado', 'pendiente')
            ->get();

        return view('modules.asistencia.permisos.bandeja', compact('solicitudes'));
    }

    /**
     * Aprobar o rechazar permiso
     */
    public function decidir(Request $request, Permiso $permiso)
    {
        $request->validate([
            'estado' => 'required|in:aprobado,rechazado',
            'comentario' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        
        // Auditoría de Seguridad: Verificar que sea el supervisor o un admin
        if ($permiso->empleado->supervisor_id !== $user->id && !$user->hasRole(['Admin', 'Super-Admin'])) {
            abort(403, 'No tienes autorización para decidir sobre esta solicitud.');
        }

        $permiso->update([
            'estado' => $request->estado,
            'aprobado_por' => $user->id,
            'comentario_aprobador' => $request->comentario
        ]);

        return back()->with('success', 'Solicitud actualizada: ' . strtoupper($request->estado));
    }
}
