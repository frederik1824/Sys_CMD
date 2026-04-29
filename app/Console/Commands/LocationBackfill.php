<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Afiliado;
use App\Models\Empresa;
use App\Models\Provincia;
use App\Models\Municipio;
use Illuminate\Support\Facades\DB;

class LocationBackfill extends Command
{
    protected $signature = 'locations:backfill';
    protected $description = 'Migra datos de ubicación de texto libre a IDs normalizados';

    public function handle()
    {
        $this->info('Iniciando Backfill de Ubicaciones...');

        // 1. Mapear Provincias y Municipios en Afiliados
        $this->info('Procesando Afiliados...');
        Afiliado::chunk(200, function ($afiliados) {
            foreach ($afiliados as $afiliado) {
                $changed = false;

                // Mapear Provincia
                if (!$afiliado->provincia_id && $afiliado->provincia) {
                    $prov = $this->findProvincia($afiliado->provincia);
                    if ($prov) {
                        $afiliado->provincia_id = $prov->id;
                        $changed = true;
                    }
                }

                // Mapear Municipio
                if (!$afiliado->municipio_id && $afiliado->municipio) {
                    $muni = $this->findMunicipio($afiliado->municipio, $afiliado->provincia_id);
                    if ($muni) {
                        $afiliado->municipio_id = $muni->id;
                        $changed = true;
                    }
                }

                // Vincular Empresa por nombre si empresa_id es nulo
                if (!$afiliado->empresa_id && $afiliado->empresa) {
                    $empresa = Empresa::where('nombre', $afiliado->empresa)->first();
                    if ($empresa) {
                        $afiliado->empresa_id = $empresa->id;
                        $changed = true;
                    }
                }

                if ($changed) {
                    $afiliado->save();
                }
            }
        });

        // 2. Mapear Ubicaciones en Empresas
        $this->info('Procesando Empresas...');
        foreach (Empresa::all() as $empresa) {
            $changed = false;
            
            if (!$empresa->provincia_id && $empresa->provincia) {
                $prov = $this->findProvincia($empresa->provincia);
                if ($prov) {
                    $empresa->provincia_id = $prov->id;
                    $changed = true;
                }
            }

            if (!$empresa->municipio_id && $empresa->municipio) {
                $muni = $this->findMunicipio($empresa->municipio, $empresa->provincia_id);
                if ($muni) {
                    $empresa->municipio_id = $muni->id;
                    $changed = true;
                }
            }

            if ($changed) {
                $empresa->save();
            }
        }

        // 3. Aplicar Herencia (Regla de negocio: Afiliado hereda de Empresa si no tiene ubicación propia)
        $this->info('Aplicando herencia de ubicaciones (Empresa -> Afiliado)...');
        DB::table('afiliados')
            ->join('empresas', 'afiliados.empresa_id', '=', 'empresas.id')
            ->whereNull('afiliados.provincia_id')
            ->update([
                'afiliados.provincia_id' => DB::raw('empresas.provincia_id'),
                'afiliados.municipio_id' => DB::raw('empresas.municipio_id')
            ]);

        $this->info('Backfill completado con éxito.');
    }

    private function findProvincia($name)
    {
        $name = strtoupper(trim($name));
        if (empty($name) || in_array($name, ['#N/D', 'N/A', 'NULL'])) return null;

        return Provincia::where('nombre', $name)
            ->orWhere('nombre', 'like', "%{$name}%")
            ->first();
    }

    private function findMunicipio($name, $provinciaId = null)
    {
        $name = strtoupper(trim($name));
        if (empty($name) || in_array($name, ['#N/D', 'N/A', 'NULL'])) return null;

        $query = Municipio::where('nombre', $name)
            ->orWhere('nombre', 'like', "%{$name}%");

        if ($provinciaId) {
            $query->where('provincia_id', $provinciaId);
        }

        return $query->first();
    }
}
