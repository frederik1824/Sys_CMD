<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PssAuditLog extends Model
{
    protected $table = 'pss_audit_logs';

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'user_id',
        'accion',
        'campo',
        'valor_anterior',
        'valor_nuevo',
        'ip_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }
}
