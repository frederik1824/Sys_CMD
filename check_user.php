<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('email', 'admin@arscmd.com')->first();
if ($user) {
    echo "User: " . $user->name . "\n";
    echo "Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
    try {
        echo "Has Access to access_admin_panel: " . ($user->hasPermissionTo('access_admin_panel') ? 'YES' : 'NO') . "\n";
    } catch (\Exception $e) {
        echo "Permission error: " . $e->getMessage() . "\n";
    }
} else {
    echo "User not found\n";
}
