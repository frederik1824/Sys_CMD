<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category',
        'image_url',
        'is_active',
        'is_urgent',
        'user_id',
        'published_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_urgent' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('published_at', '<=', now());
    }
}
