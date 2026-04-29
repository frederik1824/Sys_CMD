<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentStatus extends Model
{
    protected $fillable = ['name', 'slug', 'color_class'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
