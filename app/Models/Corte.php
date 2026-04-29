<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\FirebaseSyncable;

class Corte extends Model
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
    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'activo'];

    public function afiliados()
    {
        return $this->hasMany(Afiliado::class);
    }
}
