<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CheckUsers extends Command
{
    protected $signature = 'users:check';
    protected $description = 'Check existing users and create test user if needed';

    public function handle()
    {
        // Mostrar usuarios existentes
        $users = User::all();
        $this->info("=== Usuarios en la base de datos ===");
        
        if ($users->count() == 0) {
            $this->warn("No hay usuarios en la base de datos");
        } else {
            foreach ($users as $user) {
                $this->line("ID: {$user->id} | Email: {$user->email} | Rol: {$user->role}");
            }
        }

        // Crear usuario de prueba si no existe
        $testEmail = 'test@example.com';
        $existingUser = User::where('email', $testEmail)->first();

        if (!$existingUser) {
            $user = User::create([
                'name' => 'Test User',
                'email' => $testEmail,
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            $this->success("Usuario creado: {$user->email}");
        } else {
            // Actualizar contraseña para asegurar que funcione
            $existingUser->password = Hash::make('password');
            $existingUser->save();
            $this->info("Usuario ya existe, contraseña actualizada: {$existingUser->email}");
        }

        $this->info("\n=== Credenciales para login ===");
        $this->line("Email: test@example.com");
        $this->line("Password: password");
        $this->line("URL: http://localhost:8001/login");
        
        return 0;
    }
}
