<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PssMedico extends Model
{
    use SoftDeletes;
    protected $table = 'pss_medicos';

    protected $fillable = [
        'nombre',
        'telefono_1',
        'telefono_2',
        'ciudad_id',
        'especialidad_id',
        'clinica_id',
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

    public function especialidad()
    {
        return $this->belongsTo(PssEspecialidad::class, 'especialidad_id');
    }

    public function clinica()
    {
        return $this->belongsTo(PssClinica::class, 'clinica_id');
    }
}
