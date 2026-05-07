<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallCenterDespacho extends Model
{
    protected $fillable = [
        'registro_id', 'direccion_entrega', 'empresa_destino', 'persona_recibe',
        'telefono_contacto', 'mensajero_id', 'fecha_despacho', 'estado_despacho',
        'observaciones', 'formulario_recibido'
    ];

    protected $casts = [
        'fecha_despacho' => 'datetime',
        'formulario_recibido' => 'boolean',
    ];

    public function registro()
    {
        return $this->belongsTo(CallCenterRegistro::class, 'registro_id');
    }

    public function mensajero()
    {
        return $this->belongsTo(Mensajero::class, 'mensajero_id');
    }
}
