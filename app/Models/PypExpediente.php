<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;

class PypExpediente extends Model
{
    use SoftDeletes, HasUuids, Auditable;

    protected $fillable = [
        'uuid', 'afiliado_id', 'riesgo_score', 'riesgo_nivel', 
        'estado_clinico', 'enfermedades_json', 'ultimo_seguimiento_at', 
        'proxima_evaluacion_at', 'asignado_a'
    ];

    protected $casts = [
        'enfermedades_json' => 'array',
        'ultimo_seguimiento_at' => 'datetime',
        'proxima_evaluacion_at' => 'datetime',
        'riesgo_score' => 'decimal:2'
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    // RELACIONES
    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class);
    }

    public function asignado()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function evaluaciones()
    {
        return $this->hasMany(PypEvaluacion::class, 'expediente_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(PypSeguimiento::class, 'expediente_id');
    }

    public function programas()
    {
        return $this->belongsToMany(PypPrograma::class, 'pyp_expediente_programa', 'expediente_id', 'programa_id')
                    ->withPivot('fecha_inscripcion')
                    ->withTimestamps();
    }

    // ACCESSORS PARA UI
    public function getRiesgoColorAttribute()
    {
        return match($this->riesgo_nivel) {
            'Alto' => 'bg-rose-100 text-rose-700 border-rose-200',
            'Moderado' => 'bg-amber-100 text-amber-700 border-amber-200',
            default => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        };
    }

    public function getEstadoClinicoColorAttribute()
    {
        return match($this->estado_clinico) {
            'Descompensado' => 'text-rose-600 font-black',
            'Parcialmente Controlado' => 'text-amber-600 font-bold',
            default => 'text-emerald-600',
        };
    }
}
