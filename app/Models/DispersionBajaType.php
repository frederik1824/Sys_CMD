<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class DispersionBajaType extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = ['name', 'code', 'is_active', 'order_weight'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
