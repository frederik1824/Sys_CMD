<?php

namespace App\Models\Asistencia;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'asistencia_departamentos';
    protected $fillable = ['nombre', 'codigo', 'activo'];

    public function cargos()
    {
        return $this->hasMany(Cargo::class, 'departamento_id');
    }
}
