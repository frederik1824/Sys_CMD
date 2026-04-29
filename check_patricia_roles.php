<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$patricia = User::where('name', 'LIKE', '%Patricia%')->orWhere('email', 'LIKE', '%Patricia%')->first();

if (!$patricia) {
    echo "User Patricia not found.\n";
    exit;
}

echo "User: " . $patricia->name . " (ID: " . $patricia->id . ")\n";
echo "Spatie Roles: " . implode(', ', $patricia->getRoleNames()->toArray()) . "\n";
echo "Spatie Direct Permissions: " . implode(', ', $patricia->getPermissionNames()->toArray()) . "\n";
echo "Legacy Rol ID: " . $patricia->rol_id . " (Name: " . ($patricia->rol?->nombre ?? 'N/A') . ")\n";

echo "\n--- Global Roles ---\n";
foreach (Role::all() as $role) {
    echo "Role: " . $role->name . " (Permissions: " . implode(', ', $role->permissions->pluck('name')->toArray()) . ")\n";
}
