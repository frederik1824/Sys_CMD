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
}
