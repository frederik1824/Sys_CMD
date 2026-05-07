<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispersionPeriod extends Model
{
    use Auditable;

    protected $fillable = [
        'year', 'month', 'status', 'created_by', 'closed_by', 'closed_at', 'notes'
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function cortes(): HasMany
    {
        return $this->hasMany(DispersionCorte::class, 'period_id')->orderBy('corte_number');
    }

    public function getMonthNameAttribute(): string
    {
        return [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ][$this->month] ?? 'N/A';
    }
}
