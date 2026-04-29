<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoSolicitudAfiliacion extends Model
{
    protected $table = 'documentos_solicitud_afiliacion';

    protected $fillable = [
        'solicitud_id', 'documento_requerido_id', 'archivo_path', 'nombre_original',
        'mime_type', 'validacion_estado', 'comentario_validacion', 'uploaded_by',
        'validated_by', 'validated_at'
    ];

    protected $casts = [
        'validated_at' => 'datetime'
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudAfiliacion::class, 'solicitud_id');
    }

    public function requerimiento()
    {
        return $this->belongsTo(DocumentoRequeridoSolicitud::class, 'documento_requerido_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
