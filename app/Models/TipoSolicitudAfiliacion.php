<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoSolicitudAfiliacion extends Model
{
    protected $table = 'tipos_solicitud_afiliacion';

    protected $fillable = [
        'nombre', 'descripcion', 'activo', 'sla_horas'
    ];

    public function documentosRequeridos()
    {
        return $this->hasMany(DocumentoRequeridoSolicitud::class, 'tipo_solicitud_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudAfiliacion::class, 'tipo_solicitud_id');
    }
}
