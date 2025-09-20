<?php

require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InventarioExistencia;
use App\Models\Bodega;
use App\Models\TipoAlimento;
use App\Models\Lote;

echo "=== SIMULANDO EL CONTROLADOR CORREGIDO ===\n\n";

try {
    // Simular exactamente lo que hace el controlador create()
    echo "1. EJECUTANDO LA LÓGICA DEL CONTROLADOR...\n";
    
    $lotes = Lote::where('estado', 'activo')->with('unidadProduccion')->get();
    $bodegas = Bodega::orderBy('nombre')->get();
    
    $tiposAlimento = TipoAlimento::where('activo', true)
        ->whereNotNull('inventario_item_id')
        ->orderBy('nombre')
        ->get();
    
    echo "   ✓ Lotes encontrados: " . $lotes->count() . "\n";
    echo "   ✓ Bodegas encontradas: " . $bodegas->count() . "\n";
    echo "   ✓ Tipos de alimento: " . $tiposAlimento->count() . "\n";
    
    // Crear estructura exacta del controlador
    $existenciasPorBodega = [];
    
    foreach ($bodegas as $bodega) {
        $existenciasPorBodega[$bodega->id] = [];
        
        echo "\n   Procesando bodega: {$bodega->nombre}\n";
        
        // Traer DIRECTAMENTE de tu inventario los alimentos con stock
        $existencias = InventarioExistencia::where('bodega_id', $bodega->id)
            ->where('stock_actual', '>', 0)
            ->whereHas('item', function($query) {
                $query->where('tipo', 'alimento'); // Solo alimentos de tu inventario
            })
            ->with(['item', 'item.tipoAlimento'])
            ->get();
        
        echo "     Existencias encontradas: " . $existencias->count() . "\n";
        
        foreach ($existencias as $existencia) {
            // Si el item tiene un tipo de alimento conectado
            if ($existencia->item && $existencia->item->tipoAlimento) {
                $tipo = $existencia->item->tipoAlimento;
                $alimento = [
                    'tipo_alimento_id' => $tipo->id,
                    'nombre_completo' => $tipo->nombre_completo,
                    'categoria' => ucfirst($tipo->categoria ?: 'pellet'),
                    'costo_por_kg' => $tipo->costo_por_kg ?: 0,
                    'cantidad_disponible' => round($existencia->stock_actual, 2),
                    'unidad' => $existencia->item->unidad_base
                ];
                
                $existenciasPorBodega[$bodega->id][] = $alimento;
                
                echo "       → {$tipo->nombre_completo}: {$existencia->stock_actual} {$existencia->item->unidad_base}\n";
            } else {
                // Si es un alimento sin tipo conectado
                $alimento = [
                    'tipo_alimento_id' => 'item_' . $existencia->item->id,
                    'nombre_completo' => $existencia->item->nombre,
                    'categoria' => 'Alimento',
                    'costo_por_kg' => 0,
                    'cantidad_disponible' => round($existencia->stock_actual, 2),
                    'unidad' => $existencia->item->unidad_base
                ];
                
                $existenciasPorBodega[$bodega->id][] = $alimento;
                
                echo "       → {$existencia->item->nombre}: {$existencia->stock_actual} {$existencia->item->unidad_base} (sin tipo conectado)\n";
            }
        }
    }
    
    // 2. Mostrar el resultado final
    echo "\n2. DATOS QUE SE ENVÍAN AL FORMULARIO:\n";
    
    $totalAlimentos = 0;
    foreach ($existenciasPorBodega as $bodegaId => $alimentos) {
        if (count($alimentos) > 0) {
            $bodega = Bodega::find($bodegaId);
            echo "\n   📦 {$bodega->nombre} (" . count($alimentos) . " alimentos):\n";
            
            foreach ($alimentos as $alimento) {
                echo "      • {$alimento['nombre_completo']}\n";
                echo "        Cantidad: {$alimento['cantidad_disponible']} {$alimento['unidad']}\n";
                echo "        Categoría: {$alimento['categoria']}\n";
                if ($alimento['costo_por_kg'] > 0) {
                    echo "        Precio: Q{$alimento['costo_por_kg']}/kg\n";
                }
                echo "\n";
                $totalAlimentos++;
            }
        }
    }
    
    echo "3. RESUMEN FINAL:\n";
    echo "   • Total opciones de alimento: {$totalAlimentos}\n";
    echo "   • Bodegas con alimentos: " . count(array_filter($existenciasPorBodega, function($items) { return count($items) > 0; })) . "\n";
    
    // 3. Generar el JSON que va al JavaScript
    echo "\n4. JSON PARA EL FRONTEND:\n";
    echo "```json\n";
    echo json_encode($existenciasPorBodega, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n```\n";
    
    if ($totalAlimentos > 0) {
        echo "\n✅ ÉXITO: El controlador ahora trae correctamente tus alimentos del inventario\n";
        echo "✅ Los datos están listos para mostrar en el formulario\n";
    } else {
        echo "\n❌ ERROR: No se encontraron alimentos para mostrar\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stacktrace:\n" . $e->getTraceAsString() . "\n";
}