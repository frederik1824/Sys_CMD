<?php

namespace App\Models\Modules\Dispersion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PensionadoMaster extends Model
{
    use SoftDeletes;

    protected $table = 'dispersion_pensionados_master';

    protected $fillable = [
        'uuid', 'cedula', 'solicitud_id', 'nss', 'nombre_completo', 'fecha_nacimiento', 
        'genero', 'tipo_pension', 'institucion_pension', 'monto_pension', 
        'ultimo_pago_confirmado_at', 'notificado_at', 'estado_sistema', 'data_adicional'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'ultimo_pago_confirmado_at' => 'datetime',
        'notificado_at' => 'datetime',
        'data_adicional' => 'json',
        'monto_pension' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function pagosTitular()
    {
        return $this->hasMany(DispersionTitular::class, 'cedula', 'cedula');
    }

    public function pagosDependiente()
    {
        return $this->hasMany(DispersionDependiente::class, 'cedula_dependiente', 'cedula');
    }

    public function getHistorialPagosAttribute()
    {
        return $this->pagosTitular->concat($this->pagosDependiente)->sortByDesc('periodo');
    }
}
