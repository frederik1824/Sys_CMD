<?php

namespace App\Models\Modules\Dispersion;

use Illuminate\Database\Eloquent\Model;

class DispersionLog extends Model
{
    protected $table = 'dispersion_pensionados_logs';

    protected $fillable = [
        'carga_id', 'tipo', 'mensaje', 'detalles'
    ];

    public function carga()
    {
        return $this->belongsTo(DispersionCarga::class, 'carga_id');
    }
}
