<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $fillable = ['nombre'];

    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }

    public function responsables()
    {
        return $this->belongsToMany(Responsable::class, 'provincia_responsable');
    }
}
