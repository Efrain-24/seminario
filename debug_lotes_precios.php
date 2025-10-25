<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lote;

echo "=== PRECIOS DE LOTES ===\n";
$lotes = Lote::orderBy('id', 'desc')->take(5)->get(['id', 'codigo_lote', 'precio_libra']);
foreach($lotes as $lote) {
    $precio = $lote->precio_libra ?? 'NULL';
    echo "Lote ID: {$lote->id} | Codigo: {$lote->codigo_lote} | Precio: Q{$precio}/lb\n";
}

echo "\n=== SISTEMA SIN PRECIO GLOBAL ===\n";
echo "Cada lote debe tener su precio configurado individualmente.\n";

?>