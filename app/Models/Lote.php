<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use \App\Traits\Auditable;
    protected $fillable = [
        'nombre', 'corte_id', 'empresa_tipo', 'user_id', 'total_registros', 'observaciones'
    ];

    public function corte()
    {
        return $this->belongsTo(Corte::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function afiliados()
    {
        return $this->hasMany(Afiliado::class);
    }
}
