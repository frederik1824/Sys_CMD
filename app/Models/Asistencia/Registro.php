<?php

namespace App\Models\Asistencia;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    protected $table = 'asistencia_registros';
    protected $fillable = [
        'empleado_id', 'fecha', 'hora_entrada', 'hora_salida', 
        'inicio_almuerzo', 'fin_almuerzo',
        'ip_entrada', 'dispositivo_entrada', 'ip_salida', 'dispositivo_salida',
        'minutos_tardanza', 'minutos_salida_temprana', 'minutos_trabajados_neto', 'cumplio_jornada',
        'requiere_justificacion', 'justificacion_empleado', 'hora_salida_ajustada',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_entrada' => 'datetime',
        'hora_salida' => 'datetime',
        'inicio_almuerzo' => 'datetime',
        'fin_almuerzo' => 'datetime',
        'hora_salida_ajustada' => 'datetime',
        'cumplio_jornada' => 'boolean',
        'requiere_justificacion' => 'boolean'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    /**
     * Calcula minutos trabajados hasta el momento (Live)
     */
    public function getMinutosTrabajadosActualesAttribute()
    {
        if (!$this->hora_entrada) return 0;

        $final = $this->hora_salida ?: Carbon::now();
        $totalMinutos = $this->hora_entrada->diffInMinutes($final);

        // Restar almuerzo si ocurrió o está en curso
        if ($this->inicio_almuerzo) {
            $finAlmuerzo = $this->fin_almuerzo ?: Carbon::now();
            $minutosAlmuerzo = $this->inicio_almuerzo->diffInMinutes($finAlmuerzo);
            $totalMinutos -= $minutosAlmuerzo;
        }

        return max(0, $totalMinutos);
    }

    /**
     * Calcula métricas de asistencia comparando con el turno del empleado
     */
    public function calcularMetricas()
    {
        $turno = $this->empleado->turno;
        if (!$turno) return;

        // 1. Calcular Tardanza
        if ($this->hora_entrada) {
            $entradaEsperada = Carbon::parse($this->fecha->format('Y-m-d') . ' ' . $turno->entrada_esperada);
            $entradaConTolerancia = $entradaEsperada->copy()->addMinutes($turno->tolerancia_minutos);
            
            if ($this->hora_entrada->greaterThan($entradaConTolerancia)) {
                $this->minutos_tardanza = $this->hora_entrada->diffInMinutes($entradaEsperada);
            } else {
                $this->minutos_tardanza = 0;
            }
        }

        // 2. Calcular Salida Temprana
        if ($this->hora_salida) {
            $salidaEsperada = Carbon::parse($this->fecha->format('Y-m-d') . ' ' . $turno->salida_esperada);
            if ($this->hora_salida->lessThan($salidaEsperada)) {
                $this->minutos_salida_temprana = $this->hora_salida->diffInMinutes($salidaEsperada);
            } else {
                $this->minutos_salida_temprana = 0;
            }
        }

        // 3. Calcular Horas Trabajadas Netas
        if ($this->hora_entrada && $this->hora_salida) {
            $totalMinutos = $this->hora_entrada->diffInMinutes($this->hora_salida);
            
            // Restar almuerzo si ocurrió
            if ($this->inicio_almuerzo && $this->fin_almuerzo) {
                $minutosAlmuerzo = $this->inicio_almuerzo->diffInMinutes($this->fin_almuerzo);
                $totalMinutos -= $minutosAlmuerzo;
            } else {
                // Si no marcó almuerzo, restar el tiempo de almuerzo por defecto del turno
                $totalMinutos -= $turno->minutos_almuerzo;
            }

            $this->minutos_trabajados_neto = max(0, $totalMinutos);
            
            // 4. Cumplimiento de Jornada
            $jornadaEsperadaMinutos = Carbon::parse($turno->entrada_esperada)->diffInMinutes(Carbon::parse($turno->salida_esperada)) - $turno->minutos_almuerzo;
            $this->cumplio_jornada = $this->minutos_trabajados_neto >= ($jornadaEsperadaMinutos - 10); // 10 min de margen
        }

        $this->save();
    }
}
