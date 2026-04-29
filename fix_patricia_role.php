<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$u = User::where('email', 'pgeronimo@arscmd.com.do')->first();
if ($u) {
    echo "Found user: " . $u->name . "\n";
    echo "Current roles: " . implode(', ', $u->getRoleNames()->toArray()) . "\n";
    
    // Cambiar a Operador
    $u->syncRoles(['Operador']);
    
    echo "New roles: " . implode(', ', $u->getRoleNames()->toArray()) . "\n";
} else {
    echo "User not found.\n";
}
