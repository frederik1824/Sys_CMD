<?php

namespace App\Http\Controllers\Modules\CallCenter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CallCenterRegistro;
use App\Models\CallCenterEstado;
use App\Models\Afiliado;
use Illuminate\Support\Str;

class CallCenterTicketController extends Controller
{
    /**
     * Renderiza el formulario simplificado para los representantes de autorizaciones médicas.
     */
    public function create(Request $request)
    {
        // Buscar la carga virtual global para Enlaces de Autorizaciones
        $cargaVirtual = \App\Models\CallCenterCarga::where('nombre', 'Enlace Call Center - Autorizaciones')->first();

        $misTickets = collect();
        if ($cargaVirtual) {
            $query = CallCenterRegistro::with('estado', 'operador')
                ->where('carga_id', $cargaVirtual->id)
                ->where('created_by_id', auth()->id()); // Filtrado por autoría individual

            // Buscador por Nombre o Póliza
            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function($q) use ($s) {
                    $q->where('nombre', 'LIKE', "%$s%")
                      ->orWhere('poliza', 'LIKE', "%$s%");
                });
            }

            $misTickets = $query->orderBy('created_at', 'desc')
                ->take(100)
                ->get();
        }

        return view('modules.autorizaciones.ticket', compact('misTickets'));
    }

    /**
     * Almacena la solicitud en la tabla de registros del Call Center
     * con prioridad alta y sin asignación específica.
     */
    public function store(Request $request)
    {
        $request->validate([
            'poliza' => 'required|string|max:50',
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:25',
            'notas' => 'required|string|max:500',
        ]);

        // 🚨 CONTROL DE DUPLICIDAD
        $estadoCompletado = CallCenterEstado::where('nombre', 'Completado')->first();
        $idCompletado = $estadoCompletado ? $estadoCompletado->id : 0;

        $duplicado = CallCenterRegistro::where('poliza', $request->poliza)
            ->where('estado_id', '!=', $idCompletado)
            ->first();

        if ($duplicado) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Ya existe una solicitud activa para la póliza #{$request->poliza}. Estado actual: " . $duplicado->estado->nombre);
        }

        // Carga Virtual Global
        $cargaVirtual = \App\Models\CallCenterCarga::firstOrCreate(
            ['nombre' => 'Enlace Call Center - Autorizaciones'],
            ['user_id' => 1]
        );
        
        $cargaVirtual->increment('total_registros');
        $cargaVirtual->increment('registros_nuevos');

        // Estado "Pendiente de gestión"
        $estadoPendiente = CallCenterEstado::where('nombre', 'Pendiente de gestión')->first();
        $estadoId = $estadoPendiente ? $estadoPendiente->id : 1;

        // Buscar si existe en la base de datos maestra
        $afiliado = Afiliado::where('poliza', $request->poliza)->first();

        // Crear el registro de derivación
        CallCenterRegistro::create([
            'uuid' => (string) Str::uuid(),
            'poliza' => $request->poliza,
            'cedula' => $afiliado ? $afiliado->cedula : null, // Captura automática si existe
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'afiliado_id' => $afiliado ? $afiliado->uuid : null,
            'estado_id' => $estadoId,
            'prioridad' => 100, 
            'observaciones' => "[NOTAS MÉDICAS]: " . $request->notas,
            'operador_id' => null, 
            'carga_id' => $cargaVirtual->id,
            'created_by_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Solicitud enviada correctamente al Call Center.');
    }
}
