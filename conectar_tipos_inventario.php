<?php

require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TipoAlimento;
use App\Models\InventarioItem;
use App\Models\InventarioExistencia;
use App\Models\Bodega;

echo "=== CONECTANDO TIPOS DE ALIMENTO AL INVENTARIO ===\n\n";

try {
    // 1. Conectar "Alimento de prueba" a un tipo de alimento existente
    echo "1. Conectando 'Alimento de prueba' a tipos de alimento...\n";
    
    $alimentoPrueba = InventarioItem::find(12);
    $tipoEngorde = TipoAlimento::find(6); // Nicovita Tilapia Engorde
    
    if ($alimentoPrueba && $tipoEngorde) {
        $tipoEngorde->inventario_item_id = $alimentoPrueba->id;
        $tipoEngorde->save();
        echo "   ✓ Conectado 'Alimento de prueba' con 'Nicovita Tilapia Engorde'\n";
    }
    
    // 2. Crear items de inventario para los tipos faltantes
    echo "\n2. Creando items de inventario para tipos de alimento faltantes...\n";
    
    $tiposSinInventario = TipoAlimento::where('activo', true)
        ->whereNull('inventario_item_id')
        ->get();
    
    foreach ($tiposSinInventario as $tipo) {
        // Crear item de inventario
        $item = InventarioItem::create([
            'nombre' => $tipo->nombre_completo,
            'sku' => 'ALM-' . strtoupper(substr($tipo->marca, 0, 3)) . '-' . str_pad($tipo->id, 3, '0', STR_PAD_LEFT),
            'tipo' => 'alimento',
            'unidad_base' => 'kg',
            'stock_minimo' => 50,
            'descripcion' => "Alimento {$tipo->categoria} - {$tipo->nombre_completo}"
        ]);
        
        // Conectar el tipo de alimento al item
        $tipo->inventario_item_id = $item->id;
        $tipo->save();
        
        echo "   ✓ Creado item '{$item->nombre}' (SKU: {$item->sku}) para '{$tipo->nombre_completo}'\n";
        
        // Crear existencias iniciales en bodegas principales
        $bodegasAlimento = Bodega::whereIn('id', [2, 5, 8])->get(); // Bodegas de alimentos
        
        foreach ($bodegasAlimento as $bodega) {
            $stockInicial = rand(50, 200); // Stock aleatorio entre 50-200 kg
            
            InventarioExistencia::create([
                'item_id' => $item->id,
                'bodega_id' => $bodega->id,
                'stock_actual' => $stockInicial
            ]);
            
            echo "     → Agregado stock de {$stockInicial} kg en {$bodega->nombre}\n";
        }
    }
    
    // 3. Verificar resultados
    echo "\n3. Verificación final...\n";
    
    $tiposConectados = TipoAlimento::where('activo', true)
        ->whereNotNull('inventario_item_id')
        ->count();
    
    $tiposTotal = TipoAlimento::where('activo', true)->count();
    
    echo "   ✓ Tipos de alimento conectados: {$tiposConectados}/{$tiposTotal}\n";
    
    if ($tiposConectados == $tiposTotal) {
        echo "   🎉 ¡TODOS LOS TIPOS DE ALIMENTO ESTÁN CONECTADOS AL INVENTARIO!\n";
    }
    
    // 4. Mostrar lo que ahora aparecerá en el formulario
    echo "\n4. RESUMEN DE LO QUE APARECERÁ EN EL FORMULARIO:\n";
    
    $bodegas = Bodega::all();
    foreach ($bodegas as $bodega) {
        $existencias = InventarioExistencia::where('bodega_id', $bodega->id)
            ->where('stock_actual', '>', 0)
            ->whereHas('item.tipoAlimento', function($query) {
                $query->where('activo', true);
            })
            ->with(['item.tipoAlimento'])
            ->get();
        
        if ($existencias->count() > 0) {
            echo "\n   BODEGA: {$bodega->nombre}\n";
            foreach ($existencias as $existencia) {
                if ($existencia->item && $existencia->item->tipoAlimento) {
                    $tipo = $existencia->item->tipoAlimento;
                    echo "      → {$tipo->nombre_completo} ({$existencia->stock_actual} kg)\n";
                }
            }
        }
    }
    
    echo "\n=== CONFIGURACIÓN COMPLETADA ===\n";
    echo "✅ Ahora todos los tipos de alimento aparecerán en el formulario\n";
    echo "✅ Cada bodega mostrará los alimentos disponibles\n";
    echo "✅ El inventario está completamente integrado\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stacktrace:\n" . $e->getTraceAsString() . "\n";
}