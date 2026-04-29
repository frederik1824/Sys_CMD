<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipios';
    protected $fillable = ['provincia_id', 'nombre'];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }
}
