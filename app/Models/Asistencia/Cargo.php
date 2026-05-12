<?php

namespace App\Models\Asistencia;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'asistencia_cargos';
    protected $fillable = ['nombre', 'departamento_id', 'activo'];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'cargo_id');
    }
}
