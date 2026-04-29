<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ResponsableScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check() && !auth()->user()->hasRole(['Admin'])) {
            // Si es un usuario con rol restrictivo (como Operador), debe tener un responsable_id
            // Si no lo tiene, forzamos un filtro que devuelva vacío (id = 0) por seguridad.
            $builder->where('responsable_id', auth()->user()->responsable_id ?? 0);
            
            // Si es operador, restringirlo estrictamente a ver solo los registros de la empresa CMD (Vía Lote)
            if (auth()->user()->hasRole('Operador')) {
                $builder->whereHas('lote', function($q) {
                    $q->where('empresa_tipo', 'CMD');
                });
            }
        }
    }
}
