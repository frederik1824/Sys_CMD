<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PssCentro extends Model
{
    use SoftDeletes;
    protected $table = 'pss_centros';

    protected $fillable = [
        'nombre',
        'telefono_1',
        'telefono_2',
        'ciudad_id',
        'grupo_id',
        'estado',
        'origen_importacion',
        'fecha_importacion',
        'observaciones'
    ];

    protected $casts = [
        'fecha_importacion' => 'datetime',
    ];

    public function ciudad()
    {
        return $this->belongsTo(PssCiudad::class, 'ciudad_id');
    }

    public function grupo()
    {
        return $this->belongsTo(PssGrupo::class, 'grupo_id');
    }
}
