<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoRequeridoSolicitud extends Model
{
    protected $table = 'documentos_requeridos_solicitud';

    protected $fillable = [
        'tipo_solicitud_id', 'nombre_documento', 'obligatorio', 'descripcion', 'activo'
    ];

    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitudAfiliacion::class, 'tipo_solicitud_id');
    }
}
