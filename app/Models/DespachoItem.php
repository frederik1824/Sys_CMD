<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespachoItem extends Model
{
    protected $fillable = ['despacho_id', 'afiliado_id', 'status', 'motivo_fallo', 'fecha_evento'];

    protected $casts = [
        'fecha_evento' => 'datetime'
    ];

    public function despacho()
    {
        return $this->belongsTo(Despacho::class);
    }

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class);
    }
}
