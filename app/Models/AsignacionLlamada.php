<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionLlamada extends Model
{
    use HasFactory;

    protected $fillable = [
        'afiliado_id',
        'usuario_id',
        'asignador_id',
        'fecha_asignacion',
        'activa',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'activa' => 'boolean',
    ];

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function asignador()
    {
        return $this->belongsTo(User::class, 'asignador_id');
    }
}
