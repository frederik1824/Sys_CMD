<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provincia;
use App\Models\Municipio;

class GeograficaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'SANTIAGO' => ['SANTIAGO DE LOS CABALLEROS', 'TAMBORIL', 'LICEY AL MEDIO', 'VILLA GONZALEZ', 'PUÑAL', 'SAN JOSE DE LAS MATAS'],
            'SANTO DOMINGO' => ['SANTO DOMINGO ESTE', 'SANTO DOMINGO OESTE', 'SANTO DOMINGO NORTE', 'BOCA CHICA', 'LOS ALCARRIZOS'],
            'DISTRITO NACIONAL' => ['SANTO DOMINGO'],
            'LA ROMANA' => ['LA ROMANA', 'GUAYMATE', 'VILLA HERMOSA'],
            'LA ALTAGRACIA' => ['HIGUEY', 'PUNTA CANA', 'BAVARO', 'SAN RAFAEL DEL YUMA'],
            'BARAHONA' => ['BARAHONA', 'CABRAL', 'ENRIQUILLO', 'VICENTE NOBLE'],
            'PUERTO PLATA' => ['PUERTO PLATA', 'SOSUA', 'CABARETE', 'IMBERT'],
            'SAN CRISTOBAL' => ['SAN CRISTOBAL', 'HAINA', 'YAGUATE', 'VILLA ALTAGRACIA'],
            'DUARTE' => ['SAN FRANCISCO DE MACORIS', 'ARENOSO', 'CASTILLO', 'PIMENTEL'],
            'LA VEGA' => ['LA VEGA', 'JARABACOA', 'CONSTANZA', 'JIMA ABAJO'],
            'SAN PEDRO DE MACORIS' => ['SAN PEDRO DE MACORIS', 'CONSUELO', 'QUISQUEYA'],
        ];

        foreach ($data as $provinciaNombre => $municipios) {
            $provincia = Provincia::firstOrCreate(['nombre' => $provinciaNombre]);
            foreach ($municipios as $muniNombre) {
                Municipio::firstOrCreate([
                    'provincia_id' => $provincia->id,
                    'nombre' => $muniNombre
                ]);
            }
        }
    }
}
