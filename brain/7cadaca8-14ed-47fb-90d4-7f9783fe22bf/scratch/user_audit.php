<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- AUDITORÍA DE USUARIOS Y ROLES ---\n";
User::all()->each(function($u) {
    $roles = $u->getRoleNames()->implode(', ');
    echo "ID: {$u->id} - Nombre: {$u->name} - Responsable: {$u->responsable_id} - Roles: [{$roles}]\n";
});

echo "\n--- AUDITORÍA DE RESPONSABLES ---\n";
DB::table('responsables')->get()->each(function($r) {
    echo "ID: {$r->id} - Nombre: {$r->nombre}\n";
});
