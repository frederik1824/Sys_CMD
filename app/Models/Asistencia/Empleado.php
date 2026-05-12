<?php

namespace App\Models\Asistencia;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'asistencia_empleados';
    protected $fillable = [
        'user_id', 
        'codigo_empleado', 
        'cedula', 
        'nombre_completo', 
        'cargo_id', 
        'turno_id', 
        'supervisor_id', 
        'fecha_ingreso', 
        'foto_path', 
        'estado'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function registros()
    {
        return $this->hasMany(Registro::class, 'empleado_id');
    }

    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'empleado_id');
    }
}
