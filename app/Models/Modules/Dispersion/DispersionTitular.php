<?php

namespace App\Models\Modules\Dispersion;

use Illuminate\Database\Eloquent\Model;

class DispersionTitular extends Model
{
    protected $table = 'dispersion_pensionados_titulares';

    protected $fillable = [
        'carga_id', 'tipo_afiliado', 'nss', 'cedula', 'codigo_pensionado', 'salario', 'monto_descuento_salud', 
        'monto_capita_adicional', 'tipo_pago', 'cuenta_banco', 'tipo_pensionado', 
        'origen_pension', 'monto_total', 'periodo', 'raw_line', 'hash_integridad'
    ];

    protected $casts = [
        'salario' => 'decimal:2',
        'monto_descuento_salud' => 'decimal:2',
        'monto_capita_adicional' => 'decimal:2',
        'monto_total' => 'decimal:2',
    ];

    public function carga()
    {
        return $this->belongsTo(DispersionCarga::class, 'carga_id');
    }

    public function dependientes()
    {
        return $this->hasMany(DispersionDependiente::class, 'titular_id');
    }
}
