<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class PypPrograma extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'nombre', 'slug', 'descripcion', 'icon', 'color', 'is_active'
    ];

    public function expedientes()
    {
        return $this->belongsToMany(PypExpediente::class, 'pyp_expediente_programa', 'programa_id', 'expediente_id')
                    ->withPivot('fecha_inscripcion')
                    ->withTimestamps();
    }
}
