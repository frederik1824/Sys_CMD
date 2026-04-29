<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'zona', 'es_frecuente'];

    public function despachos()
    {
        return $this->hasMany(Despacho::class);
    }
}
