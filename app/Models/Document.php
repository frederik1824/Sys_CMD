<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'code', 'title', 'description', 'department_id',
        'document_type_id', 'document_status_id', 'is_regulatory',
        'visibility', 'created_by', 'expires_at'
    ];

    protected $casts = [
        'is_regulatory' => 'boolean',
        'expires_at' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Institutional Visibility Scope
     */
    public function scopeVisibleTo($query, $user)
    {
        if (!$user) {
            return $query->where('visibility', 'public');
        }

        // Adms have total visibility
        if ($user->hasRole('Administrador')) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('visibility', 'public') // Everyone
              ->orWhere(function ($sq) use ($user) {
                  $sq->where('visibility', 'department')
                     ->where('department_id', $user->department_id); // Same Team
              })
              ->orWhere(function ($sq) use ($user) {
                  $sq->where('visibility', 'private')
                     ->where('created_by', $user->id); // Onwer only
              });
        });
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(DocumentStatus::class, 'document_status_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }
}
