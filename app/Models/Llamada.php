<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Llamada extends Model
{
    use HasFactory;

    protected $fillable = [
        'afiliado_id',
        'usuario_id',
        'estado_llamada',
        'observacion',
        'evidencia_foto',
        'fecha_llamada',
        'proximo_contacto',
    ];

    protected $casts = [
        'fecha_llamada' => 'datetime',
        'proximo_contacto' => 'date',
    ];

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
