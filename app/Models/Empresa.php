<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\FirebaseSyncable;

class Empresa extends Model
{
    use Auditable, HasUuids, SoftDeletes, FirebaseSyncable;

    /**
     * Define the document ID for Firebase (Use UUID for stability)
     */
    public function getFirebaseDocumentId(): string
    {
        return (string) ($this->uuid ?? '');
    }

    /**
     * Define which columns should be generated as UUIDs.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the route key name for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected $fillable = [
        'uuid', 'nombre', 'rnc', 'direccion', 'telefono', 'es_real', 'es_filial',
        'provincia_id', 'municipio_id',
        'contacto_nombre', 'contacto_puesto', 'contacto_telefono', 'contacto_email',
        'comision_tipo', 'comision_valor',
        'promotor_id', 'estado_contacto',
        'latitude', 'longitude', 'google_maps_url',
        // Legacy fields marked for future removal
        'provincia', 'municipio', 'firebase_synced_at', 'es_verificada' 
    ];

    protected $casts = [
        'es_real' => 'boolean',
        'es_filial' => 'boolean',
        'es_verificada' => 'boolean',
        'comision_valor' => 'decimal:2',
        'firebase_synced_at' => 'datetime',
    ];

    /**
     * Determina si la empresa requiere atención basada en la última interacción
     */
    public function getDataQualityAttribute()
    {
        $fields = [
            'nombre' => 25,
            'rnc' => 15,
            'direccion' => 15,
            'telefono' => 10,
            'provincia_id' => 10,
            'municipio_id' => 10,
            'latitude' => 7.5,
            'longitude' => 7.5,
        ];

        $score = 0;
        $missing = [];

        foreach ($fields as $field => $weight) {
            if (!empty($this->{$field})) {
                $score += $weight;
            } else {
                $missing[] = $field;
            }
        }

        $level = 'critical';
        $color = 'rose';
        if ($score >= 90) { $level = 'perfect'; $color = 'emerald'; }
        elseif ($score >= 70) { $level = 'good'; $color = 'blue'; }
        elseif ($score >= 40) { $level = 'warning'; $color = 'amber'; }

        return (object) [
            'score' => round($score),
            'level' => $level,
            'color' => $color,
            'missing' => $missing,
            'is_ready' => $score >= 70
        ];
    }

    /**
     * Determina si la empresa requiere atención basada en la última interacción
     */
    public function getSlaStatusAttribute()
    {
        $ultimaInteraccion = method_exists($this, 'interacciones') ? $this->interacciones()->latest()->first() : null;
        
        if (!$ultimaInteraccion) {
            $clase = 'critical';
            $mensaje = 'Sin actividad registrada';
            $dias = null;
        } else {
            $dias = $ultimaInteraccion->created_at->diffInDays(now());
            if ($dias >= 15) {
                $clase = 'critical';
                $mensaje = "Inactiva por $dias días";
            } elseif ($dias >= 7) {
                $clase = 'warning';
                $mensaje = "Revisión pendiente ($dias días)";
            } else {
                $clase = 'good';
                $mensaje = 'Al día';
            }
        }

        return (object) [
            'level' => $clase,
            'message' => $mensaje,
            'days' => $dias,
            'color' => match($clase) {
                'critical' => 'rose',
                'warning' => 'amber',
                'good' => 'emerald',
            }
        ];
    }

    public function promotor()
    {
        return $this->belongsTo(User::class, 'promotor_id');
    }

    public function interacciones()
    {
        return $this->hasMany(InteraccionEmpresa::class)->orderBy('fecha_contacto', 'desc');
    }

    /**
     * Relación optimizada para obtener solo la última interacción
     */
    public function latestInteraccion()
    {
        return $this->hasOne(InteraccionEmpresa::class)->latestOfMany('fecha_contacto');
    }

    public function afiliados()
    {
        return $this->hasMany(Afiliado::class);
    }

    public function provinciaRel()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }

    public function municipioRel()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    /**
     * @deprecated Usar provinciaRel()
     */
    public function provincia()
    {
        return $this->provinciaRel();
    }

    /**
     * @deprecated Usar municipioRel()
     */
    public function municipio()
    {
        return $this->municipioRel();
    }

    /**
     * Automatic Coordinate Extractor from Google Maps URL
     */
    public function setGoogleMapsUrlAttribute($value)
    {
        $this->attributes['google_maps_url'] = $value;

        if (empty($value)) return;

        $finalUrl = $value;

        // Si es un link corto de Google, resolvemos el destino real
        if (str_contains($value, 'goo.gl') || str_contains($value, 'maps.app.goo.gl')) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->get($value);
                $finalUrl = $response->effectiveUri(); 
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("No se pudo resolver el URL corto: " . $e->getMessage());
            }
        }

        // Buscamos el patrón lat,long en el URL resultante
        if (preg_match('/(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/', (string)$finalUrl, $matches)) {
            $this->attributes['latitude'] = $matches[1];
            $this->attributes['longitude'] = $matches[2];
        }
    }
}
