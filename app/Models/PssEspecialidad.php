<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PssEspecialidad extends Model
{
    protected $table = 'pss_especialidades';
    protected $fillable = ['nombre', 'activo'];

    public function medicos()
    {
        return $this->hasMany(PssMedico::class, 'especialidad_id');
    }
}
