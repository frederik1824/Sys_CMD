<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallCenterBandejaSalida extends Model
{
    protected $table = 'call_center_bandeja_salida';

    protected $fillable = ['registro_id', 'fecha_envio', 'enviado_por', 'procesado', 'fecha_procesado'];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_procesado' => 'datetime',
        'procesado' => 'boolean',
    ];

    public function registro()
    {
        return $this->belongsTo(CallCenterRegistro::class, 'registro_id');
    }

    public function enviadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'enviado_por');
    }
}
