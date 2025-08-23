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
                    'gestionar_usuarios.view', 'gestionar_usuarios.create', 'gestionar_usuarios.edit', 'gestionar_usuarios.delete',
                    'gestionar_roles.view', 'gestionar_roles.create', 'gestionar_roles.edit', 'gestionar_roles.delete',
                    'unidades.view', 'unidades.create', 'unidades.edit', 'unidades.delete',
                    'lotes.view', 'lotes.create', 'lotes.edit', 'lotes.delete',
                    'mantenimientos.view', 'mantenimientos.create', 'mantenimientos.edit', 'mantenimientos.delete',
                    'alimentacion.view', 'alimentacion.create', 'alimentacion.edit', 'alimentacion.delete',
                    'sanidad.view', 'sanidad.create', 'sanidad.edit', 'sanidad.delete',
                    'crecimiento.view', 'crecimiento.create', 'crecimiento.edit', 'crecimiento.delete',
                    'costos.view', 'costos.create', 'costos.edit', 'costos.delete',
                    'monitoreo.view', 'monitoreo.create', 'monitoreo.edit', 'monitoreo.delete'
                ],
                'is_active' => true
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Gestión operativa',
                'permissions' => [
                    'gestionar_usuarios.view', 'gestionar_usuarios.create', 'gestionar_usuarios.edit',
                    'unidades.view', 'unidades.create', 'unidades.edit', 'unidades.delete',
                    'lotes.view', 'lotes.create', 'lotes.edit', 'lotes.delete',
                    'mantenimientos.view', 'mantenimientos.create', 'mantenimientos.edit', 'mantenimientos.delete',
                    'alimentacion.view', 'alimentacion.create', 'alimentacion.edit', 'alimentacion.delete',
                    'sanidad.view', 'sanidad.create', 'sanidad.edit', 'sanidad.delete',
                    'crecimiento.view', 'crecimiento.create', 'crecimiento.edit', 'crecimiento.delete',
                    'costos.view', 'costos.create', 'costos.edit', 'costos.delete',
                    'monitoreo.view', 'monitoreo.create', 'monitoreo.edit', 'monitoreo.delete'
                ],
                'is_active' => true
            ],
            [
                'name' => 'empleado',
                'display_name' => 'Empleado',
                'description' => 'Acceso operativo básico',
                'permissions' => [
                    'unidades.view', 'unidades.create', 'unidades.edit',
                    'lotes.view', 'lotes.create', 'lotes.edit',
                    'mantenimientos.view', 'mantenimientos.create',
                    'alimentacion.view', 'alimentacion.create', 'alimentacion.edit',
                    'sanidad.view', 'sanidad.create', 'sanidad.edit',
                    'crecimiento.view', 'crecimiento.create', 'crecimiento.edit',
                    'costos.view', 'costos.create',
                    'monitoreo.view', 'monitoreo.create', 'monitoreo.edit'
                ],
                'is_active' => true
            ],
            [
                'name' => 'generico',
                'display_name' => 'Genérico',
                'description' => 'Solo lectura',
                'permissions' => [
                    'unidades.view',
                    'lotes.view',
                    'mantenimientos.view',
                    'alimentacion.view',
                    'sanidad.view',
                    'crecimiento.view',
                    'costos.view',
                    'monitoreo.view'
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

        // Ejecutar seeders adicionales
        $this->call([
            ProduccionSeeder::class,
            TipoAlimentoSeeder::class,
            AlimentacionSeeder::class,
            CosechaParcialSeeder::class,
            MortalidadSeeder::class,
            InventarioSeeder::class,
        ]);
    }
}
