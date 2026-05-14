<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitudAfiliacion extends Model
{
    use SoftDeletes;

    protected $table = 'solicitudes_afiliacion';

    protected $fillable = [
        'codigo_solicitud', 'tipo_solicitud_id', 'departamento_id', 'solicitante_user_id', 'asignado_user_id',
        'afiliado_id', 'cedula', 'nombre_completo', 'telefono', 'correo', 'empresa',
        'rnc_empresa', 'numero_resolucion', 'tipo_pension', 'institucion_pension',
        'estado', 'prioridad', 'observacion_solicitante', 'observacion_interna',
        'motivo_rechazo', 'motivo_devolucion', 'sla_fecha_limite', 'pausado_at', 'sla_acumulado_segundos', 'fecha_asignacion', 'fecha_cierre',
        'satisfaccion_nivel', 'satisfaccion_comentario', 'es_primera_resolucion', 'fecha_primera_asignacion'
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    protected $casts = [
        'sla_fecha_limite' => 'datetime',
        'pausado_at' => 'datetime',
        'fecha_asignacion' => 'datetime',
        'fecha_cierre' => 'datetime',
        'pago_confirmado_at' => 'datetime',
    ];

    /**
     * Calcula el tiempo transcurrido desde la creación hasta la confirmación del pago.
     */
    public function getLeadTimePagoAttribute()
    {
        if (!$this->pago_confirmado_at) return null;
        return $this->created_at->diffForHumans($this->pago_confirmado_at, true);
    }

    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitudAfiliacion::class, 'tipo_solicitud_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_user_id');
    }

    public function asignado()
    {
        return $this->belongsTo(User::class, 'asignado_user_id');
    }

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id', 'uuid');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoSolicitudAfiliacion::class, 'solicitud_id');
    }

    public function historial()
    {
        return $this->hasMany(HistorialSolicitudAfiliacion::class, 'solicitud_id')->orderBy('created_at', 'desc');
    }

    public function getStatusColorAttribute()
    {
        return match($this->estado) {
            'Borrador'   => 'bg-slate-100 text-slate-700',
            'Pendiente'  => 'bg-amber-100 text-amber-700',
            'Asignada'   => 'bg-blue-100 text-blue-700',
            'En revisión'=> 'bg-indigo-100 text-indigo-700',
            'Devuelta'   => 'bg-orange-100 text-orange-700',
            'Corregida'  => 'bg-cyan-100 text-cyan-700',
            'Aprobada'   => 'bg-teal-50 text-teal-700 border-teal-200', // Color más suave para indicar que no ha terminado
            'Completada' => 'bg-emerald-100 text-emerald-700', // Este ahora es el verde de éxito final
            'Rechazada'  => 'bg-rose-100 text-rose-700',
            'Escalada'   => 'bg-purple-100 text-purple-700',
            'Cerrada'    => 'bg-slate-800 text-white',
            'Cancelada'  => 'bg-slate-400 text-white',
            default      => 'bg-slate-100 text-slate-700',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->estado) {
            'Aprobada' => 'Pendiente de Cierre',
            'En revisión' => 'En Evaluación',
            default => $this->estado,
        };
    }

    public function getPriorityColorAttribute()
    {
        $isFinished = in_array($this->estado, ['Completada', 'Rechazada', 'Cerrada', 'Cancelada']);

        return match($this->prioridad) {
            'Normal'  => 'bg-slate-50 text-slate-500 border-slate-200',
            'Alta'    => 'bg-gradient-to-r from-amber-400 to-orange-500 text-white border-orange-600 shadow-lg shadow-amber-200/50 font-black',
            'Urgente' => 'bg-gradient-to-r from-rose-500 to-red-700 text-white border-rose-800 shadow-xl shadow-rose-300/60 font-black ' . ($isFinished ? '' : 'animate-pulse'),
            default   => 'bg-slate-50 text-slate-500 border-slate-200',
        };
    }

    public function getPriorityIconAttribute()
    {
        return match($this->prioridad) {
            'Normal'  => 'ph ph-clock',
            'Alta'    => 'ph-fill ph-lightning',
            'Urgente' => 'ph-fill ph-warning-octagon',
            default   => 'ph ph-clock',
        };
    }

    public function getPriorityColorSimpleAttribute()
    {
        return match($this->prioridad) {
            'Normal'  => 'bg-slate-300',
            'Alta'    => 'bg-amber-400',
            'Urgente' => 'bg-rose-500',
            default   => 'bg-slate-300',
        };
    }

    public function setCedulaAttribute($value)
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        if (strlen($clean) === 11) {
            $formatted = substr($clean, 0, 3) . '-' . substr($clean, 3, 7) . '-' . substr($clean, 10, 1);
            $this->attributes['cedula'] = $formatted;
        } else {
            $this->attributes['cedula'] = $value;
        }
    }

    public function hasConfirmedPayment()
    {
        $cleanCedula = preg_replace('/[^0-9]/', '', $this->cedula);
        
        return \App\Models\Modules\Dispersion\DispersionTitular::whereRaw("REPLACE(cedula, '-', '') = ?", [$cleanCedula])->exists() ||
               \App\Models\Modules\Dispersion\DispersionDependiente::whereRaw("REPLACE(cedula_dependiente, '-', '') = ?", [$cleanCedula])->exists();
    }

    public function getSlaPercentageAttribute()
    {
        if (!$this->sla_fecha_limite) return 0;
        if (in_array($this->estado, ['Completada', 'Rechazada', 'Cancelada', 'Cerrada'])) return 100;

        $totalSeconds = $this->created_at->diffInSeconds($this->sla_fecha_limite);
        if ($totalSeconds <= 0) return 100;
        
        // Si está pausada, descontamos el tiempo de pausa o calculamos hasta el momento de pausa
        $referenceTime = $this->pausado_at ?: now();
        $elapsedSeconds = $this->created_at->diffInSeconds($referenceTime);
        $elapsedSeconds -= $this->sla_acumulado_segundos; // Restamos lo que ya estuvo pausado antes
        
        return min(max(round(($elapsedSeconds / $totalSeconds) * 100), 0), 100);
    }
}
