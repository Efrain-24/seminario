<?php
// Script para verificar si unidad_produccion_id está en la base de datos

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\MantenimientoUnidad;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Obtener el último mantenimiento
$mantenimiento = MantenimientoUnidad::orderBy('id', 'desc')->first();

if ($mantenimiento) {
    echo "=== Último Mantenimiento ===\n";
    echo "ID: " . $mantenimiento->id . "\n";
    echo "unidad_produccion_id: " . ($mantenimiento->unidad_produccion_id ?? 'NULL') . "\n";
    echo "unidadProduccion->nombre: " . ($mantenimiento->unidadProduccion?->nombre ?? 'NULL') . "\n";
    echo "Todos los campos:\n";
    var_dump($mantenimiento->toArray());
} else {
    echo "No hay mantenimientos en la base de datos\n";
}
?>
