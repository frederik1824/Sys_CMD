<?php

namespace App\Models\Asistencia;

use Illuminate\Database\Eloquent\Model;

class TipoPermiso extends Model
{
    protected $table = 'asistencia_tipos_permiso';
    protected $fillable = ['nombre', 'slug', 'requiere_evidencia', 'es_remunerado'];

    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'tipo_permiso_id');
    }
}
