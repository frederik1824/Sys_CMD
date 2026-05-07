<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallCenterGestion extends Model
{
    protected $table = 'call_center_gestiones';

    protected $fillable = [
        'registro_id', 'operador_id', 'estado_anterior_id', 'estado_nuevo_id',
        'tipo_contacto', 'resultado_contacto', 'telefono_contactado',
        'persona_contactada', 'observacion', 'fecha_proximo_contacto'
    ];

    protected $casts = [
        'fecha_proximo_contacto' => 'date',
    ];

    public function registro()
    {
        return $this->belongsTo(CallCenterRegistro::class, 'registro_id');
    }

    public function operador()
    {
        return $this->belongsTo(\App\Models\User::class, 'operador_id');
    }

    public function estadoAnterior()
    {
        return $this->belongsTo(CallCenterEstado::class, 'estado_anterior_id');
    }

    public function estadoNuevo()
    {
        return $this->belongsTo(CallCenterEstado::class, 'estado_nuevo_id');
    }
}
