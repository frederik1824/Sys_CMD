<?php

namespace App\Models\Asistencia;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'asistencia_permisos';
    protected $fillable = [
        'empleado_id', 'tipo_permiso_id', 'fecha_desde', 'fecha_hasta', 
        'hora_inicio', 'hora_fin', 'motivo', 'evidencia_path', 
        'estado', 'aprobado_por', 'comentario_aprobador'
    ];

    protected $casts = [
        'fecha_desde' => 'date',
        'fecha_hasta' => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoPermiso::class, 'tipo_permiso_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}
