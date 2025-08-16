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
                    'gestionar_usuarios', 'gestionar_roles',
                    'ver_unidades', 'crear_unidades', 'editar_unidades', 'eliminar_unidades',
                    'ver_lotes', 'crear_lotes', 'editar_lotes', 'eliminar_lotes',
                    'ver_mantenimientos', 'crear_mantenimientos', 'editar_mantenimientos', 'eliminar_mantenimientos',
                    'ver_alimentacion', 'crear_alimentacion', 'editar_alimentacion', 'eliminar_alimentacion',
                    'ver_sanidad', 'crear_sanidad', 'editar_sanidad', 'eliminar_sanidad',
                    'ver_crecimiento', 'crear_crecimiento', 'editar_crecimiento', 'eliminar_crecimiento',
                    'ver_costos', 'crear_costos', 'editar_costos', 'eliminar_costos',
                    'ver_monitoreo', 'crear_monitoreo', 'editar_monitoreo', 'eliminar_monitoreo'
                ],
                'is_active' => true
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Gestión operativa',
                'permissions' => [
                    'gestionar_usuarios',
                    'ver_unidades', 'crear_unidades', 'editar_unidades', 'eliminar_unidades',
                    'ver_lotes', 'crear_lotes', 'editar_lotes', 'eliminar_lotes',
                    'ver_mantenimientos', 'crear_mantenimientos', 'editar_mantenimientos', 'eliminar_mantenimientos',
                    'ver_alimentacion', 'crear_alimentacion', 'editar_alimentacion', 'eliminar_alimentacion',
                    'ver_sanidad', 'crear_sanidad', 'editar_sanidad', 'eliminar_sanidad',
                    'ver_crecimiento', 'crear_crecimiento', 'editar_crecimiento', 'eliminar_crecimiento',
                    'ver_costos', 'crear_costos', 'editar_costos', 'eliminar_costos',
                    'ver_monitoreo', 'crear_monitoreo', 'editar_monitoreo', 'eliminar_monitoreo'
                ],
                'is_active' => true
            ],
            [
                'name' => 'empleado',
                'display_name' => 'Empleado',
                'description' => 'Acceso operativo básico',
                'permissions' => [
                    'ver_unidades', 'crear_unidades', 'editar_unidades',
                    'ver_lotes', 'crear_lotes', 'editar_lotes',
                    'ver_mantenimientos', 'crear_mantenimientos',
                    'ver_alimentacion', 'crear_alimentacion', 'editar_alimentacion',
                    'ver_sanidad', 'crear_sanidad', 'editar_sanidad',
                    'ver_crecimiento', 'crear_crecimiento', 'editar_crecimiento',
                    'ver_costos', 'crear_costos',
                    'ver_monitoreo', 'crear_monitoreo', 'editar_monitoreo'
                ],
                'is_active' => true
            ],
            [
                'name' => 'generico',
                'display_name' => 'Genérico',
                'description' => 'Solo lectura',
                'permissions' => [
                    'ver_unidades',
                    'ver_lotes',
                    'ver_mantenimientos',
                    'ver_alimentacion',
                    'ver_sanidad',
                    'ver_crecimiento',
                    'ver_costos',
                    'ver_monitoreo'
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
