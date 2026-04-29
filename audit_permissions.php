<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;

foreach (Role::all() as $role) {
    echo "Role: " . $role->name . "\n";
    echo "Permissions: " . implode(', ', $role->permissions->pluck('name')->toArray()) . "\n\n";
}
