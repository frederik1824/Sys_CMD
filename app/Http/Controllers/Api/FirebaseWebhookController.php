<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Afiliado;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FirebaseWebhookController extends Controller
{
    /**
     * Recibe notificaciones de cambios en tiempo real desde Firebase
     */
    public function handle(Request $request)
    {
        // 1. Verificación de Seguridad
        $secret = $request->header('X-Firebase-Secret');
        if ($secret !== config('services.firebase.webhook_secret')) {
            Log::warning('Intento de Webhook de Firebase fallido: Secret inválido.');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $type = $request->input('type'); // 'afiliado' o 'empresa'
        $uuid = $request->input('uuid');
        $id   = $request->input('id');   // ID de documento en Firebase

        Log::info("Webhook recibido para {$type}: {$id}");

        try {
            if ($type === 'afiliado') {
                $item = Afiliado::where('id', $id)->orWhere('uuid', $uuid)->orWhere('cedula', $id)->first();
                if (!$item) {
                    // Si no existe, lo creamos para cumplir con el "Firebase-First"
                    $item = new Afiliado();
                    $item->cedula = $id;
                    $item->save();
                    Log::info("Webhook: Creado nuevo afiliado localmente para concordar con Firebase: {$id}");
                }
                
                $item->pullFromFirebase();
                return response()->json(['success' => true, 'message' => 'Afiliado sincronizado (Pull forced)']);
                
            } elseif ($type === 'empresa') {
                $item = Empresa::where('id', $id)->orWhere('uuid', $uuid)->first();
                if (!$item) {
                    $item = new Empresa();
                    $item->nombre = "Importada desde Firebase ($id)"; // Placeholder que se llenará con el pull
                    $item->save();
                }
                
                $item->pullFromFirebase();
                return response()->json(['success' => true, 'message' => 'Empresa sincronizada']);
            }

            return response()->json(['success' => false, 'message' => 'No se encontró el registro local'], 404);

        } catch (\Exception $e) {
            Log::error("Error en Webhook de Firebase: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
