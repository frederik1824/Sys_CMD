<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaAfiliado extends Model
{
    protected $table = 'notas_afiliados';
    protected $fillable = ['afiliado_id', 'user_id', 'contenido'];

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeEntregadoProveedor($query)
    {
        return $query->whereNotNull('fecha_entrega_proveedor');
    }
}
