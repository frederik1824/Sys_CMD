<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ResponsabilidadAsignada extends Notification
{
    use Queueable;

    protected $responsable;
    protected $provinciasCount;

    public function __construct($responsable, $provinciasCount = 0)
    {
        $this->responsable = $responsable;
        $this->provinciasCount = $provinciasCount;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $provText = $this->provinciasCount > 1 ? "{$this->provinciasCount} provincias" : "{$this->provinciasCount} provincia";
        return [
            'title' => 'Gestión Territorial Asignada',
            'message' => "Se te ha delegado la gestión operativa de {$provText}. Revisa tu Dashboard para ver los registros aislados correspondientes.",
            'url' => route('dashboard'),
            'type' => 'info',
            'icon' => 'public'
        ];
    }
}
