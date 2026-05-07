<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = 'flopez@ars.cmd';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "ERROR: User $email not found in database.\n";
    // Check first user as fallback
    $user = User::first();
    echo "Checking first user instead: " . $user->email . "\n";
}

echo "Diagnostic for User: " . $user->name . " (" . $user->email . ")\n";
echo "Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";

// Ensure Admin role has access
$adminRole = Role::where('name', 'Admin')->first();
if ($adminRole && !$user->hasRole('Admin')) {
    echo "ACTION: Assigning Admin role to " . $user->email . "\n";
    $user->assignRole('Admin');
}

echo "Final Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
