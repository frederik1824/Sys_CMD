<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\FirebaseSyncable;

class Proveedor extends Model
{
    use FirebaseSyncable;

    /**
     * Define the document ID for Firebase (ID)
     */
    public function getFirebaseDocumentId(): string
    {
        return (string)$this->id;
    }
    use HasFactory;

    protected $fillable = [
        'nombre',
        'precio_base',
        'activo'
    ];
}
