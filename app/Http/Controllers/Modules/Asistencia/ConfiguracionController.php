<?php

namespace App\Http\Controllers\Modules\Asistencia;

use App\Http\Controllers\Controller;
use App\Models\Asistencia\Turno;
use App\Models\Asistencia\Configuracion;
use App\Models\Asistencia\Departamento;
use App\Models\Asistencia\Cargo;
use App\Models\Asistencia\Empleado;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Centro de Control de Asistencia
     */
    public function index()
    {
        $turnos = Turno::all();
        $departamentos = Departamento::withCount('cargos')->get();
        $empleados = Empleado::with(['cargo', 'turno'])->where('estado', 'activo')->get();
        
        $configs = [
            'tolerancia' => Configuracion::get('asistencia_tolerancia', 15),
            'almuerzo_defecto' => Configuracion::get('asistencia_almuerzo_minutos', 60),
            'solo_ip_oficina' => Configuracion::get('asistencia_solo_ip_oficina', false),
        ];

        return view('modules.asistencia.configuracion.index', compact('turnos', 'departamentos', 'configs', 'empleados'));
    }

    /**
     * Gestión de Turnos (Crear/Editar)
     */
    public function saveTurno(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:asistencia_turnos,id',
            'nombre' => 'required|string',
            'entrada_esperada' => 'required',
            'salida_esperada' => 'required',
            'tolerancia_minutos' => 'required|integer',
            'minutos_almuerzo' => 'required|integer',
        ]);

        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        foreach($dias as $dia) {
            $data[$dia] = $request->has($dia);
        }

        Turno::updateOrCreate(['id' => $request->id], $data);

        return back()->with('success', 'Turno actualizado correctamente.');
    }

    /**
     * Asignar turno a un empleado
     */
    public function asignarTurno(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:asistencia_empleados,id',
            'turno_id' => 'required|exists:asistencia_turnos,id',
        ]);

        $empleado = Empleado::findOrFail($request->empleado_id);
        $empleado->update(['turno_id' => $request->turno_id]);

        return response()->json(['success' => true, 'message' => 'Turno asignado correctamente.']);
    }

    /**
     * Actualiza configuraciones globales
     */
    public function updateGlobal(Request $request)
    {
        Configuracion::set('asistencia_tolerancia', $request->tolerancia);
        Configuracion::set('asistencia_almuerzo_minutos', $request->almuerzo_defecto);
        Configuracion::set('asistencia_solo_ip_oficina', $request->has('solo_ip_oficina'));

        return back()->with('success', 'Configuración global actualizada.');
    }
}
