<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CosechaParcial;

echo "=== ÚLTIMAS 3 VENTAS ===\n";

$ventas = CosechaParcial::whereNotNull('codigo_venta')
    ->orderBy('id', 'desc')
    ->take(3)
    ->get(['id', 'codigo_venta', 'cliente', 'cliente_nit', 'lote_id', 'created_at']);

foreach($ventas as $v) {
    echo "ID: {$v->id} - Venta: {$v->codigo_venta} - Cliente: '{$v->cliente}' - NIT: '{$v->cliente_nit}' - Fecha: {$v->created_at}\n";
}