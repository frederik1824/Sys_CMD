<?php

namespace App\Models\Asistencia;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'asistencia_turnos';
    protected $fillable = [
        'nombre', 
        'entrada_esperada', 
        'salida_esperada', 
        'tolerancia_minutos', 
        'minutos_almuerzo', 
        'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo', 
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'lunes' => 'boolean',
        'martes' => 'boolean',
        'miercoles' => 'boolean',
        'jueves' => 'boolean',
        'viernes' => 'boolean',
        'sabado' => 'boolean',
        'domingo' => 'boolean'
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'turno_id');
    }
}
