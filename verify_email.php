<?php
// Conexión directa a MySQL
$conexion = new mysqli('127.0.0.1', 'root', '', 'seminario');

if ($conexion->connect_error) {
    echo "❌ Error de conexión a MySQL:\n";
    echo "Código: " . $conexion->connect_errno . "\n";
    echo "Mensaje: " . $conexion->connect_error . "\n";
    die();
}

echo "Verificando estado del email...\n\n";

$email = 'mdeleong9@miumg.edu.gt';

// Primero, ver el estado actual
$sql = "SELECT id, name, email, email_verified_at FROM users WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "❌ Usuario no encontrado\n";
    die();
}

echo "Estado Actual:\n";
echo "─────────────────────────────────────────\n";
echo "Email: {$user['email']}\n";
echo "Email Verificado: " . ($user['email_verified_at'] ? $user['email_verified_at'] : "❌ NO") . "\n\n";

if (!$user['email_verified_at']) {
    echo "Marcando email como verificado...\n";
    
    // Actualizar email_verified_at con la fecha actual
    $sql_update = "UPDATE users SET email_verified_at = NOW() WHERE id = ?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param('i', $user['id']);
    
    if ($stmt_update->execute()) {
        echo "✅ Email marcado como verificado exitosamente\n\n";
        
        // Verificar el cambio
        $stmt->execute();
        $result = $stmt->get_result();
        $user_updated = $result->fetch_assoc();
        
        echo "Estado Actualizado:\n";
        echo "─────────────────────────────────────────\n";
        echo "Email: {$user_updated['email']}\n";
        echo "Email Verificado: ✅ {$user_updated['email_verified_at']}\n\n";
        echo "El usuario ya puede iniciar sesión sin problemas.\n";
    } else {
        echo "❌ Error al actualizar: " . $conexion->error . "\n";
    }
} else {
    echo "✅ El email ya estaba verificado desde: {$user['email_verified_at']}\n";
}

$conexion->close();
?>
