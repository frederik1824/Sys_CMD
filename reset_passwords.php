<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = User::all();
foreach ($users as $user) {
    $user->password = Hash::make('admin1234');
    $user->save();
    echo "Reseteado: {$user->email}\n";
}
echo "Proceso Terminado.";
