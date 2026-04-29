<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logEvent('created');
        });

        static::updated(function ($model) {
            $model->logEvent('updated');
        });

        static::deleted(function ($model) {
            $model->logEvent('deleted');
        });
    }

    protected function logEvent($event)
    {
        $oldValues = null;
        $newValues = null;

        if ($event === 'updated') {
            $newValues = $this->getDirty();
            $oldValues = array_intersect_key($this->getOriginal(), $newValues);
        } elseif ($event === 'created') {
            $newValues = $this->getAttributes();
        } elseif ($event === 'deleted') {
            $oldValues = $this->getOriginal();
        }

        // Evitar loggear si no hay cambios reales en update
        if ($event === 'updated' && empty($newValues)) {
            return;
        }

        // Campos a ignorar (seguridad y ruido)
        $ignore = ['updated_at', 'created_at', 'password', 'remember_token', 'firebase_synced_at'];
        if ($oldValues) $oldValues = array_diff_key($oldValues, array_flip($ignore));
        if ($newValues) $newValues = array_diff_key($newValues, array_flip($ignore));

        AuditLog::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($this),
            'model_id' => $this->id ?? $this->uuid ?? 0,
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
