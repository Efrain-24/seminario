<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear roles básicos primero
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Acceso completo al sistema',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                    'production.view', 'production.create', 'production.edit', 'production.delete',
                    'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
                    'sales.view', 'sales.create', 'sales.edit', 'sales.delete',
                    'reports.view', 'reports.create', 'reports.edit', 'reports.delete',
                    'finances.view', 'finances.create', 'finances.edit', 'finances.delete',
                    'maintenance.view', 'maintenance.create', 'maintenance.edit', 'maintenance.delete',
                    'system.view', 'system.create', 'system.edit', 'system.delete'
                ],
                'is_active' => true
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Gestión operativa',
                'permissions' => [
                    'users.view', 'users.edit',
                    'roles.view',
                    'production.view', 'production.create', 'production.edit', 'production.delete',
                    'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
                    'sales.view', 'sales.create', 'sales.edit',
                    'reports.view', 'reports.create',
                    'finances.view',
                    'maintenance.view', 'maintenance.create'
                ],
                'is_active' => true
            ],
            [
                'name' => 'empleado',
                'display_name' => 'Empleado',
                'description' => 'Acceso operativo básico',
                'permissions' => [
                    'users.view',
                    'production.view', 'production.create', 'production.edit',
                    'inventory.view', 'inventory.create',
                    'sales.view', 'sales.create',
                    'reports.view',
                    'maintenance.view', 'maintenance.create'
                ],
                'is_active' => true
            ],
            [
                'name' => 'generico',
                'display_name' => 'Genérico',
                'description' => 'Solo lectura',
                'permissions' => [
                    'production.view',
                    'inventory.view',
                    'sales.view',
                    'reports.view'
                ],
                'is_active' => true
            ]
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'permissions' => $roleData['permissions'],
                    'is_active' => $roleData['is_active']
                ]
            );
        }
        
        // Crear o actualizar usuario administrador principal
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Crear o actualizar usuario administrador adicional
        User::updateOrCreate(
            ['email' => 'admin@piscicultura.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Crear o actualizar usuario manager
        User::updateOrCreate(
            ['email' => 'manager@piscicultura.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'email_verified_at' => now(),
            ]
        );

        // Crear o actualizar usuario empleado
        User::updateOrCreate(
            ['email' => 'empleado@piscicultura.com'],
            [
                'name' => 'Empleado',
                'password' => Hash::make('empleado123'),
                'role' => 'empleado',
                'email_verified_at' => now(),
            ]
        );

        // Crear o actualizar usuario genérico
        User::updateOrCreate(
            ['email' => 'generico@piscicultura.com'],
            [
                'name' => 'Usuario Genérico',
                'password' => Hash::make('generico123'),
                'role' => 'generico',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Usuarios creados/actualizados:');
        $this->command->info('- Admin Principal: test@example.com / password');
        $this->command->info('- Admin: admin@piscicultura.com / admin123');
        $this->command->info('- Manager: manager@piscicultura.com / manager123');
        $this->command->info('- Empleado: empleado@piscicultura.com / empleado123');
        $this->command->info('- Genérico: generico@piscicultura.com / generico123');
    }
}
