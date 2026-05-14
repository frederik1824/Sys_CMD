<?php

namespace App\Models\Modules\Dispersion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DispersionCarga extends Model
{
    use SoftDeletes;

    protected $table = 'dispersion_pensionados_cargas';

    protected $fillable = [
        'uuid', 'periodo', 'fecha_carga', 'user_id', 'nombre_archivo', 'archivo_path', 
        'hash_archivo', 'total_registros', 'total_titulares', 'total_dependientes', 
        'monto_total_dispersado', 'monto_total_salud', 'monto_total_capita', 'estado', 'observaciones'
    ];

    protected $casts = [
        'fecha_carga' => 'datetime',
        'monto_total_dispersado' => 'decimal:2',
        'monto_total_salud' => 'decimal:2',
        'monto_total_capita' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function titulares()
    {
        return $this->hasMany(DispersionTitular::class, 'carga_id');
    }

    public function dependientes()
    {
        return $this->hasMany(DispersionDependiente::class, 'carga_id');
    }

    public function logs()
    {
        return $this->hasMany(DispersionLog::class, 'carga_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
