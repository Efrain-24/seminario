<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lote;
use App\Models\UnidadProduccion;

echo "=== VALIDACIÓN: UNA UNIDAD = UN LOTE ===\n\n";

// Obtener unidades con lotes activos
$unidades = UnidadProduccion::with(['lotes' => function($query) {
    $query->where('estado', 'activo');
}])->where('estado', 'activo')->get();

echo "ESTADO ACTUAL DE UNIDADES:\n";
foreach($unidades as $unidad) {
    $lotesActivos = $unidad->lotes;
    echo "Unidad: {$unidad->nombre} ({$unidad->codigo})\n";
    echo "Lotes activos: " . $lotesActivos->count() . "\n";
    
    if ($lotesActivos->count() > 0) {
        foreach($lotesActivos as $lote) {
            echo "  - {$lote->codigo_lote} (Estado: {$lote->estado})\n";
        }
    } else {
        echo "  - DISPONIBLE para nuevo lote\n";
    }
    echo "---\n";
}

// Mostrar unidades disponibles (sin lotes activos)
echo "\nUNIDADES DISPONIBLES PARA NUEVOS LOTES:\n";
$disponibles = UnidadProduccion::where('estado', 'activo')
    ->whereDoesntHave('lotes', function($query) {
        $query->where('estado', 'activo');
    })
    ->get(['nombre', 'codigo']);

if ($disponibles->count() > 0) {
    foreach($disponibles as $unidad) {
        echo "✅ {$unidad->nombre} ({$unidad->codigo})\n";
    }
} else {
    echo "❌ No hay unidades disponibles - todas tienen lotes activos\n";
}

?>