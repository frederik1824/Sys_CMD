<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialSolicitudAfiliacion extends Model
{
    protected $table = 'historial_solicitud_afiliacion';

    protected $fillable = [
        'solicitud_id', 'user_id', 'accion', 'estado_anterior', 'estado_nuevo', 'comentario', 'detalles'
    ];

    protected $casts = [
        'detalles' => 'array'
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudAfiliacion::class, 'solicitud_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
