<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaTraspaso extends Model
{
    protected $fillable = [
        'agente_id',
        'periodo',
        'meta_cantidad',
    ];

    public function agente()
    {
        return $this->belongsTo(AgenteTraspaso::class, 'agente_id');
    }
}
