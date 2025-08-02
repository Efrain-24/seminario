<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Verificar usuarios existentes
$users = User::all();
echo "Usuarios en la base de datos:\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Email: {$user->email}, Rol: {$user->role}\n";
}

// Crear usuario admin si no existe
$adminEmail = 'admin@test.com';
$existingAdmin = User::where('email', $adminEmail)->first();

if (!$existingAdmin) {
    $admin = User::create([
        'name' => 'Administrador',
        'email' => $adminEmail,
        'password' => Hash::make('password'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
    echo "\nUsuario admin creado: {$admin->email}\n";
} else {
    echo "\nUsuario admin ya existe: {$existingAdmin->email}\n";
}

echo "\nCredenciales para login:\n";
echo "Email: admin@test.com\n";
echo "Password: password\n";
