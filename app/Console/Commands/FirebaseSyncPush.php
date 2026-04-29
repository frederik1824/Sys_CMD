<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\User;
use App\Models\Empresa;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FirebaseSyncPush extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'firebase:push {--companies : Push only companies} {--affiliates : Push only affiliates} {--all : Sync everything}';

    /**
     * The console command description.
     */
    protected $description = 'Pushes local data to Firebase Firestore (Selective Push).';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseSyncService $firebase)
    {
        $all = !$this->option('companies') && !$this->option('affiliates');

        $this->info("🚀 Starting Firebase Cloud Sync (PUSH Selectivo)...");

        // 🛡️ SYNC ROLES & USERS (Always if --all or specific logic, but let's stick to companies/affiliates for user request)
        if ($all) {
            $this->syncCommon($firebase);
        }

        // 🏢 SYNC COMPANIES
        if ($this->option('companies') || $all) {
            $this->syncCompanies($firebase);
        }

        // 📝 SYNC AFILIADOS
        if ($this->option('affiliates') || $all) {
            $this->syncAffiliates($firebase);
        }

        $this->info("✅ Firebase Cloud PUSH completed!");
        return 0;
    }

    protected function syncCommon($firebase)
    {
        $this->info("--- Roles & Users ---");
        // Simplified Roles/Users push for brevity in this refactor
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $firebase->push('usuarios', (string)$user->id, [
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->toArray()
            ]);
        }
    }

    protected function syncCompanies($firebase)
    {
        $this->info("--- Companies ---");
        $companies = \App\Models\Empresa::all();
        foreach ($companies as $emp) {
            $this->comment("Pushing company: {$emp->nombre}");
            $firebase->push('empresas', (string)$emp->uuid, [
                'uuid' => $emp->uuid,
                'nombre' => $emp->nombre,
                'rnc' => $emp->rnc,
                'es_verificada' => (bool)$emp->es_verificada,
                'provincia_id' => $emp->provincia_id,
                'municipio_id' => $emp->municipio_id,
            ]);
        }
    }

    protected function syncAffiliates($firebase)
    {
        $this->info("--- Afiliados ---");
        $total = \App\Models\Afiliado::count();
        $this->info("Syncing {$total} affiliates...");
        $bar = $this->output->createProgressBar($total);

        \App\Models\Afiliado::chunk(100, function ($afiliados) use ($firebase, $bar) {
            foreach ($afiliados as $af) {
                $firebase->push('afiliados', (string)$af->cedula, [
                    'uuid' => $af->uuid,
                    'cedula' => $af->cedula,
                    'nombre_completo' => $af->nombre_completo,
                    'empresa_id' => $af->empresa_id,
                    'estado_id' => $af->estado_id,
                    'responsable_id' => $af->responsable_id,
                    'corte_id' => $af->corte_id,
                ]);
                $bar->advance();
            }
        });
        $bar->finish();
        $this->newLine();
    }
}
