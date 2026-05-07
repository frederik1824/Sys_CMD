<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class Traspaso extends Model
{
    use Auditable;

    protected $fillable = [
        'nombre_afiliado',
        'cedula_afiliado',
        'nombre_solicitante',
        'cedula_solicitante',
        'fecha_solicitud',
        'fecha_envio_epbd',
        'numero_solicitud_epbd',
        'pendiente_carga_documento',
        'pendiente_aprobar_consentimiento',
        'agente_id',
        'agente_legacy',
        'estado_id',
        'estado_legacy',
        'motivos_estado',
        'fecha_efectivo',
        'periodo_efectivo',
        'cantidad_dependientes',
        'es_emitido',
    ];

    public function agenteRel()
    {
        return $this->belongsTo(AgenteTraspaso::class, 'agente_id');
    }

    public function estadoRel()
    {
        return $this->belongsTo(EstadoTraspaso::class, 'estado_id');
    }

    public function motivoRechazoRel()
    {
        return $this->belongsTo(MotivoRechazoTraspaso::class, 'motivo_rechazo_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_solicitud' => 'date',
            'fecha_envio_epbd' => 'date',
            'fecha_efectivo' => 'date',
            'pendiente_carga_documento' => 'boolean',
            'pendiente_aprobar_consentimiento' => 'boolean',
            'es_emitido' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($traspaso) {
            // 1. Homologación Automática de Agentes (Más flexible)
            if ($traspaso->agente_legacy && !$traspaso->agente_id) {
                $nombreNormalizado = self::normalizeName($traspaso->agente_legacy);
                
                // Buscamos coincidencia exacta primero
                $agentes = \App\Models\AgenteTraspaso::all();
                $agente = $agentes->first(function($a) use ($nombreNormalizado) {
                    return self::normalizeName($a->nombre) === $nombreNormalizado;
                });

                // Si no hay exacta, buscamos por similitud (si un nombre contiene al otro)
                if (!$agente) {
                    $agente = $agentes->first(function($a) use ($nombreNormalizado) {
                        $agenteBase = self::normalizeName($a->nombre);
                        return str_contains($agenteBase, $nombreNormalizado) || str_contains($nombreNormalizado, $agenteBase);
                    });
                }

                if ($agente) {
                    $traspaso->agente_id = $agente->id;
                }
            }

            // 2. Homologación Automática de Estados
            if ($traspaso->isDirty('estado_legacy') && $traspaso->estado_legacy) {
                $textoEstado = strtoupper($traspaso->estado_legacy);
                $slug = 'proceso'; // default

                if (str_contains($textoEstado, 'RE')) $slug = 'rechazado';
                elseif (str_contains($textoEstado, 'EF') || str_contains($textoEstado, 'AP')) $slug = 'efectivo';
                elseif (str_contains($textoEstado, 'EM')) $slug = 'emitido';

                $estado = \App\Models\EstadoTraspaso::where('slug', $slug)->first();
                if ($estado) {
                    $traspaso->estado_id = $estado->id;
                }
            }

            // 3. Regla de Negocio: Prioridad de Fecha Efectiva y Auto-periodo
            if ($traspaso->fecha_efectivo) {
                // Auto-calcular periodo (YYYY-MM)
                $traspaso->periodo_efectivo = $traspaso->fecha_efectivo->format('Y-m');

                if (!$traspaso->es_emitido) {
                    $estadoEfectivo = \App\Models\EstadoTraspaso::where('slug', 'efectivo')->first();
                    if ($estadoEfectivo) {
                        $traspaso->estado_id = $estadoEfectivo->id;
                        $traspaso->estado_legacy = 'EFECTIVO';
                    }
                }
            } else {
                // Si no hay fecha efectiva, NO puede haber periodo efectivo
                $traspaso->periodo_efectivo = null;
            }
        });
    }

    /**
     * Normaliza un nombre para comparación (Agentes)
     */
    public static function normalizeName($name)
    {
        if (!$name) return '';
        $name = mb_strtoupper(trim($name), 'UTF-8');
        return preg_replace('/\s+/', ' ', $name);
    }
}
