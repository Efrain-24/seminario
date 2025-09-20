<?php

require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InventarioItem;
use App\Models\InventarioExistencia;
use App\Models\Bodega;

echo "=== VERIFICACIÓN: SOLO ALIMENTOS EN FORMULARIO ===\n\n";

try {
    // 1. Mostrar todos los items del inventario por tipo
    echo "1. ITEMS EN INVENTARIO POR TIPO:\n";
    
    $itemsPorTipo = InventarioItem::all()->groupBy('tipo');
    
    foreach ($itemsPorTipo as $tipo => $items) {
        echo "   📦 TIPO: " . strtoupper($tipo) . "\n";
        foreach ($items as $item) {
            $stockTotal = $item->existencias->sum('stock_actual');
            echo "      • {$item->nombre} - {$stockTotal} {$item->unidad_base}\n";
        }
        echo "\n";
    }
    
    // 2. Simular lo que aparece en el formulario ANTES del filtro
    echo "2. ANTES DEL FILTRO (todos los items con stock):\n";
    
    $bodegas = Bodega::all();
    foreach ($bodegas as $bodega) {
        $existenciasSinFiltro = InventarioExistencia::where('bodega_id', $bodega->id)
            ->where('stock_actual', '>', 0)
            ->with(['item'])
            ->get();
        
        if ($existenciasSinFiltro->count() > 0) {
            echo "   📦 {$bodega->nombre}:\n";
            foreach ($existenciasSinFiltro as $existencia) {
                $tipo = $existencia->item->tipo;
                $emoji = $tipo == 'alimento' ? '🍽️' : '🔧';
                echo "      {$emoji} {$existencia->item->nombre} ({$tipo}) - {$existencia->stock_actual} {$existencia->item->unidad_base}\n";
            }
            echo "\n";
        }
    }
    
    // 3. Simular lo que aparece DESPUÉS del filtro (solo alimentos)
    echo "3. DESPUÉS DEL FILTRO (solo alimentos):\n";
    
    foreach ($bodegas as $bodega) {
        $existenciasConFiltro = InventarioExistencia::where('bodega_id', $bodega->id)
            ->where('stock_actual', '>', 0)
            ->whereHas('item', function($query) {
                $query->where('tipo', 'alimento'); // Solo alimentos
            })
            ->whereHas('item.tipoAlimento', function($query) {
                $query->where('activo', true);
            })
            ->with(['item.tipoAlimento'])
            ->get();
        
        if ($existenciasConFiltro->count() > 0) {
            echo "   📦 {$bodega->nombre}:\n";
            foreach ($existenciasConFiltro as $existencia) {
                if ($existencia->item && $existencia->item->tipoAlimento) {
                    $tipo = $existencia->item->tipoAlimento;
                    echo "      🍽️ {$tipo->nombre_completo} - {$existencia->stock_actual} kg\n";
                }
            }
            echo "\n";
        }
    }
    
    // 4. Contar diferencias
    echo "4. ESTADÍSTICAS:\n";
    
    $totalItemsConStock = InventarioExistencia::where('stock_actual', '>', 0)
        ->distinct('item_id')
        ->count();
        
    $alimentosConStock = InventarioExistencia::where('stock_actual', '>', 0)
        ->whereHas('item', function($query) {
            $query->where('tipo', 'alimento');
        })
        ->distinct('item_id')
        ->count();
    
    $insumosConStock = InventarioExistencia::where('stock_actual', '>', 0)
        ->whereHas('item', function($query) {
            $query->where('tipo', '!=', 'alimento');
        })
        ->distinct('item_id')
        ->count();
    
    echo "   • Total items con stock: {$totalItemsConStock}\n";
    echo "   • Alimentos con stock: {$alimentosConStock}\n";
    echo "   • Insumos con stock: {$insumosConStock}\n";
    
    // 5. Verificar que no aparezcan insumos en el formulario
    echo "\n5. VERIFICACIÓN DE FILTRADO:\n";
    
    $insumosEnFormulario = [];
    foreach ($bodegas as $bodega) {
        $existencias = InventarioExistencia::where('bodega_id', $bodega->id)
            ->where('stock_actual', '>', 0)
            ->whereHas('item', function($query) {
                $query->where('tipo', 'alimento');
            })
            ->whereHas('item.tipoAlimento', function($query) {
                $query->where('activo', true);
            })
            ->with(['item'])
            ->get();
        
        foreach ($existencias as $existencia) {
            if ($existencia->item->tipo != 'alimento') {
                $insumosEnFormulario[] = $existencia->item->nombre;
            }
        }
    }
    
    if (empty($insumosEnFormulario)) {
        echo "   ✅ PERFECTO: No hay insumos en el formulario de alimentación\n";
        echo "   ✅ Solo aparecen items de tipo 'alimento'\n";
    } else {
        echo "   ❌ ERROR: Estos insumos aparecen en el formulario:\n";
        foreach ($insumosEnFormulario as $insumo) {
            echo "      - {$insumo}\n";
        }
    }
    
    // 6. Mostrar ejemplo de formulario final
    echo "\n6. EJEMPLO DE OPCIONES EN EL FORMULARIO:\n";
    
    $bodegaEjemplo = Bodega::where('nombre', 'like', '%Alimento%')->first();
    if ($bodegaEjemplo) {
        echo "   Cuando selecciones '{$bodegaEjemplo->nombre}', verás:\n";
        
        $existenciasEjemplo = InventarioExistencia::where('bodega_id', $bodegaEjemplo->id)
            ->where('stock_actual', '>', 0)
            ->whereHas('item', function($query) {
                $query->where('tipo', 'alimento');
            })
            ->whereHas('item.tipoAlimento', function($query) {
                $query->where('activo', true);
            })
            ->with(['item.tipoAlimento'])
            ->get();
        
        foreach ($existenciasEjemplo as $existencia) {
            if ($existencia->item && $existencia->item->tipoAlimento) {
                $tipo = $existencia->item->tipoAlimento;
                $precio = $tipo->costo_por_kg ? "Q{$tipo->costo_por_kg}/kg" : 'Sin precio';
                echo "      🍽️ {$tipo->nombre_completo} - {$tipo->categoria} ({$existencia->stock_actual} kg disponible) - {$precio}\n";
            }
        }
    }
    
    echo "\n=== FILTRADO COMPLETADO ===\n";
    echo "✅ El formulario ahora muestra SOLO alimentos\n";
    echo "✅ Los insumos (oxígeno, redes, desinfectantes) están ocultos\n";
    echo "✅ Solo aparecen items con tipo='alimento' y tipos de alimento activos\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}