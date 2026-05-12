<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\FirebaseSyncable;

class NotaAfiliado extends Model
{
    use FirebaseSyncable;
    protected $table = 'notas_afiliados';
    protected $fillable = ['afiliado_id', 'user_id', 'contenido'];

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeEntregadoProveedor($query)
    {
        return $query->whereNotNull('fecha_entrega_proveedor');
    }

    public function getFirebaseCollection(): string
    {
        return 'afiliados_notas';
    }

    public function getFirebaseDocumentId(): string
    {
        return $this->afiliado?->cedula . '_' . $this->id;
    }

    public function getFirebaseMapping(): array
    {
        return [
            'contenido' => 'text',
            'created_at' => 'timestamp'
        ];
    }
}
