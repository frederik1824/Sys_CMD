<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PssClinica extends Model
{
    protected $table = 'pss_clinicas';
    protected $fillable = ['nombre', 'ciudad_id', 'activo'];

    public function ciudad()
    {
        return $this->belongsTo(PssCiudad::class, 'ciudad_id');
    }
}
