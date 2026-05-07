<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Gate;

$user = User::where('email', 'admin@arscmd.com')->first();
if ($user) {
    echo "User: " . $user->name . "\n";
    echo "Gate check manage_system: " . (Gate::forUser($user)->allows('manage_system') ? 'YES' : 'NO') . "\n";
    echo "Gate check access_admin_panel: " . (Gate::forUser($user)->allows('access_admin_panel') ? 'YES' : 'NO') . "\n";
} else {
    echo "User not found\n";
}
