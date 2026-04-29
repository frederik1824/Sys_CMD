<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ResponsableScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Si el usuario está autenticado y tiene un responsable asignado, filtramos.
        // Si es Admin/Admin sin responsable asignado (responsable_id null), lo ve todo.
        if (Auth::check() && Auth::user()->responsable_id) {
            $builder->where($model->getTable() . '.responsable_id', Auth::user()->responsable_id);
        }
    }
}
