<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotivoRechazoTraspaso extends Model
{
    protected $table = 'motivo_rechazo_traspasos';
    protected $fillable = ['codigo_sisalril', 'codigo_unsigima', 'descripcion', 'activo'];
}
