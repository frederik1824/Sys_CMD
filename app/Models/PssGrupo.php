<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PssGrupo extends Model
{
    protected $table = 'pss_grupos';
    protected $fillable = ['nombre', 'activo'];

    public function centros()
    {
        return $this->hasMany(PssCentro::class, 'grupo_id');
    }
}
