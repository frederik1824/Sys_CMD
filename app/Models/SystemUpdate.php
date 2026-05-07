<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemUpdate extends Model
{
    protected $fillable = [
        'version', 'build_number', 'type', 'changelog', 
        'status', 'package_path', 'checksum', 
        'started_at', 'completed_at', 'error_log', 'executed_by'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function backup(): BelongsTo
    {
        return $this->belongsTo(SystemBackup::class, 'id', 'update_id');
    }
}
