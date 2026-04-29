<?php

namespace App\Http\Controllers;

use App\Models\NotaAfiliado;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'afiliado_id' => 'required|exists:afiliados,id',
            'contenido' => 'required|string'
        ]);

        NotaAfiliado::create([
            'afiliado_id' => $request->afiliado_id,
            'user_id' => auth()->id(),
            'contenido' => $request->contenido
        ]);

        return back()->with('success', 'Nota agregada correctamente.');
    }
}
