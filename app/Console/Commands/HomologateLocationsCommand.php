<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empresa;
use App\Models\Afiliado;
use App\Models\Provincia;
use App\Models\Municipio;

class HomologateLocationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:homologate-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza y homologa las provincias y municipios de las empresas basándose en los datos de los afiliados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando homologación de ubicaciones...');

        // 1. Homologar AFILIADOS primero (donde está la fuente de verdad)
        $afiliadosCount = Afiliado::where(function($q) {
                $q->whereNotNull('provincia')->orWhereNotNull('municipio');
            })
            ->where(function($q) {
                $q->whereNull('provincia_id')->orWhereNull('municipio_id');
            })->count();

        $this->info("Procesando {$afiliadosCount} afiliados...");
        $barAf = $this->output->createProgressBar($afiliadosCount);

        Afiliado::where(function($q) {
                $q->whereNotNull('provincia')->orWhereNotNull('municipio');
            })
            ->chunk(100, function ($afiliados) use ($barAf) {
                foreach ($afiliados as $afiliado) {
                    $this->processRecord($afiliado);
                    $barAf->advance();
                }
            });

        $barAf->finish();
        $this->newLine(2);

        // 2. Homologar EMPRESAS basándose en sus afiliados
        $empresasCount = Empresa::count();
        $this->info("Procesando {$empresasCount} empresas...");
        $barEm = $this->output->createProgressBar($empresasCount);

        foreach (Empresa::all() as $empresa) {
            // Si ya tiene ubicación (texto), la usamos. Si no, la tomamos de sus afiliados
            if (!$empresa->provincia || !$empresa->municipio) {
                $afiliado = $empresa->afiliados()->whereNotNull('provincia')->first();
                if ($afiliado) {
                    $empresa->provincia = $afiliado->provincia;
                    $empresa->municipio = $afiliado->municipio;
                    $empresa->save();
                }
            }

            // Ahora que tiene texto (o el que ya tenía), procesamos a ID
            $this->processRecord($empresa);
            $barEm->advance();
        }

        $barEm->finish();
        $this->newLine(2);
        $this->info('Homologación completada con éxito.');
    }

    private function processRecord($record)
    {
        $provinciaFound = null;
        $municipioFound = null;

        // Caso 1: Tiene Provincia (Texto)
        if ($record->provincia && !in_array(strtoupper($record->provincia), ['#N/D', 'N/A', ''])) {
            $provName = trim(strtoupper($record->provincia));
            $provinciaFound = Provincia::firstOrCreate(['nombre' => $provName]);
        }

        // Caso 2: Tiene Municipio (Texto)
        if ($record->municipio && !in_array(strtoupper($record->municipio), ['#N/D', 'N/A', ''])) {
            $muniName = trim(strtoupper($record->municipio));
            
            // Si ya tenemos provincia, buscamos el municipio dentro de ella o lo creamos
            if ($provinciaFound) {
                $municipioFound = Municipio::firstOrCreate([
                    'provincia_id' => $provinciaFound->id, 
                    'nombre' => $muniName
                ]);
            } else {
                // Si NO tenemos provincia, buscamos un municipio globalmente para INFERIR la provincia
                $existingMuni = Municipio::where('nombre', $muniName)->first();
                if ($existingMuni) {
                    $municipioFound = $existingMuni;
                    $provinciaFound = $existingMuni->provincia;
                    // Actualizar el texto por si acaso
                    $record->provincia = $provinciaFound->nombre;
                } else {
                    // Si el municipio es nuevo y no hay provincia, lo dejamos pendiente o creamos una "OTRAS" 
                    // (O simplemente lo creamos sin provincia si fuera posible, pero fallaría la FK si no es null)
                }
            }
        }

        // Caso 3: Solo tiene Provincia pero no municipio (ya manejado en caso 1)

        if ($provinciaFound) {
            $record->provincia_id = $provinciaFound->id;
            if ($municipioFound) {
                $record->municipio_id = $municipioFound->id;
            }
            $record->save();
        }
    }
}
