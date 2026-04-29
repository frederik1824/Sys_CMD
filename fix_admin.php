<?php
$user = App\Models\User::first();
if ($user) {
    $user->assignRole('Admin');
    $role = Spatie\Permission\Models\Role::findOrCreate('Admin');
    $role->givePermissionTo(Spatie\Permission\Models\Permission::all());
    echo "Admin permissions updated.\n";
} else {
    echo "No user found.\n";
}
