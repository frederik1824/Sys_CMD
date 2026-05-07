<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class DispersionIndicator extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = ['name', 'code', 'category', 'is_total', 'is_active', 'order_weight'];

    protected $casts = [
        'is_total' => 'boolean',
        'is_active' => 'boolean',
    ];
}
