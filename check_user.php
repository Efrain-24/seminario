<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Buscar usuario por email
$user = User::where('email', 'mdeleong9@miumg.edu.gt')->first();

if (!$user) {
    echo "❌ Usuario NO encontrado en la base de datos\n";
    echo "Email: mdeleong9@miumg.edu.gt\n";
    
    // Mostrar usuarios existentes
    echo "\nUsuarios en la base de datos:\n";
    $users = User::all(['id', 'name', 'email', 'created_at']);
    foreach ($users as $u) {
        echo "- {$u->email} ({$u->name})\n";
    }
} else {
    echo "✅ Usuario ENCONTRADO\n";
    echo "ID: {$user->id}\n";
    echo "Nombre: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Creado: {$user->created_at}\n";
    
    // Verificar contraseña
    $password = '5qgkLP9XRH7!u';
    $passwordValid = Hash::check($password, $user->password);
    
    echo "\n--- Verificación de Contraseña ---\n";
    echo "Contraseña: " . str_repeat('*', strlen($password)) . "\n";
    if ($passwordValid) {
        echo "✅ La contraseña es CORRECTA\n";
    } else {
        echo "❌ La contraseña es INCORRECTA\n";
    }
}
?>
