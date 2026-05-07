<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    protected $fillable = [
        'is_automated',
        'schedule_frequency',
        'schedule_time',
        'max_backups',
        'custom_path',
    ];

    protected $casts = [
        'is_automated' => 'boolean',
        'max_backups' => 'integer',
    ];
}
