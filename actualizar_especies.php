<?php
require_once 'vendor/autoload.php';

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';

try {
    // Actualizar lotes sin especie
    $lotesSinEspecie = \App\Models\Lote::whereNull('especie')
                                      ->orWhere('especie', '')
                                      ->limit(10)
                                      ->get();
    
    $especies = ['Tilapia', 'Trucha', 'Carpa', 'Salmón', 'Bagre'];
    
    foreach ($lotesSinEspecie as $index => $lote) {
        $especieAsignada = $especies[$index % count($especies)];
        $lote->update(['especie' => $especieAsignada]);
        echo "Lote {$lote->codigo_lote} actualizado con especie: {$especieAsignada}\n";
    }
    
    echo "\nLotes actualizados exitosamente!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>