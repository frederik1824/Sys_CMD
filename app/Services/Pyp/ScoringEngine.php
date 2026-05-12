<?php

namespace App\Services\Pyp;

use App\Models\PypExpediente;
use App\Models\PypEvaluacion;

class ScoringEngine
{
    /**
     * Calcula y actualiza el riesgo de un expediente basado en su última evaluación
     */
    public function calculate(PypExpediente $expediente): array
    {
        $score = 0;
        $evaluacion = $expediente->evaluaciones()->latest()->first();
        $afiliado = $expediente->afiliado;

        if (!$evaluacion) {
            return ['score' => 0, 'nivel' => 'Bajo'];
        }

        // 1. FACTORES DEMOGRÁFICOS (Edad)
        $edad = \Carbon\Carbon::parse($afiliado->fecha_nacimiento ?? now())->age;
        if ($edad > 80) $score += 4;
        elseif ($edad > 65) $score += 2;

        // 2. PATOLOGÍAS CRÓNICAS (Basado en diagnósticos)
        $diagnosticos = $evaluacion->diagnosticos ?? [];
        foreach ($diagnosticos as $diag) {
            $score += match(strtolower($diag)) {
                'diabetes' => 3,
                'hipertension' => 2,
                'cardiopatia' => 4,
                'insuficiencia renal' => 5,
                'cancer' => 5,
                'asma', 'epoc' => 2,
                default => 1
            };
        }

        // 3. ESTADO CLÍNICO
        if ($expediente->estado_clinico === 'Descompensado') $score += 3;
        if ($expediente->estado_clinico === 'Parcialmente Controlado') $score += 1;

        // 4. USO DE SERVICIOS (Signos de alarma)
        $hospitalizaciones = $evaluacion->signos_vitales['hospitalizaciones_año'] ?? 0;
        $emergencias = $evaluacion->signos_vitales['emergencias_año'] ?? 0;

        if ($hospitalizaciones > 0) $score += ($hospitalizaciones * 3);
        if ($emergencias > 2) $score += 2;

        // CLASIFICACIÓN FINAL
        $nivel = 'Bajo';
        if ($score >= 8) $nivel = 'Alto';
        elseif ($score >= 4) $nivel = 'Moderado';

        // Persistir en el expediente
        $expediente->update([
            'riesgo_score' => $score,
            'riesgo_nivel' => $nivel
        ]);

        return [
            'score' => $score,
            'nivel' => $nivel
        ];
    }
}
