<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenciaAfiliado extends Model
{
    protected $fillable = [
        'afiliado_id', 'tipo_documento', 'status', 'file_path', 'user_id', 'validated_by', 'observaciones'
    ];

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validador()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
