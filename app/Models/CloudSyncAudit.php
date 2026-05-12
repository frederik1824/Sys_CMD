<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CloudSyncAudit extends Model
{
    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'field',
        'old_value',
        'new_value',
        'company_origin',
        'user_name',
        'synced_at'
    ];

    protected $casts = [
        'synced_at' => 'datetime'
    ];

    public function auditable()
    {
        return $this->morphTo();
    }
}
