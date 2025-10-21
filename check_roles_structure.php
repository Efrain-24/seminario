<?php
// Conexión directa a MySQL
$conexion = new mysqli('127.0.0.1', 'root', '', 'seminario');

if ($conexion->connect_error) {
    echo "❌ Error de conexión a MySQL:\n";
    die();
}

// Ver estructura de la tabla roles
echo "Estructura de la tabla 'roles':\n";
echo str_repeat("─", 50) . "\n";

$sql = "DESCRIBE roles";
$result = $conexion->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "{$row['Field']}: {$row['Type']}\n";
}

echo "\n" . str_repeat("─", 50) . "\n";
echo "Estructura de la tabla 'role_user':\n";
echo str_repeat("─", 50) . "\n";

$sql2 = "DESCRIBE role_user";
$result2 = $conexion->query($sql2);

while ($row = $result2->fetch_assoc()) {
    echo "{$row['Field']}: {$row['Type']}\n";
}

$conexion->close();
?>
