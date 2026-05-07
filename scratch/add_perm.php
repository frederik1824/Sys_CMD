<?php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

Permission::firstOrCreate(['name' => 'configurar_traspasos']);

$role = Role::where('name', 'Supervisor de Traspasos')->first();
if ($role) $role->givePermissionTo('configurar_traspasos');

$admin = Role::where('name', 'Admin')->first();
if ($admin) $admin->givePermissionTo('configurar_traspasos');

echo "Permissions updated successfully.\n";
