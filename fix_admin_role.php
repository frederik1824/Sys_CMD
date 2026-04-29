<?php
use App\Models\User;
use Spatie\Permission\Models\Role;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = User::where('email', 'admin@arscmd.com')->first();
if ($user) {
    // Asignamos los roles correctos que encontramos en la DB
    $user->assignRole(['Admin']);
    echo "Roles 'Admin' y 'Admin' asignados correctamente a: " . $user->email;
}
