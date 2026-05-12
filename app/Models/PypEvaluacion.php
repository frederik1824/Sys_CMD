<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;

class PypEvaluacion extends Model
{
    protected $table = 'pyp_evaluaciones';
    use SoftDeletes, HasUuids, Auditable;

    protected $fillable = [
        'uuid', 'expediente_id', 'medico_id', 'datos_evaluacion_json', 
        'diagnostico', 'plan_accion'
    ];

    protected $casts = [
        'datos_evaluacion_json' => 'array',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function expediente()
    {
        return $this->belongsTo(PypExpediente::class);
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }
}
