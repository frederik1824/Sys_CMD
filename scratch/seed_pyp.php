<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\Afiliado;
use App\Models\PypExpediente;
use App\Models\PypPrograma;
use App\Models\PypSeguimiento;
use App\Models\User;
use Illuminate\Support\Str;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$afiliado = Afiliado::first();
$user = User::first();

if (!$afiliado) {
    echo "No hay afiliados en la base de datos.";
    exit;
}

// 1. Crear Expediente
$exp = PypExpediente::updateOrCreate(
    ['afiliado_id' => $afiliado->id],
    [
        'uuid' => (string) Str::uuid(),
        'riesgo_score' => 9.5,
        'riesgo_nivel' => 'Alto',
        'estado_clinico' => 'Descompensado',
        'enfermedades_json' => ['Diabetes', 'Hipertensión'],
        'ultimo_seguimiento_at' => now()->subDays(10), // Para que aparezca en alertas
        'asignado_a' => $user->id ?? null
    ]
);

// 2. Vincular a Programas
$diab = PypPrograma::where('slug', 'diabetes')->first();
$hpta = PypPrograma::where('slug', 'hipertension')->first();

if ($diab) $exp->programas()->syncWithoutDetaching([$diab->id => ['fecha_inscripcion' => now()]]);
if ($hpta) $exp->programas()->syncWithoutDetaching([$hpta->id => ['fecha_inscripcion' => now()]]);

// 3. Crear un Seguimiento
PypSeguimiento::create([
    'uuid' => (string) Str::uuid(),
    'expediente_id' => $exp->id,
    'user_id' => $user->id ?? 1,
    'tipo_contacto' => 'Llamada',
    'resultado' => 'No contestó',
    'comentarios' => 'Se intentó contactar para cita de control.',
    'proximo_contacto_at' => now()->addDays(1)
]);

echo "Expediente PyP Creado para: " . $afiliado->nombre_completo . "\n";
echo "Riesgo: " . $exp->riesgo_nivel . "\n";
