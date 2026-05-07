<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallCenterCarga extends Model
{
    protected $fillable = ['nombre', 'user_id', 'total_registros', 'registros_nuevos', 'registros_actualizados'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registros()
    {
        return $this->hasMany(CallCenterRegistro::class, 'carga_id');
    }
}
