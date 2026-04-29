<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Afiliado;
use App\Services\FirebaseSyncService;

class NormalizeCedulas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'afiliados:normalize-cedulas {--dry-run : Only show what would be changed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Normalize all affiliate ID cards (Cédulas) to XXX-XXXXXXX-X format and sync with Firebase.';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseSyncService $firebase)
    {
        $this->info("Scanning affiliates for normalization...");

        // Usamos eager loading para evitar errores de Lazy Loading durante la sincronización a Firebase
        $query = Afiliado::with(['corte', 'responsable', 'estado', 'empresaModel', 'proveedor']);
        
        $total = $query->count();
        $count = 0;
        $duplicates = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(100, function ($afiliados) use ($firebase, $bar, &$count, &$duplicates) {
            foreach ($afiliados as $afiliado) {
                // Obtenemos el valor crudo en DB para comparar
                $oldCedula = $afiliado->getRawOriginal('cedula');
                
                // Si ya tiene el formato correcto, saltar
                if (preg_match('/^\d{3}-\d{7}-\d{1}$/', $oldCedula)) {
                    $bar->advance();
                    continue;
                }

                $newCedula = Afiliado::formatCedula($oldCedula);

                if ($oldCedula === $newCedula) {
                    $bar->advance();
                    continue;
                }

                if ($this->option('dry-run')) {
                    $this->line("\nWould change: {$oldCedula} -> {$newCedula}");
                    $bar->advance();
                    continue;
                }

                // Verificar Colisión Voluntaria (Ya existe uno normalizado con esa ID en el mismo corte)
                $existing = Afiliado::where('cedula', $newCedula)
                    ->where('corte_id', $afiliado->corte_id)
                    ->where('id', '!=', $afiliado->id)
                    ->first();

                try {
                    if ($existing) {
                        // MERGE / DEDUPLICATION LOGIC
                        // Si el existente es más reciente o tiene estado avanzado, borramos el viejo.
                        // En este caso, simplemente eliminamos el registro "sucio" local y de Firebase.
                        $this->line("\nCollision detected: {$oldCedula} matches existing {$newCedula} (Corte {$afiliado->corte_id}). Deleting duplicate record.");
                        $firebase->deleteDocument('afiliados', $oldCedula);
                        $afiliado->delete(); // Soft delete o force delete según modelo
                        $duplicates++;
                    } else {
                        // REGULAR NORMALIZATION
                        // 1. Actualizar localmente (ID cambia)
                        $afiliado->cedula = $newCedula;
                        $afiliado->save(); // El observer hará el PUSH con la nueva ID

                        // 2. Eliminar el documento viejo de Firebase
                        $firebase->deleteDocument('afiliados', $oldCedula);
                        $count++;
                    }
                } catch (\Exception $e) {
                    $this->error("\nError processing {$oldCedula}: " . $e->getMessage());
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->info("\n\nNormalization complete!");
        $this->info("- {$count} records normalized.");
        $this->info("- {$duplicates} duplicate records merged/removed.");

        return 0;
    }
}
