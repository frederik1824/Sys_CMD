<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSetupSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Array Completo de Permisos a crear y estructurar
        $permissions = [
            'access_admin_panel', // Dashboard admin, configuraciones básicas y Catálogos
            'manage_users',       // Crear, editar y eliminar Usuarios y Roles
            'manage_companies',   // Todo el CRUD de Empresas
            'manage_affiliates',  // CRUD de Afiliados, cambios de estados
            'manage_evidencias',  // Módulo de expedientes/evidencias
            'manage_logistics',   // Monitor logístico, Lotes, Mensajeros, Despachos, Rutas
            'manage_closures',    // Módulo de Cierre Físico de sobres
            'manage_liquidations',// Liquidaciones ante SAFESURE y control de pagos
            'view_reports',       // Visualización de Estadísticas, SLA, Heatmap, etc.
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Roles Principales
        $superAdmin = Role::firstOrCreate(['name' => 'Admin']); // Super Admin lo puede todo globalmente (Gate intercept)
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $operador = Role::firstOrCreate(['name' => 'Operador']);
        $auditor = Role::firstOrCreate(['name' => 'Auditor']);
        $mensajero = Role::firstOrCreate(['name' => 'Asistente de Logística']);

        // 3. Asignaciones Exactas por Perfil
        // Administrador: Domina operativos y reportes, pero quiza no configuraciones avanzadas
        $admin->syncPermissions([
            'manage_companies', 
            'manage_affiliates', 
            'manage_evidencias',
            'manage_logistics',
            'manage_closures',
            'manage_liquidations',
            'view_reports'
        ]);

        // Operador Base (Digiltador/CRM)
        $operador->syncPermissions([
            'manage_affiliates',
            'manage_companies',
            'manage_evidencias'
        ]);

        // Auditor (Solo ver reportes y liquidación quizá)
        $auditor->syncPermissions([
            'view_reports',
            'manage_liquidations'
        ]);

        // Equipo Logístico
        $mensajero->syncPermissions([
            'manage_logistics',
            'manage_closures'
        ]);

        // Reasignación para los Usuarios existentes, si se requiere limpiar
        // El Admin ya está puenteado por el AppServiceProvider o podemos forzar asignación:
        $firstUser = \App\Models\User::first();
        if ($firstUser && !$firstUser->hasRole('Admin')) {
            $firstUser->assignRole('Admin');
        }
    }
}
