<?php

namespace App\Http\Controllers\Modules\Pyp;

use App\Http\Controllers\Controller;
use App\Models\Afiliado;
use App\Models\PypSeguimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeguimientoController extends Controller
{
    /**
     * Store a new follow-up interaction.
     */
    public function store(Request $request, $afiliado_uuid)
    {
        $afiliado = Afiliado::where('uuid', $afiliado_uuid)->firstOrFail();
        $exp = $afiliado->pypExpediente;

        $request->validate([
            'tipo_contacto' => 'required|string',
            'resultado' => 'required|string',
            'comentarios' => 'nullable|string',
            'proximo_contacto_at' => 'nullable|date',
        ]);

        $seguimiento = PypSeguimiento::create([
            'uuid' => (string) Str::uuid(),
            'expediente_id' => $exp->id,
            'user_id' => auth()->id(),
            'tipo_contacto' => $request->tipo_contacto,
            'resultado' => $request->resultado,
            'comentarios' => $request->comentarios,
            'proximo_contacto_at' => $request->proximo_contacto_at,
        ]);

        // Actualizar la fecha de último seguimiento en el expediente
        $exp->update([
            'ultimo_seguimiento_at' => now()
        ]);

        return redirect()->route('pyp.afiliados.show', $afiliado->uuid)
                         ->with('success', 'Seguimiento registrado exitosamente.');
    }
}
