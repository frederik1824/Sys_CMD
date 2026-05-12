<?php

namespace App\Models\Asistencia;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'asistencia_configuracion';
    protected $fillable = ['clave', 'valor', 'descripcion'];

    /**
     * Obtiene una configuración por clave
     */
    public static function get($clave, $default = null)
    {
        $config = self::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    /**
     * Guarda o actualiza una configuración
     */
    public static function set($clave, $valor, $descripcion = null)
    {
        return self::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor, 'descripcion' => $descripcion]
        );
    }
}
