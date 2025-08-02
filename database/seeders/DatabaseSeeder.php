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
        // Ejecutar seeder de roles primero
        $this->call(RoleSeeder::class);
        
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

        $this->command->info('Usuarios creados/actualizados:');
        $this->command->info('- Admin Principal: test@example.com / password');
        $this->command->info('- Admin: admin@piscicultura.com / admin123');
        $this->command->info('- Manager: manager@piscicultura.com / manager123');
        $this->command->info('- Empleado: empleado@piscicultura.com / empleado123');
    }
}
