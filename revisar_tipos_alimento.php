<?php

require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TipoAlimento;
use App\Models\InventarioItem;
use App\Models\InventarioExistencia;

echo "=== REVISIÓN DE TIPOS DE ALIMENTO VS INVENTARIO ===\n\n";

try {
    // 1. Mostrar todos los tipos de alimento
    echo "1. TIPOS DE ALIMENTO EN EL SISTEMA:\n";
    $tiposAlimento = TipoAlimento::where('activo', true)->get();
    
    foreach ($tiposAlimento as $tipo) {
        echo "   • ID: {$tipo->id} - {$tipo->nombre_completo}\n";
        echo "     Marca: {$tipo->marca}\n";
        echo "     Nombre: {$tipo->nombre}\n";
        echo "     Categoría: {$tipo->categoria}\n";
        echo "     Inventario Item ID: " . ($tipo->inventario_item_id ?? 'NO CONECTADO') . "\n";
        echo "     Estado: " . ($tipo->activo ? 'Activo' : 'Inactivo') . "\n";
        echo "     -------------------------\n";
    }
    
    // 2. Mostrar items de inventario
    echo "\n2. ITEMS EN EL INVENTARIO:\n";
    $inventarioItems = InventarioItem::with(['tipoAlimento', 'existencias'])->get();
    
    foreach ($inventarioItems as $item) {
        echo "   • ID: {$item->id} - {$item->nombre}\n";
        echo "     SKU: {$item->sku}\n";
        echo "     Tipo: {$item->tipo}\n";
        echo "     Conectado a TipoAlimento: " . ($item->tipoAlimento ? $item->tipoAlimento->nombre_completo : 'NO CONECTADO') . "\n";
        
        $stockTotal = $item->existencias->sum('stock_actual');
        echo "     Stock Total: {$stockTotal} {$item->unidad_base}\n";
        echo "     -------------------------\n";
    }
    
    // 3. Verificar conexiones faltantes
    echo "\n3. ANÁLISIS DE CONEXIONES:\n";
    
    $tiposSinInventario = TipoAlimento::where('activo', true)
        ->whereNull('inventario_item_id')
        ->get();
    
    if ($tiposSinInventario->count() > 0) {
        echo "   ❌ TIPOS DE ALIMENTO SIN CONEXIÓN AL INVENTARIO:\n";
        foreach ($tiposSinInventario as $tipo) {
            echo "      - {$tipo->nombre_completo}\n";
        }
    } else {
        echo "   ✓ Todos los tipos de alimento activos están conectados al inventario\n";
    }
    
    $inventarioSinTipo = InventarioItem::whereDoesntHave('tipoAlimento')->get();
    
    if ($inventarioSinTipo->count() > 0) {
        echo "\n   ❌ ITEMS DE INVENTARIO SIN CONEXIÓN A TIPO DE ALIMENTO:\n";
        foreach ($inventarioSinTipo as $item) {
            echo "      - {$item->nombre} (SKU: {$item->sku})\n";
        }
    } else {
        echo "\n   ✓ Todos los items de inventario están conectados a tipos de alimento\n";
    }
    
    // 4. Mostrar lo que debería aparecer en el formulario
    echo "\n4. LO QUE DEBERÍA APARECER EN EL FORMULARIO:\n";
    $existenciasConStock = InventarioExistencia::where('stock_actual', '>', 0)
        ->with(['item.tipoAlimento', 'bodega'])
        ->get()
        ->groupBy('bodega_id');
    
    foreach ($existenciasConStock as $bodegaId => $existencias) {
        $bodegaNombre = $existencias->first()->bodega->nombre;
        echo "   BODEGA: {$bodegaNombre}\n";
        
        foreach ($existencias as $existencia) {
            if ($existencia->item && $existencia->item->tipoAlimento) {
                $tipo = $existencia->item->tipoAlimento;
                echo "      → {$tipo->nombre_completo} ({$existencia->stock_actual} kg disponible)\n";
            } else {
                echo "      → {$existencia->item->nombre} (SIN TIPO DE ALIMENTO CONECTADO)\n";
            }
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}