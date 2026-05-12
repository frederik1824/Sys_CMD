<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PssImportacionDetalle extends Model
{
    protected $table = 'pss_importacion_detalles';

    protected $fillable = [
        'importacion_id',
        'fila',
        'estado',
        'datos_originales',
        'error_mensaje'
    ];

    protected $casts = [
        'datos_originales' => 'json'
    ];

    public function importacion()
    {
        return $this->belongsTo(PssImportacion::class, 'importacion_id');
    }
}
