<?php
// Conexión directa a MySQL
$conexion = new mysqli('127.0.0.1', 'root', '', 'seminario');

if ($conexion->connect_error) {
    echo "❌ Error de conexión a MySQL:\n";
    echo "Código: " . $conexion->connect_errno . "\n";
    echo "Mensaje: " . $conexion->connect_error . "\n";
    die();
}

echo "✅ Conectado a MySQL exitosamente\n\n";

// Buscar usuario por email
$email = 'mdeleong9@miumg.edu.gt';
$sql = "SELECT id, name, email, password, created_at FROM users WHERE email = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo "❌ Error en prepare: " . $conexion->error . "\n";
    die();
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ Usuario NO encontrado en la base de datos\n";
    echo "Email buscado: $email\n\n";
    
    // Mostrar todos los usuarios
    echo "Usuarios existentes en la base de datos:\n";
    echo str_repeat("-", 60) . "\n";
    
    $sql_all = "SELECT id, name, email, created_at FROM users ORDER BY created_at DESC";
    $result_all = $conexion->query($sql_all);
    
    if ($result_all->num_rows > 0) {
        while ($row = $result_all->fetch_assoc()) {
            echo "ID: {$row['id']}\n";
            echo "Nombre: {$row['name']}\n";
            echo "Email: {$row['email']}\n";
            echo "Creado: {$row['created_at']}\n";
            echo str_repeat("-", 60) . "\n";
        }
    } else {
        echo "No hay usuarios en la base de datos\n";
    }
} else {
    $user = $result->fetch_assoc();
    
    echo "✅ Usuario ENCONTRADO\n\n";
    echo "Información del Usuario:\n";
    echo "─────────────────────────────────────────\n";
    echo "ID: {$user['id']}\n";
    echo "Nombre: {$user['name']}\n";
    echo "Email: {$user['email']}\n";
    echo "Creado: {$user['created_at']}\n\n";
    
    // Verificar contraseña
    $password = '5qgkLP9XRH7!u';
    $hash = $user['password'];
    
    echo "Verificación de Contraseña:\n";
    echo "─────────────────────────────────────────\n";
    
    // Usar password_verify para verificar
    if (password_verify($password, $hash)) {
        echo "✅ La contraseña es CORRECTA\n";
        echo "La cuenta está lista para usar\n";
    } else {
        echo "❌ La contraseña es INCORRECTA\n";
        echo "Hash almacenado: " . substr($hash, 0, 20) . "...\n";
    }
}

$conexion->close();
?>
