<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensajero extends Model
{
    protected $fillable = ['nombre', 'cedula', 'telefono', 'vehiculo_placa', 'vehiculo_tipo', 'activo', 'color'];

    public function despachos()
    {
        return $this->hasMany(Despacho::class);
    }
}
