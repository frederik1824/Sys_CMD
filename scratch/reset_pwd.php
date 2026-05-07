<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'admin@arscmd.com')->first();
if ($user) {
    $user->password = Hash::make('password123');
    $user->save();
    echo "Clave actualizada con exito.\n";
} else {
    echo "Usuario no encontrado.\n";
}
