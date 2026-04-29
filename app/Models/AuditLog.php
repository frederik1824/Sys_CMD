<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'model_type', 'model_id', 'event',
        'old_values', 'new_values', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo(null, 'model_type', 'model_id');
    }

    /**
     * Human-readable action name mapping.
     */
    public function getHumanActionAttribute(): string
    {
        return match ($this->action) {
            'CREATE_DOCUMENT' => 'Creación de Documento',
            'UPLOAD_VERSION' => 'Nueva Versión Cargada',
            'APPROVE_DOCUMENT' => 'Documento Aprobado',
            'REJECT_DOCUMENT' => 'Documento Rechazado',
            'APPROVE_VERSION' => 'Versión Validada',
            'UPDATE_PROFILE' => 'Perfil Actualizado',
            default => str_replace('_', ' ', ucfirst(strtolower($this->action))),
        };
    }

    /**
     * Extrae información relevante del JSON de detalles y la convierte en una frase natural.
     */
    public function getFormattedDetailsAttribute(): string
    {
        $details = $this->details;
        if (!is_array($details)) return (string) $details;

        if ($this->action === 'CREATE_DOCUMENT') {
            return "Se registró '{$details['title']}' con el código {$details['code']}.";
        }

        if ($this->action === 'UPLOAD_VERSION' || $this->action === 'APPROVE_VERSION') {
            return "Versión {$details['version']} del documento maestro.";
        }

        if ($this->action === 'REJECT_DOCUMENT') {
            return "Motivo: " . ($details['reason'] ?? 'Sin observaciones.');
        }

        if ($this->action === 'APPROVE_DOCUMENT') {
            return "Validación institucional completada.";
        }

        // Default: format JSON keys as text if possible
        return collect($details)->map(fn($v, $k) => ucfirst($k) . ": " . (is_array($v) ? json_encode($v) : $v))->implode(', ');
    }
}
