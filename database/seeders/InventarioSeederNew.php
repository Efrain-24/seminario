<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarioSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear bodegas
        $bodegas = [
            [
                'nombre' => 'Bodega Principal',
                'ubicacion' => 'Instalación Central - Bloque A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Bodega de Alimentos',
                'ubicacion' => 'Instalación Central - Bloque B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Bodega de Insumos',
                'ubicacion' => 'Instalación Central - Bloque C',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('bodegas')->insert($bodegas);

        // Crear items de inventario
        $items = [
            [
                'nombre' => 'Alimento Premium Para Truchas',
                'sku' => 'ALM-PREM-001',
                'tipo' => 'alimento',
                'unidad_base' => 'kg',
                'stock_minimo' => 100.000,
                'descripcion' => 'Alimento balanceado premium para truchas en etapa de crecimiento',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Alimento Iniciación',
                'sku' => 'ALM-INIT-001',
                'tipo' => 'alimento',
                'unidad_base' => 'kg',
                'stock_minimo' => 50.000,
                'descripcion' => 'Alimento especial para alevinos y peces juveniles',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Oxígeno Líquido',
                'sku' => 'INS-OXI-001',
                'tipo' => 'insumo',
                'unidad_base' => 'litro',
                'stock_minimo' => 20.000,
                'descripcion' => 'Oxígeno líquido para oxigenación de estanques',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Red de Pesca',
                'sku' => 'INS-RED-001',
                'tipo' => 'insumo',
                'unidad_base' => 'unidad',
                'stock_minimo' => 2.000,
                'descripcion' => 'Red de pesca profesional para manejo de peces',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Desinfectante Acuícola',
                'sku' => 'INS-DES-001',
                'tipo' => 'insumo',
                'unidad_base' => 'litro',
                'stock_minimo' => 10.000,
                'descripcion' => 'Desinfectante especializado para instalaciones acuícolas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('inventario_items')->insert($items);

        // Obtener IDs creados
        $bodegaIds = DB::table('bodegas')->pluck('id');
        $itemIds = DB::table('inventario_items')->pluck('id');

        // Crear existencias iniciales
        $existencias = [
            [
                'item_id' => $itemIds[0], // Alimento Premium
                'bodega_id' => $bodegaIds[1], // Bodega de Alimentos
                'stock_actual' => 250.500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => $itemIds[1], // Alimento Iniciación
                'bodega_id' => $bodegaIds[1], // Bodega de Alimentos
                'stock_actual' => 150.750,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => $itemIds[2], // Oxígeno Líquido
                'bodega_id' => $bodegaIds[2], // Bodega de Insumos
                'stock_actual' => 45.000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => $itemIds[3], // Red de Pesca
                'bodega_id' => $bodegaIds[2], // Bodega de Insumos
                'stock_actual' => 8.000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => $itemIds[4], // Desinfectante
                'bodega_id' => $bodegaIds[2], // Bodega de Insumos
                'stock_actual' => 25.000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Algunos items también en bodega principal
            [
                'item_id' => $itemIds[0], // Alimento Premium
                'bodega_id' => $bodegaIds[0], // Bodega Principal
                'stock_actual' => 75.000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('inventario_existencias')->insert($existencias);

        $this->command->info('✅ Sistema de inventario creado exitosamente:');
        $this->command->info('  - ' . count($bodegas) . ' bodegas');
        $this->command->info('  - ' . count($items) . ' items');
        $this->command->info('  - ' . count($existencias) . ' registros de existencias');
    }
}
