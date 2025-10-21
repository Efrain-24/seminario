<?php

require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Lote;

echo "=== VERIFICANDO DATOS DE LOTES ===\n\n";

$lotes = Lote::where('estado', 'activo')
    ->orderBy('codigo_lote')
    ->get(['id', 'codigo_lote', 'especie', 'cantidad_actual']);

echo "Total de lotes activos: " . $lotes->count() . "\n\n";

foreach($lotes as $lote) {
    echo "ID: {$lote->id}\n";
    echo "Código: {$lote->codigo_lote}\n";
    echo "Especie: {$lote->especie}\n";
    echo "Cantidad actual: {$lote->cantidad_actual}\n";
    echo "------------------------\n";
}

echo "\n=== ESTRUCTURA JSON ===\n";
echo json_encode($lotes->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);