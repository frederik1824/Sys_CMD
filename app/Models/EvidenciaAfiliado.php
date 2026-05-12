<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\FirebaseSyncable;

class EvidenciaAfiliado extends Model
{
    use FirebaseSyncable;
    protected $fillable = [
        'afiliado_id', 'tipo_documento', 'status', 'file_path', 'user_id', 'validated_by', 'observaciones'
    ];

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validador()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function getFirebaseCollection(): string
    {
        return 'afiliados_evidencias';
    }

    public function getFirebaseDocumentId(): string
    {
        return ($this->afiliado?->cedula ?? $this->afiliado_id) . '_' . ($this->tipo_documento ?? 'doc');
    }

    public function getFirebaseMapping(): array
    {
        return [
            'tipo_documento' => 'type',
            'status' => 'status',
            'file_path' => 'file_url',
            'observaciones' => 'notes'
        ];
    }
}
