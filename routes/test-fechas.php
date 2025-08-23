<?php

use Illuminate\Http\Request;
use App\Models\Lote;
use Carbon\Carbon;

Route::get('/test-fechas-lotes', function () {
    $lotes = Lote::select('codigo_lote', 'fecha_inicio', 'created_at')->get();
    
    echo "<h2>Verificación de Fechas de Lotes</h2>";
    echo "<p>Fecha actual: " . now()->format('Y-m-d H:i:s') . "</p><br>";
    
    foreach ($lotes as $lote) {
        $fechaInicio = Carbon::parse($lote->fecha_inicio);
        $diasTranscurridos = now()->diffInDays($fechaInicio);
        
        echo "<div style='margin-bottom: 10px; padding: 10px; border: 1px solid #ccc;'>";
        echo "<strong>Lote:</strong> " . $lote->codigo_lote . "<br>";
        echo "<strong>Fecha inicio:</strong> " . $fechaInicio->format('Y-m-d') . "<br>";
        echo "<strong>Fecha creación registro:</strong> " . $lote->created_at->format('Y-m-d H:i:s') . "<br>";
        echo "<strong>Días transcurridos:</strong> " . $diasTranscurridos . " días<br>";
        echo "<strong>Formato mejorado:</strong> " . formatDiasVida($diasTranscurridos) . "<br>";
        echo "</div>";
    }
    
    return '';
});
