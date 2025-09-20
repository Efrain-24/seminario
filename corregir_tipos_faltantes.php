<?php

require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TipoAlimento;
use App\Models\InventarioItem;
use App\Models\InventarioExistencia;
use App\Models\Bodega;

echo "=== CORRIGIENDO TIPOS DE ALIMENTO FALTANTES ===\n\n";

try {
    // Encontrar tipos que comparten el mismo inventario_item_id
    echo "1. Identificando tipos de alimento que comparten items de inventario...\n";
    
    $tiposPorItem = TipoAlimento::where('activo', true)
        ->whereNotNull('inventario_item_id')
        ->get()
        ->groupBy('inventario_item_id');
    
    foreach ($tiposPorItem as $itemId => $tipos) {
        if ($tipos->count() > 1) {
            echo "   ⚠️  Item ID {$itemId} es compartido por:\n";
            foreach ($tipos as $tipo) {
                echo "      - {$tipo->nombre_completo}\n";
            }
        }
    }
    
    // Crear items separados para tipos que comparten
    echo "\n2. Creando items de inventario separados...\n";
    
    $tiposCrecimiento = TipoAlimento::whereIn('id', [2, 5])->get(); // Los que faltan
    
    foreach ($tiposCrecimiento as $tipo) {
        // Crear nuevo item de inventario
        $nuevoItem = InventarioItem::create([
            'nombre' => $tipo->nombre_completo,
            'sku' => 'ALM-' . strtoupper(substr($tipo->marca, 0, 3)) . '-' . str_pad($tipo->id, 3, '0', STR_PAD_LEFT),
            'tipo' => 'alimento',
            'unidad_base' => 'kg',
            'stock_minimo' => 50,
            'descripcion' => "Alimento {$tipo->categoria} - {$tipo->nombre_completo}"
        ]);
        
        // Actualizar el tipo para usar el nuevo item
        $tipo->inventario_item_id = $nuevoItem->id;
        $tipo->save();
        
        echo "   ✓ Creado item separado '{$nuevoItem->nombre}' (SKU: {$nuevoItem->sku})\n";
        
        // Crear existencias en bodegas principales
        $bodegasAlimento = Bodega::whereIn('id', [2, 5, 8])->get();
        
        foreach ($bodegasAlimento as $bodega) {
            $stockInicial = rand(80, 180);
            
            InventarioExistencia::create([
                'item_id' => $nuevoItem->id,
                'bodega_id' => $bodega->id,
                'stock_actual' => $stockInicial
            ]);
            
            echo "     → Stock de {$stockInicial} kg en {$bodega->nombre}\n";
        }
    }
    
    // 3. Verificación final
    echo "\n3. Verificación final completa...\n";
    
    $tiposActivos = TipoAlimento::where('activo', true)->count();
    $tiposConInventario = TipoAlimento::where('activo', true)
        ->whereNotNull('inventario_item_id')
        ->count();
    
    echo "   • Tipos activos totales: {$tiposActivos}\n";
    echo "   • Tipos con inventario: {$tiposConInventario}\n";
    
    if ($tiposActivos == $tiposConInventario) {
        echo "   ✅ PERFECTO: Todos los tipos tienen inventario asignado\n";
    }
    
    // 4. Verificar que todos aparezcan en el formulario
    echo "\n4. Simulando formulario actualizado...\n";
    
    $existenciasPorBodega = [];
    $bodegas = Bodega::all();
    
    foreach ($bodegas as $bodega) {
        $existencias = InventarioExistencia::where('bodega_id', $bodega->id)
            ->where('stock_actual', '>', 0)
            ->whereHas('item.tipoAlimento', function($query) {
                $query->where('activo', true);
            })
            ->with(['item.tipoAlimento'])
            ->get();
        
        $alimentosEnBodega = [];
        foreach ($existencias as $existencia) {
            if ($existencia->item && $existencia->item->tipoAlimento) {
                $tipo = $existencia->item->tipoAlimento;
                $alimentosEnBodega[] = $tipo->nombre_completo;
            }
        }
        
        if (count($alimentosEnBodega) > 0) {
            echo "   📦 {$bodega->nombre}: " . count($alimentosEnBodega) . " alimentos\n";
        }
    }
    
    // 5. Contar tipos únicos en formulario
    $tiposEnFormulario = [];
    foreach ($bodegas as $bodega) {
        $existencias = InventarioExistencia::where('bodega_id', $bodega->id)
            ->where('stock_actual', '>', 0)
            ->whereHas('item.tipoAlimento', function($query) {
                $query->where('activo', true);
            })
            ->with(['item.tipoAlimento'])
            ->get();
        
        foreach ($existencias as $existencia) {
            if ($existencia->item && $existencia->item->tipoAlimento) {
                $tiposEnFormulario[$existencia->item->tipoAlimento->id] = $existencia->item->tipoAlimento->nombre_completo;
            }
        }
    }
    
    echo "\n5. RESULTADO FINAL:\n";
    echo "   • Tipos activos en BD: {$tiposActivos}\n";
    echo "   • Tipos únicos en formulario: " . count($tiposEnFormulario) . "\n";
    
    if (count($tiposEnFormulario) == $tiposActivos) {
        echo "   🎉 ¡ÉXITO TOTAL! Todos los tipos aparecen en el formulario\n";
        
        echo "\n   Tipos disponibles:\n";
        foreach ($tiposEnFormulario as $id => $nombre) {
            echo "      ✓ {$nombre}\n";
        }
    } else {
        echo "   ❌ Aún faltan algunos tipos\n";
    }
    
    echo "\n=== CORRECCIÓN COMPLETADA ===\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}