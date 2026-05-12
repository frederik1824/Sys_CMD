<?php

namespace App\Models\Asistencia;

use Illuminate\Database\Eloquent\Model;

class Feriado extends Model
{
    protected $table = 'asistencia_feriados';
    protected $fillable = ['nombre', 'fecha', 'recurrente'];

    protected $casts = [
        'fecha' => 'date',
        'recurrente' => 'boolean'
    ];
}
