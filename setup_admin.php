<?php
// Conexión directa a MySQL
$conexion = new mysqli('127.0.0.1', 'root', '', 'seminario');

if ($conexion->connect_error) {
    echo "❌ Error de conexión a MySQL:\n";
    die();
}

$email = 'mdeleong9@miumg.edu.gt';

echo "Configurando acceso de ADMIN...\n\n";

// 1. Obtener el ID del usuario
$sql = "SELECT id, name, email FROM users WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "❌ Usuario no encontrado\n";
    die();
}

echo "Usuario: {$user['name']} ({$user['email']})\n\n";

// 2. Crear rol admin si no existe
$sql_check_role = "SELECT id FROM roles WHERE name = 'admin'";
$result_role = $conexion->query($sql_check_role);

if ($result_role->num_rows === 0) {
    echo "Creando rol 'admin'...\n";
    $sql_insert_role = "INSERT INTO roles (name, guard_name, created_at, updated_at) 
                       VALUES ('admin', 'web', NOW(), NOW())";
    if ($conexion->query($sql_insert_role)) {
        $role_id = $conexion->insert_id;
        echo "✅ Rol 'admin' creado (ID: $role_id)\n\n";
    } else {
        echo "❌ Error al crear rol: " . $conexion->error . "\n";
        die();
    }
} else {
    $role = $result_role->fetch_assoc();
    $role_id = $role['id'];
    echo "Rol 'admin' ya existe (ID: $role_id)\n\n";
}

// 3. Verificar si el usuario ya tiene el rol
$sql_check_assign = "SELECT * FROM role_user WHERE user_id = ? AND role_id = ?";
$stmt_check = $conexion->prepare($sql_check_assign);
$stmt_check->bind_param('ii', $user['id'], $role_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "✅ El usuario ya tiene el rol 'admin'\n";
} else {
    // 4. Asignar el rol al usuario
    echo "Asignando rol 'admin' al usuario...\n";
    $sql_assign = "INSERT INTO role_user (user_id, role_id) VALUES (?, ?)";
    $stmt_assign = $conexion->prepare($sql_assign);
    $stmt_assign->bind_param('ii', $user['id'], $role_id);
    
    if ($stmt_assign->execute()) {
        echo "✅ Rol 'admin' asignado exitosamente\n";
    } else {
        echo "❌ Error al asignar rol: " . $conexion->error . "\n";
    }
}

echo "\n" . str_repeat("─", 50) . "\n";
echo "RESULTADO FINAL:\n";
echo str_repeat("─", 50) . "\n";

// Obtener roles actuales del usuario
$sql_final = "SELECT GROUP_CONCAT(r.name) as roles FROM roles r
             INNER JOIN role_user ru ON r.id = ru.role_id
             WHERE ru.user_id = ?";
$stmt_final = $conexion->prepare($sql_final);
$stmt_final->bind_param('i', $user['id']);
$stmt_final->execute();
$result_final = $stmt_final->get_result();
$final = $result_final->fetch_assoc();

echo "Usuario: {$user['name']}\n";
echo "Email: {$user['email']}\n";
echo "Roles: ✅ " . ($final['roles'] ?: 'Sin roles') . "\n";

$conexion->close();
?>
