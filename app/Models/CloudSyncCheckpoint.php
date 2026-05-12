<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CloudSyncCheckpoint extends Model
{
    protected $fillable = [
        'process_name',
        'company',
        'last_success_at',
        'last_document_id',
        'last_document_updated_at',
        'processed_count',
        'failed_count',
        'status',
        'error_message',
        'started_at',
        'finished_at',
        'sync_type',
        'user_id',
        'batch_size',
        'read_count'
    ];

    protected $casts = [
        'last_success_at' => 'datetime',
        'last_document_updated_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
