<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceStatus extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'status',
        'icon',
        'last_incident',
    ];
}
