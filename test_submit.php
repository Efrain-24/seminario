<?php
// Test para verificar si FormData está siendo enviado correctamente

echo "=== TEST DE FORM DATA ===\n\n";

// Simulamos lo que debería enviar el formulario
$testData = [
    '_token' => 'test_token',
    'unidad_produccion_id' => '1',
    'tipo_mantenimiento' => 'limpieza',
    'prioridad' => 'alta',
    'user_id' => '6',
    'fecha_mantenimiento' => '2025-03-01',
];

// Los insumos deberían venir así:
$testDataWithInsumos = $testData + [
    'insumos' => ['1', '2', '3'],  // Array de IDs
    'cantidades' => ['2', '5', '1'],  // Array de cantidades
];

echo "Sin insumos:\n";
var_dump($testData);
echo "\n";

echo "Con insumos (ESPERADO):\n";
var_dump($testDataWithInsumos);
echo "\n";

// Lo que probablemente está pasando es que llega así:
$actualData = [
    '_token' => 'test_token',
    'unidad_produccion_id' => '1',
    'tipo_mantenimiento' => 'limpieza',
    'prioridad' => 'alta',
    'user_id' => '6',
    'fecha_mantenimiento' => '2025-03-01',
    // SIN INSUMOS
];

echo "Lo que probablemente está llegando:\n";
var_dump($actualData);
echo "\n";

echo "El issue: FormData.append() podría no estar funcionando correctamente\n";
echo "o el array insumosFacturaSeleccionados está vacío.\n";
?>
