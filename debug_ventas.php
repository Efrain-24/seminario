<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CosechaParcial;

echo "=== ÚLTIMAS 5 VENTAS ===\n";

$ventas = CosechaParcial::whereNotNull('codigo_venta')
    ->with('lote')
    ->orderBy('id', 'desc')
    ->take(5)
    ->get(['id', 'codigo_venta', 'cliente', 'cliente_nit', 'cliente_nombre', 'lote_id', 'precio_kg', 'total_venta', 'created_at']);

foreach($ventas as $v) {
    $precio_libra = $v->precio_kg ? round($v->precio_kg * 0.453592, 2) : 0;
    $lote_precio = $v->lote ? $v->lote->precio_libra : 'N/A';
    echo "ID: {$v->id} - Venta: {$v->codigo_venta}\n";
    echo "  Cliente: '{$v->cliente}' | Nombre: '{$v->cliente_nombre}' | NIT: '{$v->cliente_nit}'\n";
    echo "  Precio usado: Q{$precio_libra}/lb | Precio lote: Q{$lote_precio}/lb | Total: Q{$v->total_venta}\n";
    echo "  Fecha: {$v->created_at}\n";
    echo "  ---\n";
}