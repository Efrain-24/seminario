<?php

Route::get('/test-seguimientos', function () {
    $seguimientosCount = \App\Models\Seguimiento::count();
    $seguimientos = \App\Models\Seguimiento::with('lote')->take(10)->get();
    
    echo "<h2>Verificación de Seguimientos</h2>";
    echo "<p><strong>Total de seguimientos:</strong> " . $seguimientosCount . "</p>";
    
    if ($seguimientosCount > 0) {
        echo "<h3>Primeros 10 seguimientos:</h3>";
        foreach ($seguimientos as $seguimiento) {
            echo "<div style='margin: 5px 0; padding: 5px; border: 1px solid #ccc;'>";
            echo "Lote: " . ($seguimiento->lote ? $seguimiento->lote->codigo_lote : 'N/A');
            echo " | Fecha: " . $seguimiento->fecha_seguimiento;
            echo " | Peso promedio: " . ($seguimiento->peso_promedio ?? 'N/A') . "g";
            echo " | Tipo: " . $seguimiento->tipo_seguimiento;
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>❌ NO HAY SEGUIMIENTOS EN LA BASE DE DATOS</p>";
        echo "<p>Los datos de predicción son completamente <strong>SIMULADOS</strong> basados en fórmulas matemáticas.</p>";
    }
    
    // Verificar datos de lotes
    $lotes = \App\Models\Lote::where('estado', 'activo')->get();
    echo "<h3>Análisis de datos de predicción:</h3>";
    
    foreach ($lotes as $lote) {
        $semanasTranscurridas = $lote->fecha_inicio->diffInWeeks(now());
        $seguimientos = $lote->seguimientos()->count();
        
        echo "<div style='margin: 10px 0; padding: 10px; background: #f5f5f5;'>";
        echo "<strong>Lote:</strong> " . $lote->codigo_lote . "<br>";
        echo "<strong>Semanas transcurridas:</strong> " . $semanasTranscurridas . "<br>";
        echo "<strong>Seguimientos registrados:</strong> " . $seguimientos . "<br>";
        
        if ($seguimientos == 0) {
            echo "<span style='color: orange;'>⚠️ Sin seguimientos reales - usando FÓRMULAS SIMULADAS</span>";
        } else {
            echo "<span style='color: green;'>✅ Usando datos reales de seguimientos</span>";
        }
        echo "</div>";
    }
    
    return '';
});
