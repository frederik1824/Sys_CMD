<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateEmpresasData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carnet:migrate-empresas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra los datos (nombres y rnc) de empresas desde afiliados a la tabla empresas';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migración de datos de empresas...');

        $afiliados = \App\Models\Afiliado::all();
        $count = 0;

        foreach ($afiliados as $afiliado) {
            $empresaNombre = trim($afiliado->empresa);
            if(empty($empresaNombre)) {
                $empresaNombre = 'SIN EMPRESA';
            }
            $empresaRnc = trim($afiliado->rnc_empresa);

            // Buscar si ya existe la empresa por nombre, o crearla
            $empresa = \App\Models\Empresa::firstOrCreate(
                ['nombre' => $empresaNombre],
                ['rnc' => empty($empresaRnc) ? null : $empresaRnc]
            );

            // Si el RNC estaba vacío pero ahora el afiliado lo tiene, actualizarlo
            if (empty($empresa->rnc) && !empty($empresaRnc)) {
                $empresa->rnc = $empresaRnc;
                $empresa->save();
            }

            if ($afiliado->empresa_id !== $empresa->id) {
                $afiliado->empresa_id = $empresa->id;
                $afiliado->save();
                $count++;
            }
        }

        $this->info("Se vincularon $count afiliados a sus respectivas empresas master.");
        $totalEmpresas = \App\Models\Empresa::count();
        $this->info("Total de empresas únicas registradas: $totalEmpresas.");
    }
}
