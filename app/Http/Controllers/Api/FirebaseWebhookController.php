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
        // 1. Verificación de Seguridad Reforzada
        $secret = $request->header('X-Firebase-Secret');
        $token = $request->bearerToken();
        
        // Validamos que venga el secret y que coincida con la config
        if (!$secret || $secret !== config('services.firebase.webhook_secret')) {
            Log::warning('Intento de Webhook de Firebase BLOQUEADO: Secret inválido o ausente.', [
                'ip' => $request->ip(),
                'ua' => $request->userAgent()
            ]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 2. Validación de Payload
        $request->validate([
            'type' => 'required|in:afiliado,empresa',
            'id'   => 'required|string|max:100',
            'uuid' => 'nullable|uuid'
        ]);

        $type = $request->input('type'); 
        $uuid = $request->input('uuid');
        $id   = $request->input('id');   

        Log::info("Webhook validado para {$type}: {$id}");

        try {
            if ($type === 'afiliado') {
                // Buscamos con prioridad: UUID -> Cédula -> ID
                $item = Afiliado::where(function($q) use ($uuid, $id) {
                    if ($uuid) $q->where('uuid', $uuid);
                    $q->orWhere('cedula', $id)->orWhere('id', $id);
                })->first();

                if (!$item) {
                    // Solo creamos si el ID parece una cédula válida (seguridad extra)
                    if (preg_match('/^[0-9-]{11,15}$/', $id)) {
                        $item = new Afiliado();
                        $item->cedula = $id;
                        $item->save();
                        Log::info("Webhook: Creado nuevo registro local para {$id}");
                    } else {
                        throw new \Exception("ID de afiliado inválido para creación automática: {$id}");
                    }
                }
                
                $item->pullFromFirebase();
                return response()->json(['success' => true, 'message' => 'Sincronización exitosa']);
                
            } elseif ($type === 'empresa') {
                $item = Empresa::where(function($q) use ($uuid, $id) {
                    if ($uuid) $q->where('uuid', $uuid);
                    $q->orWhere('id', $id)->orWhere('rnc', $id);
                })->first();

                if (!$item) {
                    $item = new Empresa();
                    $item->nombre = "Pendiente Sincronización ($id)";
                    $item->save();
                }
                
                $item->pullFromFirebase();
                return response()->json(['success' => true, 'message' => 'Empresa sincronizada']);
            }

            return response()->json(['success' => false, 'message' => 'Tipo no soportado'], 400);

        } catch (\Exception $e) {
            Log::error("Error Crítico en Webhook: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'error' => 'Error interno de procesamiento'], 500);
        }
    }
}
