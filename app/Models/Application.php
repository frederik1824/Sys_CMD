<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Application extends Model
{
    protected $fillable = [
        'slug', 'name', 'description', 'route', 'icon', 'color', 
        'is_active', 'is_visible', 'order_weight'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_application_roles')
                    ->withPivot(['role_id', 'is_active', 'assigned_at'])
                    ->withTimestamps();
    }

    public function userAccess(): HasMany
    {
        return $this->hasMany(UserApplicationRole::class, 'application_id');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(UserApplicationRole::class, 'application_id');
    }
}
