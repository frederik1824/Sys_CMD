<?php

namespace App\Http\Controllers\Modules\Pyp;

use App\Http\Controllers\Controller;
use App\Models\Afiliado;
use App\Models\PypEvaluacion;
use App\Models\PypExpediente;
use App\Services\Pyp\ScoringEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class EvaluacionController extends Controller
{
    protected $scoringEngine;

    public function __construct(ScoringEngine $scoringEngine)
    {
        $this->scoringEngine = $scoringEngine;
    }

    /**
     * Show the form for a new medical evaluation.
     */
    public function create($afiliado_uuid)
    {
        $afiliado = Afiliado::where('uuid', $afiliado_uuid)->firstOrFail();
        return view('modules.pyp.evaluaciones.create', compact('afiliado'));
    }

    /**
     * Store the evaluation and recalculate risk.
     */
    public function store(Request $request, $afiliado_uuid)
    {
        $afiliado = Afiliado::where('uuid', $afiliado_uuid)->firstOrFail();
        $exp = $afiliado->pypExpediente;

        $request->validate([
            'presion_sistolica' => 'required|numeric',
            'presion_diastolica' => 'required|numeric',
            'glucosa' => 'required|numeric',
            'peso' => 'required|numeric',
            'talla' => 'required|numeric',
            'diagnostico' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Crear Evaluación
            $evaluacion = PypEvaluacion::create([
                'uuid' => (string) Str::uuid(),
                'expediente_id' => $exp->id,
                'medico_id' => auth()->id(),
                'datos_evaluacion_json' => $request->only([
                    'presion_sistolica', 
                    'presion_diastolica', 
                    'glucosa', 
                    'peso', 
                    'talla',
                    'fumador',
                    'sedentarismo'
                ]),
                'diagnostico' => $request->diagnostico,
                'plan_accion' => $request->plan_accion,
            ]);

            // 2. Recalcular Riesgo usando el Scoring Engine
            $newRisk = $this->scoringEngine->calculate($exp);

            // 3. Actualizar Expediente
            $exp->update([
                'riesgo_score' => $newRisk['score'],
                'riesgo_nivel' => $newRisk['nivel'],
                'estado_clinico' => $request->estado_clinico ?? 'Evaluado',
                'enfermedades_json' => $request->comorbilidades ?? [],
                'ultimo_evaluacion_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('pyp.afiliados.show', $afiliado->uuid)
                             ->with('success', 'Evaluación registrada y riesgo actualizado a: ' . $newRisk['nivel']);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar evaluación: ' . $e->getMessage());
        }
    }
}
