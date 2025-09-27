<?php

require_once 'bootstrap/app.php';

use App\Models\CosechaParcial;
use App\Models\Lote;
use Illuminate\Support\Facades\DB;

// Hacer una prueba simple de crear cosecha
echo "=== PRUEBA DE CREACIÓN DE COSECHA ===\n";

try {
    // Ver lotes disponibles
    $lotes = Lote::select('id', 'codigo_lote', 'cantidad_actual')->get();
    echo "Lotes disponibles:\n";
    foreach ($lotes as $lote) {
        echo "- ID: {$lote->id}, Código: {$lote->codigo_lote}, Stock: {$lote->cantidad_actual}\n";
    }
    
    if ($lotes->count() === 0) {
        echo "❌ No hay lotes disponibles\n";
        exit;
    }
    
    // Tomar el primer lote con stock > 0
    $loteTest = $lotes->where('cantidad_actual', '>', 0)->first();
    
    if (!$loteTest) {
        echo "❌ No hay lotes con stock disponible\n";
        exit;
    }
    
    echo "\nUsando lote: {$loteTest->codigo_lote} (Stock: {$loteTest->cantidad_actual})\n";
    
    // Intentar crear cosecha
    $cosecha = CosechaParcial::create([
        'lote_id' => $loteTest->id,
        'fecha' => now()->format('Y-m-d'),
        'cantidad_cosechada' => 1, // Solo 1 pez para prueba
        'peso_cosechado_kg' => 0.5,
        'destino' => 'muestra',
        'responsable' => 'Sistema Test',
        'observaciones' => 'Prueba creada desde script de test'
    ]);
    
    echo "✅ Cosecha creada exitosamente!\n";
    echo "- ID: {$cosecha->id}\n";
    echo "- Fecha: {$cosecha->fecha}\n";
    echo "- Cantidad: {$cosecha->cantidad_cosechada}\n";
    echo "- Destino: {$cosecha->destino}\n";
    
    // Verificar que el stock se descontó
    $loteActualizado = Lote::find($loteTest->id);
    echo "- Stock del lote después: {$loteActualizado->cantidad_actual}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}