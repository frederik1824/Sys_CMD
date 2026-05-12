<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Auditable;

class PypSeguimiento extends Model
{
    protected $table = 'pyp_seguimientos';
    use SoftDeletes, HasUuids, Auditable;

    protected $fillable = [
        'uuid', 'expediente_id', 'user_id', 'tipo_contacto', 
        'resultado', 'comentarios', 'proximo_contacto_at'
    ];

    protected $casts = [
        'proximo_contacto_at' => 'datetime'
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function expediente()
    {
        return $this->belongsTo(PypExpediente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
