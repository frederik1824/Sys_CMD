<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteLiquidacion extends Model
{
    protected $fillable = [
        'recibo',
        'fecha',
        'monto_total',
        'conteo_registros',
        'responsable_id',
        'proveedor_id',
        'evidencia_path'
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto_total' => 'decimal:2'
    ];

    public function afiliados()
    {
        return $this->hasMany(Afiliado::class, 'lote_liquidacion_id');
    }

    public function responsable()
    {
        return $this->belongsTo(Responsable::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
