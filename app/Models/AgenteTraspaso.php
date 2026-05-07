<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgenteTraspaso extends Model
{
    protected $fillable = ['nombre', 'supervisor_id', 'activo'];

    public function supervisor()
    {
        return $this->belongsTo(SupervisorTraspaso::class, 'supervisor_id');
    }

    public function metas()
    {
        return $this->hasMany(MetaTraspaso::class, 'agente_id');
    }

    public function currentMeta()
    {
        return $this->hasOne(MetaTraspaso::class, 'agente_id')->where('periodo', now()->format('Y-m'));
    }
}
