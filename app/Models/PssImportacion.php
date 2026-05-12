<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PssImportacion extends Model
{
    protected $table = 'pss_importaciones';

    protected $fillable = [
        'nombre_archivo',
        'tipo',
        'total_registros',
        'procesados',
        'errores',
        'omitidos',
        'duplicados',
        'user_id',
        'configuracion',
        'resultado_json'
    ];

    protected $casts = [
        'configuracion' => 'json',
        'resultado_json' => 'json'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(PssImportacionDetalle::class, 'importacion_id');
    }
}
