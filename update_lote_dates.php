<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\Lote;

// Actualizar fechas de lotes para mostrar edades realistas
$lotes = Lote::all();

echo "Actualizando fechas de lotes...\n\n";

foreach ($lotes as $index => $lote) {
    // Crear fechas escalonadas hacia atrás
    $diasAtras = 30 + ($index * 45); // 30, 75, 120, 165 días atrás
    $nuevaFecha = now()->subDays($diasAtras);
    
    $lote->update(['fecha_inicio' => $nuevaFecha]);
    
    echo "✅ {$lote->codigo_lote}: Actualizado a {$nuevaFecha->format('d/m/Y')} (hace {$diasAtras} días)\n";
}

echo "\n🎉 ¡Fechas actualizadas correctamente!\n";
