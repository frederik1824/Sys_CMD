<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\FirebaseSyncable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, \Spatie\Permission\Traits\HasRoles, \App\Traits\Auditable, SoftDeletes, FirebaseSyncable;

    /**
     * Define the document ID for Firebase (Email)
     */
    public function getFirebaseDocumentId(): string
    {
        return $this->email;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
        'avatar',
        'password',
        'responsable_id',
        'last_login_at',
        'last_login_ip',
        'departamento_id',
    ];

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'firebase_synced_at' => 'datetime',
        ];
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Matriz de Accesos Centralizada
     */
    public function applicationAccess(): HasMany
    {
        return $this->hasMany(UserApplicationRole::class, 'user_id');
    }

    /**
     * Valida si el usuario tiene un rol específico en una aplicación determinada (Slug)
     */
    public function hasRoleInApplication($roleName, $applicationSlug)
    {
        if ($this->hasAnyRole(['Admin', 'Super-Admin'])) return true;

        return $this->applicationAccess()
            ->where('is_active', true)
            ->whereHas('application', fn($q) => $q->where('slug', $applicationSlug)->where('is_active', true))
            ->whereHas('role', fn($q) => $q->where('name', $roleName))
            ->exists();
    }

    /**
     * Valida si el usuario tiene permiso activo para entrar a una aplicación (Slug)
     */
    public function canAccessApplication($applicationSlug)
    {
        if ($this->hasAnyRole(['Admin', 'Super-Admin'])) return true;

        return $this->applicationAccess()
            ->where('is_active', true)
            ->whereHas('application', fn($q) => $q->where('slug', $applicationSlug)->where('is_active', true))
            ->exists();
    }

    public function solicitudesAsignadas()
    {
        return $this->hasMany(SolicitudAfiliacion::class, 'asignado_user_id');
    }

    public function solicitudesCreadas()
    {
        return $this->hasMany(SolicitudAfiliacion::class, 'solicitante_user_id');
    }

    public function gestionesCallCenter()
    {
        return $this->hasMany(CallCenterGestion::class, 'operador_id');
    }

    public function promocionesCallCenter()
    {
        return $this->hasMany(CallCenterBandejaSalida::class, 'enviado_por');
    }

    /**
     * Personalización del mapeo para Firebase (Excluir campos sensibles)
     */
    public function getFirebaseMapping(): array
    {
        $fields = $this->getFillable();
        // Excluimos explícitamente password y remember_token
        $excluded = ['password', 'remember_token'];
        
        $syncFields = array_diff($fields, $excluded);
        
        return array_combine($syncFields, $syncFields);
    }
}
