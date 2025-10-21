<?php
// Conexión directa a MySQL
$conexion = new mysqli('127.0.0.1', 'root', '', 'seminario');

if ($conexion->connect_error) {
    echo "❌ Error de conexión a MySQL:\n";
    die();
}

echo "Tablas disponibles en la base de datos:\n";
echo str_repeat("─", 50) . "\n";

$sql = "SHOW TABLES";
$result = $conexion->query($sql);

$tables = [];
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

sort($tables);
foreach ($tables as $table) {
    echo "- $table\n";
}

echo "\n" . str_repeat("─", 50) . "\n";
echo "Buscando tablas relacionadas con usuarios...\n";
echo str_repeat("─", 50) . "\n";

// Verificar estructura de la tabla users
echo "\nEstructura de 'users':\n";
$sql_users = "DESCRIBE users";
$result_users = $conexion->query($sql_users);
while ($row = $result_users->fetch_assoc()) {
    echo "- {$row['Field']}: {$row['Type']}\n";
}

$conexion->close();
?>
