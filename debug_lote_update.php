<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lote;

echo "=== DEBUG LOTE UPDATE ===\n\n";

// Obtener el lote ID 1 para test
$lote = Lote::find(1);
if (!$lote) {
    echo "Lote ID 1 no encontrado\n";
    exit;
}

echo "LOTE ACTUAL:\n";
echo "ID: {$lote->id}\n";
echo "Código: {$lote->codigo_lote}\n";
echo "Precio actual: " . ($lote->precio_libra ?? 'NULL') . "\n";
echo "Updated_at: {$lote->updated_at}\n\n";

// Intentar actualizar directamente
echo "=== INTENTANDO UPDATE DIRECTO ===\n";
$resultado = $lote->update(['precio_libra' => 25.50]);
echo "Resultado update: " . ($resultado ? 'TRUE' : 'FALSE') . "\n";

// Recargar desde base de datos
$lote->refresh();
echo "Precio después del update: " . ($lote->precio_libra ?? 'NULL') . "\n";
echo "Updated_at después: {$lote->updated_at}\n\n";

// Verificar structure de la tabla
echo "=== ESTRUCTURA DE TABLA ===\n";
$columns = \DB::select("DESCRIBE lotes");
foreach($columns as $column) {
    if ($column->Field == 'precio_libra') {
        echo "Campo precio_libra encontrado:\n";
        echo "Tipo: {$column->Type}\n";
        echo "Null: {$column->Null}\n";
        echo "Default: {$column->Default}\n";
        break;
    }
}

?>