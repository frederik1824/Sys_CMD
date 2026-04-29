<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\FirebaseSyncable;

class Afiliado extends Model
{
    use Auditable, HasUuids, SoftDeletes, FirebaseSyncable;

    /**
     * Define the document ID for Firebase (Cedula)
     */
    public function getFirebaseDocumentId(): string
    {
        return $this->cedula ?? '';
    }

    /**
     * Define which columns should be generated as UUIDs.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected $fillable = [
        'uuid', 'corte_id', 'responsable_id', 'estado_id', 'empresa_id', 'nombre_completo', 'cedula',
        'sexo', 'telefono', 'direccion', 'provincia', 'municipio', 'empresa', 'rnc_empresa',
        'codigo', 'lote_id', 'proveedor_id', 'costo_entrega', 'poliza', 'contrato',
        'fecha_entrega_proveedor', 'liquidado', 'fecha_liquidacion', 'recibo_liquidacion',
        'fecha_entrega_safesure', 'lote_liquidacion_id',
        'provincia_id', 'municipio_id', 'reasignado', 'firebase_synced_at'
    ];

    protected $casts = [
        'fecha_entrega_safesure' => 'datetime',
        'fecha_liquidacion' => 'datetime',
        'fecha_entrega_proveedor' => 'datetime',
        'liquidado' => 'boolean',
        'reasignado' => 'boolean',
        'firebase_synced_at' => 'datetime'
    ];

    /**
     * Regla de Negocio: Intercepta datos entrantes de Firebase para aplicar el flujo de validación CMD
     */
    public static function formatCedula($value)
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        if (strlen($clean) === 11) {
            return substr($clean, 0, 3) . '-' . substr($clean, 3, 7) . '-' . substr($clean, 10, 1);
        }
        return $value;
    }

    public function setCedulaAttribute($value)
    {
        $this->attributes['cedula'] = self::formatCedula($value);
    }

    public function applyGatingRule(array $data): array
    {
        $isCmd = env('FIREBASE_SYNC_ROLE') === 'CMD';
        $incomingEstadoId = $data['estado_id'] ?? null;

        // Regla: Si Safe marca como Completado (9), CMD lo marca como Pendiente de recepción (7)
        // Pero si ya está marcado localmente como 9, respetamos la confirmación local.
        if ($isCmd && $incomingEstadoId == 9) {
            if ($this->estado_id != 9) {
                $data['estado_id'] = 7;
            }
        }

        return $data;
    }
    
    public function getStatusColorClassAttribute()
    {
        $estado = strtolower($this->estado?->nombre ?? 'pendiente');
        
        // Si está completado, usamos es_final o el nombre
        if ($this->estado?->es_final || $estado === 'completado') {
            return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        }
        
        return match($estado) {
            'pendiente'  => 'bg-amber-100 text-amber-700 border-amber-200',
            'entregado'  => 'bg-blue-100 text-blue-700 border-blue-200',
            'enviado'    => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            'produccion' => 'bg-slate-100 text-slate-700 border-slate-200',
            default      => 'bg-slate-50 text-slate-500 border-slate-100',
        };
    }

    public function getDataQualityAttribute()
    {
        $fields = [
            'nombre_completo' => 20,
            'cedula' => 20,
            'telefono' => 10,
            'direccion' => 15,
            'provincia' => 10, // Cambiado de provincia_id a provincia para mayor compatibilidad
            'municipio' => 10, // Cambiado de municipio_id a municipio
            'empresa_id' => 10,
            'estado_id' => 5,
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
            'is_ready' => $score >= 75 // Afiliados requieren un poco más de rigor
        ];
    }

    /**
     * Regla Estricta: Asegurar costo base al guardar si está completado
     */
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope(new \App\Models\Scopes\ResponsableScope);
        
        static::saving(function ($afiliado) {
            // Regla Estricta: Asegurar costo base al guardar si está completado
            if ($afiliado->estado_id) {
                $estado = \App\Models\Estado::find($afiliado->estado_id);
                if ($estado && strtolower($estado->nombre) === 'completado') {
                    if (is_null($afiliado->costo_entrega) || $afiliado->costo_entrega == 0) {
                        if ($afiliado->proveedor_id && $afiliado->proveedor?->precio_base > 0) {
                            $afiliado->costo_entrega = $afiliado->proveedor->precio_base;
                        } elseif ($afiliado->responsable_id && $afiliado->responsable?->precio_entrega > 0) {
                            $afiliado->costo_entrega = $afiliado->responsable->precio_entrega;
                        }
                    }
                }
            }

            // --- SUGERENCIA 1: CALIDAD DE DATOS ---
            // Normalizar Nombre Completo (Title Case)
            if ($afiliado->nombre_completo) {
                $afiliado->nombre_completo = mb_convert_case(mb_strtolower($afiliado->nombre_completo), MB_CASE_TITLE, "UTF-8");
            }

            // Herencia de datos de Empresa si están vacíos
            if ($afiliado->empresa_id) {
                $empresa = $afiliado->empresaModel;
                if ($empresa) {
                    $afiliado->telefono = $afiliado->telefono ?: $empresa->telefono;
                    $afiliado->direccion = $afiliado->direccion ?: $empresa->direccion;
                    $afiliado->provincia_id = $afiliado->provincia_id ?: $empresa->provincia_id;
                    $afiliado->municipio_id = $afiliado->municipio_id ?: $empresa->municipio_id;
                }
            }

            // Normalizar Dirección (Expandir abreviaturas)
            $afiliado->normalizeAddress();
        });

        static::creating(function ($afiliado) {
            // Auto-asignación de responsable si es nulo (Evita que el registro sea invisible por Scopes)
            if (!$afiliado->responsable_id && auth()->check()) {
                $afiliado->responsable_id = auth()->user()->responsable_id;
            }

            // Auto-asignación geográfica general
            if (!$afiliado->responsable_id && $afiliado->provincia_id) {
                $responsableGeo = \Illuminate\Support\Facades\DB::table('provincia_responsable')
                    ->where('provincia_id', $afiliado->provincia_id)
                    ->inRandomOrder() 
                    ->first();
                
                if ($responsableGeo) {
                    $afiliado->responsable_id = $responsableGeo->responsable_id;
                }
            }
        });
    }

    /**
     * Calcula los días transcurridos desde que se entregó a un proveedor
     */
    public function getDiasTranscurridosAttribute()
    {
        // Si no se ha entregado a un proveedor o ya está liquidado, no contamos días
        if (!$this->fecha_entrega_proveedor) return 0;
        
        /** @var Carbon $fecha */
        $fecha = $this->fecha_entrega_proveedor;
        return $fecha->diffInDays(now());
    }

    /**
     * Determina el color del semáforo basado en el SLA (20 días según usuario)
     */
    public function getSlaStatusAttribute()
    {
        if (strtolower($this->estado?->nombre) === 'completado') return 'completado';
        if (!$this->fecha_entrega_proveedor) return 'pendiente';

        $dias = $this->dias_transcurridos;
        if ($dias >= 20) return 'critico'; // Rojo
        if ($dias >= 15) return 'alerta';   // Amarillo
        return 'en_tiempo';                // Verde
    }
    public function getRncEmpresaAttribute($value)
    {
        return $value ?: $this->empresaModel?->rnc;
    }


    public function corte()
    {
        return $this->belongsTo(Corte::class);
    }

    public function responsable()
    {
        return $this->belongsTo(Responsable::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function municipioRel()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function provinciaRel()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }

    /**
     * @deprecated Usar provinciaRel
     */
    public function provincia()
    {
        return $this->provinciaRel();
    }

    /**
     * @deprecated Usar municipioRel
     */
    public function municipio()
    {
        return $this->municipioRel();
    }

    /**
     * RESOLUCIÓN DE UBICACIÓN FINAL (Regla de Negocio)
     * Prioridad: Afiliado Directo > Empresa > Legacy
     */
    public function getProvinciaFinalAttribute()
    {
        // Si el afiliado tiene provincia_id directa, retornamos esa relación (modelo)
        if ($this->provincia_id) {
            return $this->provinciaRel; 
        }
        // Si no, retornamos la de la empresa
        return $this->empresaModel?->provinciaRel;
    }

    public function getMunicipioFinalAttribute()
    {
        if ($this->municipio_id) {
            return $this->municipioRel;
        }
        return $this->empresaModel?->municipioRel;
    }

    public function getDireccionPersonalAttribute()
    {
        return $this->direccion ?: 'SIN DIRECCIÓN PERSONAL';
    }

    public function getDireccionEmpresaAttribute()
    {
        return $this->empresaModel?->direccion ?: 'SIN DIRECCIÓN DE EMPRESA';
    }

    public function getDireccionFinalAttribute()
    {
        // Prioridad: Si hay dirección personal cargada explícitamente, esa manda.
        // Si no, usamos la de la empresa.
        return $this->direccion ?: ($this->empresaModel?->direccion ?: 'DIRECCIÓN NO DISPONIBLE');
    }

    public function getProvinciaNombreAttribute()
    {
        return $this->provincia_final?->nombre ?? ($this->attributes['provincia'] ?? 'SIN PROVINCIA');
    }

    public function getMunicipioNombreAttribute()
    {
        return $this->municipio_final?->nombre ?? ($this->attributes['municipio'] ?? 'SIN MUNICIPIO');
    }

    /**
     * Normaliza los campos de dirección eliminando abreviaturas comunes
     */
    public function normalizeAddress()
    {
        if (!$this->direccion) return;

        $replacements = [
            '/\bC\/\b/i' => 'Calle ',
            '/\bNo\.\b/i' => '#',
            '/\bEsq\.\b/i' => 'Esquina ',
            '/\bApt\.\b/i' => 'Apartamento ',
            '/\bRes\.\b/i' => 'Residencial ',
            '/\bAut\.\b/i' => 'Autopista ',
        ];

        $this->direccion = preg_replace(array_keys($replacements), array_values($replacements), $this->direccion);
        $this->direccion = trim(preg_replace('/\s+/', ' ', $this->direccion));
    }

    /**
     * Verifica si existe un historial previo de entrega exitosa para esta cédula
     */
    public function scopeHistorialEntrega($query, $cedula)
    {
        return $query->where('cedula', $cedula)
            ->whereHas('estado', function($q) {
                $q->whereIn('nombre', ['COMPLETADO', 'LIQUIDADO']);
            });
    }

    public function empresaModel()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class);
    }

    public function loteLiquidacion()
    {
        return $this->belongsTo(LoteLiquidacion::class, 'lote_liquidacion_id');
    }

    public function evidenciasAfiliado()
    {
        return $this->hasMany(EvidenciaAfiliado::class);
    }

    // Scopes para Reporte Dual (Basados en el Responsable actual tras unificación)
    public function scopeArs($query)
    {
        return $query->where('responsable_id', 1); // ID 1 es ARS CMD
    }

    public function scopeNoArs($query)
    {
        return $query->where('responsable_id', '!=', 1); // Cualquier otro es Extra Empresa (Safesure, etc)
    }

    public function scopeEnEmpresaReal($query)
    {
        return $query->whereHas('empresaModel', function($q) {
            $q->where('es_real', true);
        });
    }

    public function scopeEnEmpresaFilial($query)
    {
        return $query->whereHas('empresaModel', function($q) {
            $q->where('es_filial', true);
        });
    }

    // Scope para métricas de Proveedores de Entrega
    public function scopeEntregadoProveedor($query)
    {
        return $query->whereNotNull('fecha_entrega_proveedor');
    }

    public function notas()
    {
        return $this->hasMany(NotaAfiliado::class);
    }

    public function despachoItems()
    {
        return $this->hasMany(DespachoItem::class);
    }

    public function llamadas()
    {
        return $this->hasMany(Llamada::class);
    }

    public function asignacionesLlamadas()
    {
        return $this->hasMany(AsignacionLlamada::class);
    }
}
