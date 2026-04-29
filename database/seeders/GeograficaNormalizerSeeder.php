<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provincia;
use App\Models\Municipio;
use App\Models\Afiliado;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

class GeograficaNormalizerSeeder extends Seeder
{
    public function run(): void
    {
        $officialProvinces = [
            'DISTRITO NACIONAL', 'SANTO DOMINGO', 'SANTIAGO', 'LA VEGA', 'PUERTO PLATA', 
            'DUARTE', 'SAN PEDRO DE MACORIS', 'LA ROMANA', 'SAN CRISTOBAL', 'AZUA', 
            'ESPAILLAT', 'SAN JUAN', 'PERAVIA', 'BARAHONA', 'MONSEÑOR NOUEL', 
            'SÁNCHEZ RAMÍREZ', 'MARÍA TRINIDAD SÁNCHEZ', 'MONTE PLATA', 'VALVERDE', 
            'LA ALTAGRACIA', 'SAMANÁ', 'BAHORUCO', 'HATO MAYOR', 'EL SEIBO', 
            'SAN JOSÉ DE OCOA', 'MONTE CRISTI', 'HERMANAS MIRABAL', 'SANTIAGO RODRÍGUEZ', 
            'DAJABÓN', 'INDEPENDENCIA', 'ELÍAS PIÑA', 'PEDERNALES'
        ];

        DB::beginTransaction();

        try {
            // 1. Asegurar que las 32 oficiales existan
            foreach ($officialProvinces as $name) {
                Provincia::firstOrCreate(['nombre' => $name]);
            }

            $officialIds = Provincia::whereIn('nombre', $officialProvinces)->pluck('id')->toArray();
            
            // 2. Obtener provincias "falsas" (sectores que están en la tabla provincias)
            $fakes = Provincia::whereNotIn('id', $officialIds)->get();

            // 3. Mapeo inteligente por palabras clave o listas conocidas
            $santiagoId = Provincia::where('nombre', 'SANTIAGO')->first()->id;
            $santiagoMuniId = Municipio::where('nombre', 'SANTIAGO DE LOS CABALLEROS')->where('provincia_id', $santiagoId)->first()->id;

            $spmId = Provincia::where('nombre', 'SAN PEDRO DE MACORIS')->first()->id;
            $spmMuniId = Municipio::where('nombre', 'SAN PEDRO DE MACORIS')->where('provincia_id', $spmId)->first()->id;

            $ppId = Provincia::where('nombre', 'PUERTO PLATA')->first()->id;
            $ppMuniId = Municipio::where('nombre', 'PUERTO PLATA')->where('provincia_id', $ppId)->first()->id;

            foreach ($fakes as $fake) {
                $targetProv = $santiagoId; // Default a Santiago porque el 90% de la data sucia es de ahí
                $targetMuni = $santiagoMuniId;

                // Reglas específicas para SPM y PP
                if (stripos($fake->nombre, 'SAN PEDRO') !== false || stripos($fake->nombre, 'SPM') !== false || stripos($fake->nombre, 'SOCO') !== false || stripos($fake->nombre, 'CONSUELO') !== false) {
                    $targetProv = $spmId;
                    $targetMuni = $spmMuniId;
                }
                elseif (stripos($fake->nombre, 'PUERTO PLATA') !== false || stripos($fake->nombre, 'SOSUA') !== false || stripos($fake->nombre, 'CABARETE') !== false || stripos($fake->nombre, 'LUCIANO') !== false) {
                    $targetProv = $ppId;
                    $targetMuni = $ppMuniId;
                }

                // Actualizar Afiliados de esta "provincia falsa"
                Afiliado::where('provincia_id', $fake->id)->update([
                    'provincia_id' => $targetProv,
                    'municipio_id' => $targetMuni,
                    'direccion' => DB::raw("CONCAT('{$fake->nombre}, ', COALESCE(direccion, ''))")
                ]);

                // Actualizar Empresas
                Empresa::where('provincia_id', $fake->id)->update([
                    'provincia_id' => $targetProv,
                    'municipio_id' => $targetMuni,
                    'direccion' => DB::raw("CONCAT('{$fake->nombre}, ', COALESCE(direccion, ''))")
                ]);

                // Opcional: Borrar la provincia falsa si ya no tiene referencias (no lo hacemos para seguir "no alterar existentes")
                // Sin embargo, ahora ya no saldrán en los filtros principales si filtramos por IDs oficiales.
            }

            DB::commit();
            $this->command->info('Normalización profunda completada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}
