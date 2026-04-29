<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AfiliacionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'solicitudes_afiliacion.index',
            'solicitudes_afiliacion.create',
            'solicitudes_afiliacion.show',
            'solicitudes_afiliacion.asignarse',
            'solicitudes_afiliacion.procesar',
            'solicitudes_afiliacion.configurar',
            'solicitudes_afiliacion.ver_todas',
            'solicitudes_afiliacion.escalar',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar a Admin
        $admin = \Spatie\Permission\Models\Role::where('name', 'Admin')->orWhere('name', 'Super Admin')->first();
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }

        // Asignar a Supervisor (Afiliación, Autorizaciones y Servicio al Cliente)
        $supervisores = \Spatie\Permission\Models\Role::with('permissions')->whereIn('name', [
            'Supervisor', 
            'Supervisor de Afiliación', 
            'Supervisor de Autorizaciones',
            'Supervisor de Servicio al Cliente'
        ])->get();

        // Si alguno no existe, crearlo (especialmente el nuevo de Servicio al Cliente)
        $rolesToEnsure = ['Supervisor de Servicio al Cliente'];
        foreach ($rolesToEnsure as $roleName) {
            if (!\Spatie\Permission\Models\Role::where('name', $roleName)->exists()) {
                $newRole = \Spatie\Permission\Models\Role::create(['name' => $roleName, 'guard_name' => 'web']);
                $supervisores->push($newRole);
            }
        }

        foreach ($supervisores as $sup) {
            $sup->givePermissionTo($permissions);
        }

        // Asignar a Analistas
        $analistas = \Spatie\Permission\Models\Role::with('permissions')->whereIn('name', [
            'Analista de Afiliación',
            'Analista de Autorizaciones'
        ])->get();
        foreach ($analistas as $ana) {
            $ana->givePermissionTo([
                'solicitudes_afiliacion.index',
                'solicitudes_afiliacion.create',
                'solicitudes_afiliacion.show',
                'solicitudes_afiliacion.asignarse',
                'solicitudes_afiliacion.procesar',
            ]);
        }

        // Asignar a CSR (Asumiendo que existe o se llama de otra forma)
        $csr = \Spatie\Permission\Models\Role::with('permissions')->whereIn('name', [
            'Operador', 
            'CSR', 
            'Servicio al Cliente (CSR)'
        ])->get();
        foreach ($csr as $c) {
            $c->givePermissionTo([
                'solicitudes_afiliacion.index',
                'solicitudes_afiliacion.create',
                'solicitudes_afiliacion.show',
            ]);
        }

        // Asignar a Departamentos Transversales (Solo lectura / Index)
        $transversales = \Spatie\Permission\Models\Role::with('permissions')->whereIn('name', [
            'Auditoría', 
            'Calidad', 
            'Recursos Humanos'
        ])->get();
        foreach ($transversales as $t) {
            $t->givePermissionTo([
                'solicitudes_afiliacion.index',
                'solicitudes_afiliacion.show',
            ]);
        }
    }
}
