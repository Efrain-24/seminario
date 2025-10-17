<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Últimos registros en mantenimiento_insumo ===\n";
$registros = DB::table('mantenimiento_insumo')->latest()->limit(10)->get();
foreach ($registros as $registro) {
    echo "ID: {$registro->id}, Mantenimiento: {$registro->mantenimiento_unidad_id}, Insumo: {$registro->inventario_item_id}, Cantidad: {$registro->cantidad}\n";
}

echo "\n=== Últimos 5 mantenimientos ===\n";
$mantenimientos = DB::table('mantenimiento_unidades')->latest()->limit(5)->get();
foreach ($mantenimientos as $mant) {
    echo "ID: {$mant->id}, Tipo: {$mant->tipo_mantenimiento}, Fecha: {$mant->fecha_mantenimiento}\n";
    $insumos = DB::table('mantenimiento_insumo')->where('mantenimiento_unidad_id', $mant->id)->get();
    echo "  -> Insumos: " . count($insumos) . "\n";
}
