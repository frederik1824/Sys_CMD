<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\FirebaseSyncable;

class Responsable extends Model
{
    use FirebaseSyncable;

    /**
     * Define the document ID for Firebase (ID)
     */
    public function getFirebaseDocumentId(): string
    {
        return (string)$this->id;
    }
    use \App\Traits\Auditable;
    protected $fillable = ['nombre', 'descripcion', 'precio_entrega', 'activo', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function afiliados()
    {
        return $this->hasMany(Afiliado::class);
    }

    public function provincias()
    {
        return $this->belongsToMany(Provincia::class, 'provincia_responsable');
    }
}
