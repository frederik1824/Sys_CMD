<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DispersionCorte extends Model
{
    use Auditable;

    protected $fillable = [
        'period_id', 'corte_number', 'reception_date', 'status', 'user_id', 'notes'
    ];

    protected $casts = [
        'reception_date' => 'date',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(DispersionPeriod::class, 'period_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(DispersionValue::class, 'corte_id');
    }

    public function bajaValues(): HasMany
    {
        return $this->hasMany(DispersionBajaValue::class, 'corte_id');
    }
}
