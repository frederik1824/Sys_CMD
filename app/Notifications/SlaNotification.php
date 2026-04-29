<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SlaNotification extends Notification
{
    use Queueable;

    protected $count;
    protected $corteName;

    public function __construct($count, $corteName = 'General')
    {
        $this->count = $count;
        $this->corteName = $corteName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Alerta de SLA Crítico',
            'message' => "Hay {$this->count} afiliados en el corte '{$this->corteName}' que han superado los 20 días de gestión.",
            'url' => route('afiliados.index', ['status_sla' => 'critico']),
            'type' => 'warning',
            'icon' => 'warning'
        ];
    }
}
