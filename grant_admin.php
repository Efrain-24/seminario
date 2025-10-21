<?php
// Conexión directa a MySQL
$conexion = new mysqli('127.0.0.1', 'root', '', 'seminario');

if ($conexion->connect_error) {
    echo "❌ Error de conexión a MySQL:\n";
    echo "Código: " . $conexion->connect_errno . "\n";
    echo "Mensaje: " . $conexion->connect_error . "\n";
    die();
}

$email = 'mdeleong9@miumg.edu.gt';

echo "Otorgando acceso de ADMIN al usuario...\n\n";

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

echo "Usuario encontrado:\n";
echo "─────────────────────────────────────────\n";
echo "ID: {$user['id']}\n";
echo "Nombre: {$user['name']}\n";
echo "Email: {$user['email']}\n\n";

// 2. Obtener el ID del rol 'admin'
$sql_role = "SELECT id FROM roles WHERE name = 'admin'";
$result_role = $conexion->query($sql_role);

if ($result_role->num_rows === 0) {
    echo "❌ El rol 'admin' no existe en la base de datos\n";
    
    // Mostrar roles disponibles
    echo "\nRoles disponibles:\n";
    $sql_all_roles = "SELECT id, name FROM roles";
    $result_all = $conexion->query($sql_all_roles);
    while ($row = $result_all->fetch_assoc()) {
        echo "- {$row['name']} (ID: {$row['id']})\n";
    }
    die();
}

$role = $result_role->fetch_assoc();
$role_id = $role['id'];

echo "Rol 'admin' encontrado (ID: $role_id)\n\n";

// 3. Verificar si el usuario ya tiene este rol
$sql_check = "SELECT * FROM role_user WHERE user_id = ? AND role_id = ?";
$stmt_check = $conexion->prepare($sql_check);
$stmt_check->bind_param('ii', $user['id'], $role_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "✅ El usuario ya tiene el rol 'admin'\n";
} else {
    // 4. Asignar el rol admin al usuario
    $sql_assign = "INSERT INTO role_user (user_id, role_id) VALUES (?, ?)";
    $stmt_assign = $conexion->prepare($sql_assign);
    $stmt_assign->bind_param('ii', $user['id'], $role_id);
    
    if ($stmt_assign->execute()) {
        echo "✅ Rol 'admin' asignado exitosamente al usuario\n\n";
        
        // Mostrar roles actuales del usuario
        echo "Roles actuales del usuario:\n";
        echo "─────────────────────────────────────────\n";
        $sql_roles_user = "SELECT r.id, r.name FROM roles r
                          INNER JOIN role_user ru ON r.id = ru.role_id
                          WHERE ru.user_id = ?";
        $stmt_roles = $conexion->prepare($sql_roles_user);
        $stmt_roles->bind_param('i', $user['id']);
        $stmt_roles->execute();
        $result_roles = $stmt_roles->get_result();
        
        while ($row = $result_roles->fetch_assoc()) {
            echo "✅ {$row['name']}\n";
        }
    } else {
        echo "❌ Error al asignar rol: " . $conexion->error . "\n";
    }
}

$conexion->close();
?>
