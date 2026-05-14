<?php

namespace App\Models\Modules\Dispersion;

use Illuminate\Database\Eloquent\Model;

class DispersionDependiente extends Model
{
    protected $table = 'dispersion_pensionados_dependientes';

    protected $fillable = [
        'carga_id', 'titular_id', 'cedula_titular', 'nss_titular', 'codigo_pensionado', 
        'cedula_dependiente', 'nss_dependiente', 'tipo_pensionado', 'origen_pension', 
        'periodo', 'raw_line', 'hash_integridad'
    ];

    public function carga()
    {
        return $this->belongsTo(DispersionCarga::class, 'carga_id');
    }

    public function titular()
    {
        return $this->belongsTo(DispersionTitular::class, 'titular_id');
    }
}
