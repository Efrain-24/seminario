<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Información del lote 1
echo "=== LOTE 1 ===\n";
$lote = \App\Models\Lote::find(1);
if ($lote) {
    echo "Lote: {$lote->codigo_lote}\n";
    echo "Unidad: {$lote->unidad_produccion_id}\n";
    echo "\n";
}

// Protocolos de la unidad
echo "=== PROTOCOLOS DE LA UNIDAD ===\n";
$protocolos = \App\Models\ProtocoloSanidad::where('unidad_produccion_id', $lote->unidad_produccion_id)->get();
echo "Total protocolos: " . $protocolos->count() . "\n";
foreach ($protocolos as $p) {
    echo "ID: {$p->id}, Nombre: {$p->nombre}, Estado: {$p->estado}, Fecha Ejecución: {$p->fecha_ejecucion}\n";
}

// Protocolos ejecutados
echo "\n=== PROTOCOLOS EJECUTADOS ===\n";
$ejecutados = \App\Models\ProtocoloSanidad::where('unidad_produccion_id', $lote->unidad_produccion_id)
    ->where('estado', 'ejecutado')
    ->get();
echo "Total ejecutados: " . $ejecutados->count() . "\n";
foreach ($ejecutados as $p) {
    echo "ID: {$p->id}, Nombre: {$p->nombre}, Estado: {$p->estado}\n";
    echo "  Insumos: " . $p->insumos->count() . "\n";
    foreach ($p->insumos as $insumo) {
        echo "    - {$insumo->inventarioItem->nombre}: {$insumo->cantidad_necesaria} x Q{$insumo->inventarioItem->costo_unitario} = Q" . ($insumo->cantidad_necesaria * $insumo->inventarioItem->costo_unitario) . "\n";
    }
}

// Ver todos los estados disponibles en protocolos
echo "\n=== TODOS LOS ESTADOS ===\n";
$estados = DB::table('protocolo_sanidads')->distinct()->pluck('estado');
echo "Estados: " . $estados->implode(', ') . "\n";
