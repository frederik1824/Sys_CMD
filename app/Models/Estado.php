<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\FirebaseSyncable;

class Estado extends Model
{
    use FirebaseSyncable;

    /**
     * Define the document ID for Firebase (ID)
     */
    public function getFirebaseDocumentId(): string
    {
        return (string)$this->id;
    }
    protected $fillable = ['nombre', 'es_final'];

    public function afiliados()
    {
        return $this->hasMany(Afiliado::class);
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class, 'estado_nuevo_id');
    }
}
