<?php

namespace App\Services\Common;

use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Genera un enlace directo a WhatsApp con un mensaje pre-cargado.
     * Útil para que el operador envíe el mensaje manualmente con un clic.
     */
    public function generateLink(string $phone, string $message): string
    {
        // Limpiar teléfono (solo números)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Asegurar formato internacional (asumimos RD +1 si no tiene)
        if (strlen($phone) === 10) {
            $phone = '1' . $phone;
        }

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    /**
     * Envía una notificación vía API (Placeholder para futura integración con Twilio/UltraMsg)
     */
    public function sendNotification(string $phone, string $message): bool
    {
        Log::info("WhatsApp Notification Queued for {$phone}: {$message}");
        
        // Aquí iría la integración con Guzzle/HttpClient para el proveedor elegido.
        
        return true; 
    }

    /**
     * Plantilla para Confirmación de Pago TSS
     */
    public function templatePaymentConfirmed($nombre, $periodo): string
    {
        return "Hola {$nombre}, ARS CMD te informa que hemos recibido la confirmación de tu pago TSS para el periodo {$periodo}. Tu solicitud de afiliación está siendo procesada.";
    }

    /**
     * Plantilla para Aprobación de Solicitud
     */
    public function templateRequestApproved($nombre, $codigo): string
    {
        return "Buenas noticias {$nombre}! Tu solicitud de afiliación {$codigo} ha sido APROBADA. Tu carnet está en proceso de emisión. ¡Bienvenido a ARS CMD!";
    }
}
