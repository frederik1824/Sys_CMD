<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallCenterEstado extends Model
{
    protected $fillable = ['nombre', 'color', 'icono', 'orden', 'finalizador'];

    public function registros()
    {
        return $this->hasMany(CallCenterRegistro::class, 'estado_id');
    }
}
