<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Despacho extends Model
{
    protected $fillable = ['mensajero_id', 'ruta_id', 'status', 'fecha_salida', 'fecha_retorno', 'observaciones'];

    protected $casts = [
        'fecha_salida' => 'datetime',
        'fecha_retorno' => 'datetime'
    ];

    public function mensajero()
    {
        return $this->belongsTo(Mensajero::class);
    }

    public function ruta()
    {
        return $this->belongsTo(Ruta::class);
    }

    public function items()
    {
        return $this->hasMany(DespachoItem::class);
    }
}
