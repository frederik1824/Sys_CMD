<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'admin@arscmd.com';
$password = 'password';

$user = App\Models\User::where('email', $email)->first();

if ($user) {
    $user->password = Hash::make($password);
    $user->save();
    echo "Password updated for $email\n";
} else {
    // Try creating it if it doesn't exist
    $user = App\Models\User::create([
        'name' => 'Admin System',
        'email' => $email,
        'password' => Hash::make($password),
        'email_verified_at' => now(),
    ]);
    $user->assignRole('Admin');
    echo "User $email created and Admin role assigned.\n";
}
