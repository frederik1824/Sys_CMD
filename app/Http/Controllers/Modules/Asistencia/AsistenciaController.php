<?php

namespace App\Http\Controllers\Modules\Asistencia;

use App\Http\Controllers\Controller;
use App\Models\Asistencia\Empleado;
use App\Models\Asistencia\Registro;
use App\Http\Requests\Modules\Asistencia\MarcarRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    /**
     * Dashboard Principal para Administradores y Supervisores
     * Optimización: Eager loading anidado para evitar N+1
     */
    public function dashboard()
    {
        $hoy = Carbon::today();
        
        // Estadísticas Críticas
        $stats = [
            'total_empleados' => Empleado::where('estado', 'activo')->count(),
            'presentes' => Registro::where('fecha', $hoy)->whereNotNull('hora_entrada')->whereNull('hora_salida')->count(),
            'ausentes' => Empleado::where('estado', 'activo')
                ->whereDoesntHave('registros', function($q) use ($hoy) {
                    $q->where('fecha', $hoy);
                })->count(),
            'tardanzas_hoy' => Registro::where('fecha', $hoy)->where('minutos_tardanza', '>', 0)->count(),
            'salidas_tempranas' => Registro::where('fecha', $hoy)->where('minutos_salida_temprana', '>', 0)->count(),
        ];

        $stats['porcentaje_asistencia'] = $stats['total_empleados'] > 0 
            ? round((($stats['total_empleados'] - $stats['ausentes']) / $stats['total_empleados']) * 100, 1) 
            : 0;

        // Lista de personal presente hoy (Optimizado con eager loading anidado)
        $presentes = Registro::with(['empleado.cargo.departamento', 'empleado.turno'])
            ->where('fecha', $hoy)
            ->orderBy('hora_entrada', 'desc')
            ->get();

        return view('modules.asistencia.dashboard', compact('stats', 'presentes'));
    }

    /**
     * Vista del Reloj Checador para el Empleado
     */
    public function index()
    {
        $user = Auth::user();
        $empleado = Empleado::with(['cargo', 'turno', 'supervisor'])->where('user_id', $user->id)->first();

        if (!$empleado) {
            return redirect()->route('dashboard')->with('error', 'No estás registrado como empleado en el sistema de asistencia.');
        }

        $hoy = Carbon::today();
        $registro = Registro::where('empleado_id', $empleado->id)->where('fecha', $hoy)->first();

        // Buscar registros que requieran justificación
        $pendienteJustificar = Registro::where('empleado_id', $empleado->id)
            ->where('requiere_justificacion', true)
            ->whereNull('justificacion_empleado')
            ->first();

        return view('modules.asistencia.index', compact('empleado', 'registro', 'pendienteJustificar'));
    }

    /**
     * Procesa la marca de tiempo (Entrada, Almuerzo, Salida)
     * Auditoría: Uso de Transacciones y FormRequest
     */
    public function marcar(MarcarRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $tipo = $request->input('tipo');
            $user = Auth::user();
            $empleado = Empleado::where('user_id', $user->id)->firstOrFail();
            $hoy = Carbon::today();
            $ahora = Carbon::now();

            $registro = Registro::firstOrNew([
                'empleado_id' => $empleado->id,
                'fecha' => $hoy
            ]);

            // Auditoría Lógica: Evitar saltos de estados
            switch ($tipo) {
                case 'entrada':
                    if ($registro->hora_entrada) return back()->with('error', 'Ya registraste tu entrada hoy.');
                    $registro->hora_entrada = $ahora;
                    $registro->ip_entrada = $request->ip();
                    $registro->dispositivo_entrada = $request->header('User-Agent');
                    break;
                
                case 'inicio_almuerzo':
                    if (!$registro->hora_entrada) return back()->with('error', 'Debes marcar entrada primero.');
                    if ($registro->inicio_almuerzo) return back()->with('error', 'Ya marcaste inicio de almuerzo.');
                    if ($registro->hora_salida) return back()->with('error', 'No puedes marcar almuerzo después de salir.');
                    $registro->inicio_almuerzo = $ahora;
                    break;

                case 'fin_almuerzo':
                    if (!$registro->inicio_almuerzo) return back()->with('error', 'No has marcado inicio de almuerzo.');
                    if ($registro->fin_almuerzo) return back()->with('error', 'Ya marcaste fin de almuerzo.');
                    $registro->fin_almuerzo = $ahora;
                    break;

                case 'salida':
                    if (!$registro->hora_entrada) return back()->with('error', 'Debes marcar entrada primero.');
                    if ($registro->hora_salida) return back()->with('error', 'Ya registraste tu salida hoy.');
                    // Si está en almuerzo y no ha marcado fin, forzar fin de almuerzo? 
                    // No, mejor pedir que marque fin de almuerzo primero para auditoría exacta.
                    if ($registro->inicio_almuerzo && !$registro->fin_almuerzo) {
                        return back()->with('error', 'Debes marcar el fin de tu almuerzo antes de registrar la salida.');
                    }
                    $registro->hora_salida = $ahora;
                    $registro->ip_salida = $request->ip();
                    $registro->dispositivo_salida = $request->header('User-Agent');
                    break;
            }

            $registro->save();
            $registro->calcularMetricas();

            return back()->with('success', 'Marca registrada correctamente: ' . strtoupper($tipo));
        });
    }

    /**
     * Guarda la justificación de un turno olvidado
     */
    public function justificar(Request $request, Registro $registro)
    {
        $request->validate([
            'justificacion' => 'required|min:10|max:500',
            'hora_salida_estimada' => 'required'
        ]);

        $user = Auth::user();
        $empleado = Empleado::where('user_id', $user->id)->firstOrFail();

        if ($registro->empleado_id !== $empleado->id) {
            abort(403, 'No autorizado para justificar este registro.');
        }

        return DB::transaction(function () use ($request, $registro) {
            $horaSalida = Carbon::parse($registro->fecha->format('Y-m-d') . ' ' . $request->hora_salida_estimada);

            $registro->update([
                'justificacion_empleado' => $request->justificacion,
                'hora_salida_ajustada' => $horaSalida,
                'hora_salida' => $horaSalida,
                'observaciones' => ($registro->observaciones ? $registro->observaciones . "\n" : "") . "Justificación enviada: " . $request->justificacion
            ]);

            $registro->calcularMetricas();

            return back()->with('success', 'Justificación enviada correctamente. El registro ha sido actualizado.');
        });
    }

    /**
     * Historial personal del empleado
     */
    public function historial()
    {
        $user = Auth::user();
        $empleado = Empleado::where('user_id', $user->id)->firstOrFail();
        
        $registros = Registro::where('empleado_id', $empleado->id)
            ->orderBy('fecha', 'desc')
            ->paginate(15);

        return view('modules.asistencia.historial', compact('registros', 'empleado'));
    }
}
