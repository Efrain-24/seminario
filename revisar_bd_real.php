<?php

require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InventarioItem;
use App\Models\InventarioExistencia;
use App\Models\TipoAlimento;
use App\Models\Bodega;

echo "=== REVISIÓN REAL DE LA BASE DE DATOS ===\n\n";

try {
    // 1. Ver exactamente qué hay en inventario_items
    echo "1. ITEMS REALES EN INVENTARIO_ITEMS:\n";
    $items = InventarioItem::all();
    
    foreach ($items as $item) {
        echo "   ID: {$item->id}\n";
        echo "   Nombre: {$item->nombre}\n";
        echo "   SKU: {$item->sku}\n";
        echo "   Tipo: {$item->tipo}\n";
        echo "   Unidad: {$item->unidad_base}\n";
        echo "   ---\n";
    }
    
    // 2. Ver qué hay en inventario_existencias
    echo "\n2. EXISTENCIAS REALES EN INVENTARIO_EXISTENCIAS:\n";
    $existencias = InventarioExistencia::with(['item', 'bodega'])->get();
    
    foreach ($existencias as $existencia) {
        echo "   Item: {$existencia->item->nombre}\n";
        echo "   Bodega: {$existencia->bodega->nombre}\n";
        echo "   Stock: {$existencia->stock_actual} {$existencia->item->unidad_base}\n";
        echo "   Tipo Item: {$existencia->item->tipo}\n";
        echo "   ---\n";
    }
    
    // 3. Ver qué hay en tipo_alimentos
    echo "\n3. TIPOS DE ALIMENTO REALES:\n";
    $tipos = TipoAlimento::all();
    
    foreach ($tipos as $tipo) {
        echo "   ID: {$tipo->id}\n";
        echo "   Nombre: {$tipo->nombre_completo}\n";
        echo "   Inventario Item ID: " . ($tipo->inventario_item_id ?? 'NULL') . "\n";
        echo "   Activo: " . ($tipo->activo ? 'Sí' : 'No') . "\n";
        echo "   ---\n";
    }
    
    // 4. Ver las conexiones reales
    echo "\n4. CONEXIONES REALES ENTRE TIPO_ALIMENTOS E INVENTARIO:\n";
    $tiposConInventario = TipoAlimento::whereNotNull('inventario_item_id')
        ->with('inventarioItem')
        ->get();
    
    if ($tiposConInventario->isEmpty()) {
        echo "   ❌ NO HAY CONEXIONES ENTRE TIPO_ALIMENTOS E INVENTARIO\n";
        echo "   Esto explica por qué no aparecen alimentos en el formulario.\n";
    } else {
        foreach ($tiposConInventario as $tipo) {
            echo "   Tipo Alimento: {$tipo->nombre_completo}\n";
            echo "   → Conectado a Item: {$tipo->inventarioItem->nombre}\n";
            echo "   → Tipo Item: {$tipo->inventarioItem->tipo}\n";
            echo "   ---\n";
        }
    }
    
    // 5. Ver qué items de inventario son de tipo 'alimento'
    echo "\n5. ITEMS DE INVENTARIO DE TIPO 'ALIMENTO':\n";
    $alimentosInventario = InventarioItem::where('tipo', 'alimento')->get();
    
    if ($alimentosInventario->isEmpty()) {
        echo "   ❌ NO HAY ITEMS DE TIPO 'ALIMENTO' EN INVENTARIO\n";
    } else {
        foreach ($alimentosInventario as $item) {
            echo "   • {$item->nombre} (SKU: {$item->sku})\n";
            
            // Ver si tiene existencias
            $stockTotal = $item->existencias->sum('stock_actual');
            echo "     Stock total: {$stockTotal} {$item->unidad_base}\n";
            
            // Ver si está conectado a algún tipo de alimento
            $tipoConectado = TipoAlimento::where('inventario_item_id', $item->id)->first();
            if ($tipoConectado) {
                echo "     Conectado a: {$tipoConectado->nombre_completo}\n";
            } else {
                echo "     ❌ NO CONECTADO A NINGÚN TIPO DE ALIMENTO\n";
            }
            echo "   ---\n";
        }
    }
    
    // 6. Diagnóstico del problema
    echo "\n6. DIAGNÓSTICO DEL PROBLEMA:\n";
    
    $totalItems = InventarioItem::count();
    $itemsAlimento = InventarioItem::where('tipo', 'alimento')->count();
    $tiposAlimento = TipoAlimento::where('activo', true)->count();
    $conexiones = TipoAlimento::whereNotNull('inventario_item_id')->count();
    
    echo "   • Total items en inventario: {$totalItems}\n";
    echo "   • Items de tipo 'alimento': {$itemsAlimento}\n";
    echo "   • Tipos de alimento activos: {$tiposAlimento}\n";
    echo "   • Conexiones inventario-tipos: {$conexiones}\n";
    
    if ($conexiones == 0) {
        echo "\n   🔴 PROBLEMA IDENTIFICADO:\n";
        echo "   No hay conexiones entre los tipos de alimento y los items de inventario.\n";
        echo "   Por eso el formulario está vacío.\n";
        
        echo "\n   📋 SOLUCIÓN NECESARIA:\n";
        echo "   1. Conectar cada tipo de alimento con su item de inventario correspondiente\n";
        echo "   2. Asegurar que los items de inventario tengan tipo='alimento'\n";
        echo "   3. Actualizar el campo 'inventario_item_id' en la tabla tipo_alimentos\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stacktrace:\n" . $e->getTraceAsString() . "\n";
}