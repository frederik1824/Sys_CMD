<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispersionValue extends Model
{
    protected $fillable = [
        'corte_id', 'indicator_id', 'quantity', 'amount'
    ];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(DispersionIndicator::class, 'indicator_id');
    }

    public function corte(): BelongsTo
    {
        return $this->belongsTo(DispersionCorte::class, 'corte_id');
    }
}
