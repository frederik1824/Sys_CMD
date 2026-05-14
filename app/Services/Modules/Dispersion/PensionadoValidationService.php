<?php

namespace App\Services\Modules\Dispersion;

use App\Models\Modules\Dispersion\DispersionTitular;
use App\Models\Modules\Dispersion\DispersionDependiente;
use App\Models\SolicitudAfiliacion;
use App\Models\Modules\Dispersion\PensionadoMaster;
use Carbon\Carbon;

class PensionadoValidationService
{
    /**
     * Checks if there is a payment match for a given ID in the dispersion data.
     */
    public function checkPaymentMatch(string $identificador)
    {
        $cleanId = preg_replace('/[^0-9]/', '', $identificador);
        
        // Search in Titulares (Cleaning both search and DB column)
        $titularMatch = DispersionTitular::whereRaw("REPLACE(cedula, '-', '') = ?", [$cleanId])
            ->orWhereRaw("REPLACE(nss, '-', '') = ?", [$cleanId])
            ->first();

        if ($titularMatch) return ['type' => 'Titular', 'data' => $titularMatch];

        // Search in Dependientes
        $dependienteMatch = DispersionDependiente::whereRaw("REPLACE(cedula_dependiente, '-', '') = ?", [$cleanId])
            ->orWhereRaw("REPLACE(nss_dependiente, '-', '') = ?", [$cleanId])
            ->first();

        if ($dependienteMatch) return ['type' => 'Dependiente', 'data' => $dependienteMatch];

        return null;
    }

    /**
     * Scans all processed dispersion data and updates the Master Portfolio.
     */
    public function syncMasterWithDispersion($cargaId)
    {
        $titulares = DispersionTitular::where('carga_id', $cargaId)->get();
        $dependientes = DispersionDependiente::where('carga_id', $cargaId)->get();
        
        foreach ($titulares as $t) {
            $this->updateOrCreateMaster($t->cedula, $t->nss, $t->periodo, 'Titular', $t->origen_pension);
        }

        foreach ($dependientes as $d) {
            $this->updateOrCreateMaster($d->cedula_dependiente, $d->nss_dependiente, $d->periodo, 'Dependiente', $d->origen_pension);
        }
    }

    protected function updateOrCreateMaster($cedula, $nss, $periodo, $tipo, $institucion)
    {
        $cleanId = preg_replace('/[^0-9]/', '', $cedula);
        
        $pensionado = PensionadoMaster::whereRaw("REPLACE(cedula, '-', '') = ?", [$cleanId])
            ->orWhereRaw("REPLACE(nss, '-', '') = ?", [$cleanId])
            ->first();

        if ($pensionado) {
            $pensionado->update([
                'ultimo_pago_confirmado_at' => now(),
                'estado_sistema' => 'ACTIVO'
            ]);
        } else {
            // Si no existe, lo creamos (esto puede pasar por solicitudes aprobadas)
            $solicitud = SolicitudAfiliacion::whereRaw("REPLACE(cedula, '-', '') = ?", [$cleanId])->first();
            
            PensionadoMaster::create([
                'cedula' => $cedula,
                'nss' => $nss,
                'solicitud_id' => $solicitud?->id,
                'nombre_completo' => $solicitud?->nombre_completo ?? 'Afiliado Detectado',
                'tipo_pension' => $tipo,
                'institucion_pension' => $institucion,
                'ultimo_pago_confirmado_at' => now(),
                'estado_sistema' => 'ACTIVO',
                'data_adicional' => [
                    'periodo_inicial' => $periodo,
                    'telefono' => $solicitud?->telefono
                ]
            ]);
        }
    }

    /**
     * Scans pending affiliation requests and promotes them if a payment match is found.
     */
    public function validatePendingRequests()
    {
        $pendingRequests = SolicitudAfiliacion::where('estado', 'Pendiente')->get();
        $promotedCount = 0;

        foreach ($pendingRequests as $request) {
            $match = $this->checkPaymentMatch($request->cedula);
            
            if ($match) {
                $request->update([
                    'estado' => 'Aprobada',
                    'pago_confirmado_at' => now(),
                    'observacion_interna' => ($request->observacion_interna ? $request->observacion_interna . "\n" : "") . 
                                           "Pago TSS Confirmado automáticamente vía Dispersión el " . now()->format('d/m/Y H:i')
                ]);

                // Promotion to Master Portfolio (handled later in syncMasterWithDispersion or here)
                $this->updateOrCreateMaster(
                    $request->cedula, 
                    null, 
                    $match['data']->periodo, 
                    $match['type'], 
                    $match['data']->origen_pension
                );

                $promotedCount++;
            }
        }

        return $promotedCount;
    }
}
