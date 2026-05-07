<?php

namespace App\Enums;

enum SlaStatus: string
{
    case PENDIENTE = 'pendiente';
    case EN_TIEMPO = 'en_tiempo';
    case ALERTA = 'alerta';
    case CRITICO = 'critico';
    case COMPLETADO = 'completado';

    public function color(): string
    {
        return match($this) {
            self::PENDIENTE => 'slate',
            self::EN_TIEMPO => 'emerald',
            self::ALERTA => 'amber',
            self::CRITICO => 'rose',
            self::COMPLETADO => 'blue',
        };
    }

    public function label(): string
    {
        return match($this) {
            self::PENDIENTE => 'Pendiente',
            self::EN_TIEMPO => 'En Tiempo',
            self::ALERTA => 'En Alerta',
            self::CRITICO => 'SLA Vencido',
            self::COMPLETADO => 'Completado',
        };
    }
}
