<?php

use App\Models\Empresa;
use App\Models\Afiliado;
use App\Models\Provincia;
use App\Models\Municipio;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$santiagoId = Provincia::where('nombre', 'SANTIAGO')->value('id');
if (!$santiagoId) {
    die("Error: No se encontró la provincia SANTIAGO en el catálogo.\n");
}

echo "Corrigiendo datos para SANTIAGO (ID: $santiagoId)...\n";

// 1. Corregir Empresas que tienen SANTIAGO en su nombre
$empresasSantiaguinas = Empresa::where('nombre', 'like', '%SANTIAGO%')
    ->where(function($q) use ($santiagoId) {
        $q->whereNull('provincia_id')
          ->orWhere('provincia_id', '!=', $santiagoId);
    })
    ->get();

echo "Encontradas " . $empresasSantiaguinas->count() . " empresas con 'SANTIAGO' en su nombre pero mal asignadas.\n";

foreach($empresasSantiaguinas as $empresa) {
    echo " -> Actualizando: " . $empresa->nombre . " (Antes: ID " . ($empresa->provincia_id ?? 'NULL') . ")\n";
    $empresa->provincia_id = $santiagoId;
    
    // Si no tiene municipio, intentamos poner el municipio central de Santiago
    if (!$empresa->municipio_id) {
        $muni = Municipio::where('provincia_id', $santiagoId)
            ->where('nombre', 'like', '%SANTIAGO%')
            ->first();
        if ($muni) {
            $empresa->municipio_id = $muni->id;
        }
    }
    
    $empresa->save();
}

// 2. Corregir Afiliados que tengan SANTIAGO en sus campos legacy
// Pero OJO: solo si el afiliado no tiene una empresa asignada que ya esté correcta.
$afiliadosSantiaguinos = Afiliado::where(function($q) {
    $q->where('provincia', 'like', '%SANTIAGO%')
      ->orWhere('municipio', 'like', '%SANTIAGO%');
})
->where(function($q) use ($santiagoId) {
    $q->whereNull('provincia_id')
      ->orWhere('provincia_id', '!=', $santiagoId);
})
->get();

echo "Encontrados " . $afiliadosSantiaguinos->count() . " afiliados con 'SANTIAGO' en texto pero mal asignados.\n";

foreach($afiliadosSantiaguinos as $afiliado) {
    $afiliado->provincia_id = $santiagoId;
    
    if (!$afiliado->municipio_id) {
        $muni = Municipio::where('provincia_id', $santiagoId)
            ->where('nombre', 'like', strtoupper($afiliado->municipio ?: 'SANTIAGO'))
            ->first();
        if ($muni) {
            $afiliado->municipio_id = $muni->id;
        }
    }
    $afiliado->save();
}

echo "Saneamiento completado.\n";
