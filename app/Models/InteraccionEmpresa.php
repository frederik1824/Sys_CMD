<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteraccionEmpresa extends Model
{
    protected $fillable = [
        'empresa_id', 'user_id', 'tipo', 'descripcion', 'fecha_contacto'
    ];

    protected $casts = [
        'fecha_contacto' => 'datetime'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
