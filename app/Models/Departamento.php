<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'activo'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudAfiliacion::class);
    }
}
