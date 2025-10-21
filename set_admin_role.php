<?php
// Conexión directa a MySQL
$conexion = new mysqli('127.0.0.1', 'root', '', 'seminario');

if ($conexion->connect_error) {
    echo "❌ Error de conexión a MySQL:\n";
    die();
}

$email = 'mdeleong9@miumg.edu.gt';

echo "Otorgando acceso de ADMIN...\n\n";

// 1. Obtener el usuario actual
$sql = "SELECT id, name, email, role FROM users WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "❌ Usuario no encontrado\n";
    die();
}

echo "Usuario encontrado:\n";
echo "─────────────────────────────────────────\n";
echo "ID: {$user['id']}\n";
echo "Nombre: {$user['name']}\n";
echo "Email: {$user['email']}\n";
echo "Rol actual: " . ($user['role'] ?: 'Sin rol') . "\n\n";

// 2. Actualizar el rol a 'admin'
$new_role = 'admin';
$sql_update = "UPDATE users SET role = ? WHERE id = ?";
$stmt_update = $conexion->prepare($sql_update);
$stmt_update->bind_param('si', $new_role, $user['id']);

if ($stmt_update->execute()) {
    echo "✅ Rol actualizado a 'admin' exitosamente\n\n";
    
    // Verificar el cambio
    $stmt->execute();
    $result = $stmt->get_result();
    $user_updated = $result->fetch_assoc();
    
    echo "Estado Actualizado:\n";
    echo "─────────────────────────────────────────\n";
    echo "Usuario: {$user_updated['name']}\n";
    echo "Email: {$user_updated['email']}\n";
    echo "✅ Nuevo rol: {$user_updated['role']}\n\n";
    echo "El usuario ahora tiene acceso completo como ADMIN.\n";
} else {
    echo "❌ Error al actualizar rol: " . $conexion->error . "\n";
}

$conexion->close();
?>
