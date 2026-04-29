<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorTraspaso extends Model
{
    protected $fillable = ['nombre', 'activo'];

    public function agentes()
    {
        return $this->hasMany(AgenteTraspaso::class, 'supervisor_id');
    }
}
