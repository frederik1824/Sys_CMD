<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Provincia;
use App\Models\Municipio;
use App\Models\Corte;
use App\Models\Estado;
use App\Models\Responsable;
use App\Services\FirebaseSyncService;

class FirebaseSyncCatalogs extends Command
{
    protected $signature = 'firebase:sync-catalogs';
    protected $description = 'Sincroniza solo los catálogos (Provincias, Municipios, Cortes, etc) con Firebase';

    public function handle(FirebaseSyncService $syncService)
    {
        $this->info('🚀 Sincronizando Catálogos con Cloud Firestore...');

        $this->sync($syncService, 'provincias', Provincia::class);
        $this->sync($syncService, 'municipios', Municipio::class);
        $this->sync($syncService, 'cortes', Corte::class);
        $this->sync($syncService, 'estados', Estado::class);
        $this->sync($syncService, 'responsables', Responsable::class);

        $this->info('✅ Catálogos sincronizados exitosamente.');
    }

    private function sync($syncService, $collection, $modelClass)
    {
        $this->info("- Sincronizando {$collection}...");
        $items = $modelClass::all();
        $bar = $this->output->createProgressBar(count($items));
        
        foreach ($items as $item) {
            $syncService->syncModel($item, $collection, (string)$item->id);
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
    }
}
