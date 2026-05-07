<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;

$p = Permission::where('name', 'access_admin_panel')->first();
if ($p) {
    echo "Permission exists\n";
} else {
    echo "Permission DOES NOT exist\n";
}
