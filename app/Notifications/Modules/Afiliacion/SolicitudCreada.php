<?php

namespace App\Notifications\Modules\Afiliacion;

use App\Models\SolicitudAfiliacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SolicitudCreada extends Notification
{
    use Queueable;

    protected $solicitud;

    /**
     * Create a new notification instance.
     */
    public function __construct(SolicitudAfiliacion $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'solicitud_id' => $this->solicitud->id,
            'codigo' => $this->solicitud->codigo_solicitud,
            'title' => 'Nueva Solicitud Recibida',
            'message' => "Se ha registrado la solicitud {$this->solicitud->codigo_solicitud} de {$this->solicitud->nombre_completo}.",
            'tipo' => 'afiliacion',
            'icon' => 'ph-file-plus',
            'color' => 'indigo',
            'url' => route('afiliacion.show', $this->solicitud->id),
        ];
    }
}
