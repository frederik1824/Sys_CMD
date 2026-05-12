<?php
use App\Models\User;

$email = 'enyer@arscmd.com.do';
$user = User::withTrashed()->where('email', $email)->first();

if ($user) {
    $user->forceDelete();
    echo "Usuario $email eliminado FISICAMENTE con éxito.\n";
} else {
    echo "No se encontró rastro de $email en la base de datos.\n";
}
