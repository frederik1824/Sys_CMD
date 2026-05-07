<?php

namespace App\Traits;

trait HasDataQuality
{
    /**
     * Calcula el porcentaje de completitud basado en pesos definidos en el modelo.
     * 
     * El modelo debe definir un método 'qualityFields()' que retorne [campo => peso]
     */
    public function getDataQualityAttribute()
    {
        $fields = method_exists($this, 'qualityFields') ? $this->qualityFields() : [];
        
        $score = 0;
        $missing = [];

        foreach ($fields as $field => $weight) {
            if (!empty($this->{$field})) {
                $score += $weight;
            } else {
                $missing[] = $field;
            }
        }

        $score = round($score);
        
        $level = 'critical';
        $color = 'rose';
        
        if ($score >= 90) { $level = 'perfect'; $color = 'emerald'; }
        elseif ($score >= 70) { $level = 'good'; $color = 'blue'; }
        elseif ($score >= 40) { $level = 'warning'; $color = 'amber'; }

        return (object) [
            'score' => $score,
            'level' => $level,
            'color' => $color,
            'missing' => $missing,
            'is_ready' => $score >= ($this->qualityThreshold ?? 75)
        ];
    }
}
