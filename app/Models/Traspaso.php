<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class Traspaso extends Model
{
    use Auditable;

    protected $fillable = [
        'nombre_afiliado',
        'cedula_afiliado',
        'nombre_solicitante',
        'cedula_solicitante',
        'fecha_solicitud',
        'fecha_envio_epbd',
        'numero_solicitud_epbd',
        'pendiente_carga_documento',
        'pendiente_aprobar_consentimiento',
        'agente',
        'estado',
        'motivos_estado',
        'fecha_efectivo',
        'periodo_efectivo',
        'cantidad_dependientes',
        'es_emitido',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_solicitud' => 'date',
            'fecha_envio_epbd' => 'date',
            'fecha_efectivo' => 'date',
            'pendiente_carga_documento' => 'boolean',
            'pendiente_aprobar_consentimiento' => 'boolean',
            'es_emitido' => 'boolean',
        ];
    }
}
