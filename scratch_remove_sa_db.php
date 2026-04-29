<?php

use Spatie\Permission\Models\Role;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sa = Role::where('name', 'Super-Admin')->first();
if ($sa) {
    $users = User::role('Super-Admin')->get();
    foreach($users as $u) {
        $u->assignRole('Admin');
        echo "Usuario {$u->email} reasignado a Admin.\n";
    }
    $sa->delete();
    echo "Rol Super-Admin eliminado permanentemente.\n";
} else {
    echo "El rol Super-Admin no existe en la base de datos.\n";
}
