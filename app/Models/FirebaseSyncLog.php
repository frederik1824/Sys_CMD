<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirebaseSyncLog extends Model
{
    protected $fillable = [
        'type', 'status', 'summary', 'items_count', 'performed_by', 'started_at', 'finished_at'
    ];

    protected $casts = [
        'summary' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function getHealthTagsAttribute(): array
    {
        $tags = [];
        $summary = $this->summary ?? [];

        if ($this->status === 'failed') {
            $tags[] = ['label' => 'Revisión', 'class' => 'bg-rose-500/20 text-rose-400 border-rose-500/30'];
        }

        if (isset($summary['dry_run']) && $summary['dry_run']) {
            $tags[] = ['label' => 'Simulación', 'class' => 'bg-amber-500/20 text-amber-400 border-amber-500/30'];
        }

        if (isset($summary['intensity'])) {
            $val = (int)$summary['intensity'];
            if ($val > 300) {
                $tags[] = ['label' => 'Turbo', 'class' => 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30'];
            } elseif ($val < 30) {
                $tags[] = ['label' => 'Eco', 'class' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'];
            }
        }

        if ($this->items_count > 1000) {
            $tags[] = ['label' => 'Masivo', 'class' => 'bg-blue-500/20 text-blue-400 border-blue-500/30'];
        }

        return $tags;
    }
}
