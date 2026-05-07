<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallCenterDocumento extends Model
{
    protected $fillable = ['registro_id', 'nombre_documento', 'estado', 'path_archivo', 'observacion'];

    public function registro()
    {
        return $this->belongsTo(CallCenterRegistro::class, 'registro_id');
    }
}
