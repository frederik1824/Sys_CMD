<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PssCiudad extends Model
{
    protected $table = 'pss_ciudades';
    protected $fillable = ['nombre', 'activo'];

    public function medicos()
    {
        return $this->hasMany(PssMedico::class, 'ciudad_id');
    }

    public function centros()
    {
        return $this->hasMany(PssCentro::class, 'ciudad_id');
    }
}
