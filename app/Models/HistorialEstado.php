<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEstado extends Model
{
    protected $fillable = [
        'afiliado_id', 'estado_anterior_id', 'estado_nuevo_id', 'user_id', 'observacion'
    ];

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class);
    }

    public function estadoAnterior()
    {
        return $this->belongsTo(Estado::class, 'estado_anterior_id');
    }

    public function estadoNuevo()
    {
        return $this->belongsTo(Estado::class, 'estado_nuevo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
