<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispersionBajaValue extends Model
{
    protected $fillable = [
        'corte_id', 'baja_type_id', 'quantity'
    ];

    public function bajaType(): BelongsTo
    {
        return $this->belongsTo(DispersionBajaType::class, 'baja_type_id');
    }

    public function corte(): BelongsTo
    {
        return $this->belongsTo(DispersionCorte::class, 'corte_id');
    }
}
