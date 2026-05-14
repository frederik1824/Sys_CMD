<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallCenterRegistro extends Model
{
    use \App\Traits\FirebaseSyncable;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'uuid', 'carga_id', 'estado_id', 'operador_id', 'created_by_id',
        'cedula', 'poliza', 'nombre', 'telefono', 'celular', 'tipo_afiliado',
        'empresa_nombre', 'empresa_rnc', 'empresa_contacto', 'empresa_direccion',
        'provincia', 'municipio',
        'afiliado_id', 'empresa_id', 'lote_id',
        'intentos_llamada', 'ultima_gestion_at', 'proximo_contacto_at', 'observaciones', 'prioridad'
    ];

    protected $casts = [
        'ultima_gestion_at' => 'datetime',
        'proximo_contacto_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function getFirebaseDocumentId(): string
    {
        return $this->uuid ?? '';
    }

    public function estado()
    {
        return $this->belongsTo(CallCenterEstado::class, 'estado_id');
    }

    public function carga()
    {
        return $this->belongsTo(CallCenterCarga::class, 'carga_id');
    }

    public function operador()
    {
        return $this->belongsTo(\App\Models\User::class, 'operador_id');
    }

    public function gestiones()
    {
        return $this->hasMany(CallCenterGestion::class, 'registro_id');
    }

    public function documentos()
    {
        return $this->hasMany(CallCenterDocumento::class, 'registro_id');
    }

    public function despacho()
    {
        return $this->hasOne(CallCenterDespacho::class, 'registro_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function afiliadoMaestro()
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id', 'uuid');
    }

    public function bandejaSalida()
    {
        return $this->hasOne(CallCenterBandejaSalida::class, 'registro_id');
    }
}
