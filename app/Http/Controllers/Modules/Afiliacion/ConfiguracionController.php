<?php

namespace App\Http\Controllers\Modules\Afiliacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TipoSolicitudAfiliacion;
use App\Models\DocumentoRequeridoSolicitud;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $tipos = TipoSolicitudAfiliacion::with('documentosRequeridos')->get();
        return view('modules.afiliacion.config', compact('tipos'));
    }

    public function storeTipo(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'sla_horas' => 'required|integer|min:1',
        ]);

        TipoSolicitudAfiliacion::create([
            'nombre' => $request->nombre,
            'sla_horas' => $request->sla_horas,
            'descripcion' => $request->descripcion,
            'activo' => true
        ]);

        return back()->with('success', 'Tipo de solicitud creado correctamente.');
    }

    public function updateTipo(Request $request, TipoSolicitudAfiliacion $tipo)
    {
        $tipo->update($request->only(['nombre', 'sla_horas', 'descripcion', 'activo']));
        return back()->with('success', 'Tipo de solicitud actualizado.');
    }

    public function storeDocumento(Request $request)
    {
        $request->validate([
            'tipo_solicitud_id' => 'required|exists:tipos_solicitud_afiliacion,id',
            'nombre_documento' => 'required|string',
        ]);

        DocumentoRequeridoSolicitud::create([
            'tipo_solicitud_id' => $request->tipo_solicitud_id,
            'nombre_documento' => $request->nombre_documento,
            'obligatorio' => $request->has('obligatorio'),
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Documento requerido añadido.');
    }

    public function deleteDocumento(DocumentoRequeridoSolicitud $documento)
    {
        $documento->delete();
        return back()->with('success', 'Documento eliminado.');
    }
}
