<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarioSeederNew extends Seeder
{
    public function run(): void
    {
        // 1) BODEGAS (clave candidata: nombre)
        $bodegas = [
            ['nombre' => 'Bodega Principal',    'ubicacion' => 'Instalación Central - Bloque A', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Bodega de Alimentos', 'ubicacion' => 'Instalación Central - Bloque B', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Bodega de Insumos',   'ubicacion' => 'Instalación Central - Bloque C', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('bodegas')->upsert(
            $bodegas,
            ['nombre'],                 // columnas para detectar duplicado
            ['ubicacion','updated_at']  // columnas a actualizar si existe
        );

        // 2) ITEMS (clave única real: sku)
        $items = [
            [
                'nombre' => 'Alimento Premium Para Truchas',
                'sku' => 'ALM-PREM-001',
                'tipo' => 'alimento',
                'unidad_base' => 'kg',
                'stock_minimo' => 100,
                'descripcion' => 'Alimento balanceado premium para truchas en etapa de crecimiento',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Alimento Iniciación',
                'sku' => 'ALM-INIT-001',
                'tipo' => 'alimento',
                'unidad_base' => 'kg',
                'stock_minimo' => 50,
                'descripcion' => 'Alimento especial para alevinos y peces juveniles',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Oxígeno Líquido',
                'sku' => 'INS-OXI-001',
                'tipo' => 'insumo',
                'unidad_base' => 'litro',
                'stock_minimo' => 20,
                'descripcion' => 'Oxígeno líquido para oxigenación de estanques',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Red de Pesca',
                'sku' => 'INS-RED-001',
                'tipo' => 'insumo',
                'unidad_base' => 'unidad',
                'stock_minimo' => 2,
                'descripcion' => 'Red de pesca profesional para manejo de peces',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Desinfectante Acuícola',
                'sku' => 'INS-DES-001',
                'tipo' => 'insumo',
                'unidad_base' => 'litro',
                'stock_minimo' => 10,
                'descripcion' => 'Desinfectante especializado para instalaciones acuícolas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('inventario_items')->upsert(
            $items,
            ['sku'],
            ['nombre','tipo','unidad_base','stock_minimo','descripcion','updated_at']
        );

        // 3) MAPAS DE IDS POR CLAVE NATURAL
        $bodegaIdByNombre = DB::table('bodegas')->pluck('id','nombre');           // ['Bodega Principal'=>1, ...]
        $itemIdBySku      = DB::table('inventario_items')->pluck('id','sku');     // ['ALM-PREM-001'=>X, ...]

        // 4) EXISTENCIAS INICIALES (idempotentes por par item_id+bodega_id)
        $existencias = [
            [
                'item_id'     => $itemIdBySku['ALM-PREM-001'],
                'bodega_id'   => $bodegaIdByNombre['Bodega de Alimentos'],
                'stock_actual'=> 250.500,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'item_id'     => $itemIdBySku['ALM-INIT-001'],
                'bodega_id'   => $bodegaIdByNombre['Bodega de Alimentos'],
                'stock_actual'=> 150.750,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'item_id'     => $itemIdBySku['INS-OXI-001'],
                'bodega_id'   => $bodegaIdByNombre['Bodega de Insumos'],
                'stock_actual'=> 45.000,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'item_id'     => $itemIdBySku['INS-RED-001'],
                'bodega_id'   => $bodegaIdByNombre['Bodega de Insumos'],
                'stock_actual'=> 8.000,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'item_id'     => $itemIdBySku['INS-DES-001'],
                'bodega_id'   => $bodegaIdByNombre['Bodega de Insumos'],
                'stock_actual'=> 25.000,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'item_id'     => $itemIdBySku['ALM-PREM-001'],
                'bodega_id'   => $bodegaIdByNombre['Bodega Principal'],
                'stock_actual'=> 75.000,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        DB::table('inventario_existencias')->upsert(
            $existencias,
            ['item_id','bodega_id'],    // clave compuesta para detectar duplicado
            ['stock_actual','updated_at']
        );

        $this->command->info('✅ Inventario sembrado/actualizado sin duplicados.');
        $this->command->info('  - ' . count($bodegas) . ' bodegas');
        $this->command->info('  - ' . count($items) . ' items');
        $this->command->info('  - ' . count($existencias) . ' existencias');
    }
}
