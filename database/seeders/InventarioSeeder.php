<?php

namespace Database\Seeders;

use App\Models\InventarioItem;
use App\Models\Bodega;
use Illuminate\Database\Seeder;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existen datos
        $bodegasExistentes = Bodega::count();
        if ($bodegasExistentes > 0) {
            $this->command->info("✅ Ya existen {$bodegasExistentes} bodegas en la base de datos.");
            $this->command->info("✅ Ya existen " . InventarioItem::count() . " items de inventario en la base de datos.");
            return;
        }

        // Crear bodegas
        $bodegas = [
            [
                'nombre' => 'Bodega Principal',
                'ubicacion' => 'Instalación Central - Bloque A',
            ],
            [
                'nombre' => 'Bodega de Medicamentos',
                'ubicacion' => 'Área Veterinaria',
            ],
            [
                'nombre' => 'Bodega de Equipos', 
                'ubicacion' => 'Taller de Mantenimiento',
            ]
        ];

        foreach ($bodegas as $bodegaData) {
            Bodega::updateOrCreate(
                ['nombre' => $bodegaData['nombre']],
                $bodegaData
            );
        }

        // Crear items de inventario
        $items = [
            // Alimentos
            [
                'nombre' => 'Alimento Concentrado Premium',
                'sku' => 'ALM001',
                'tipo' => 'alimento',
                'unidad_base' => 'kg',
                'stock_minimo' => 100.0,
                'descripcion' => 'Alimento balanceado para peces adultos, alto en proteína',
            ],
            [
                'nombre' => 'Alimento Juvenil Pellets',
                'sku' => 'ALM002',
                'tipo' => 'alimento',
                'unidad_base' => 'kg',
                'stock_minimo' => 50.0,
                'descripcion' => 'Pellets especializados para peces juveniles',
            ],
            // Insumos
            [
                'nombre' => 'Redes de Pesca',
                'sku' => 'INS001',
                'tipo' => 'insumo',
                'unidad_base' => 'unidad',
                'stock_minimo' => 3.0,
                'descripcion' => 'Redes especializadas para manejo de peces',
            ],
            [
                'nombre' => 'Tubería PVC 4 pulgadas',
                'sku' => 'INS002',
                'tipo' => 'insumo',
                'unidad_base' => 'unidad',
                'stock_minimo' => 20.0,
                'descripcion' => 'Tubería para sistemas de recirculación',
            ],
            [
                'nombre' => 'Oxímetro Digital',
                'sku' => 'EQP002',
                'tipo' => 'insumo',
                'unidad_base' => 'unidad',
                'stock_minimo' => 1.0,
                'descripcion' => 'Medidor de oxígeno disuelto en agua',
            ]
        ];

        $itemsCreados = [];
        foreach ($items as $itemData) {
            $item = InventarioItem::updateOrCreate(
                ['sku' => $itemData['sku']],
                $itemData
            );
            $itemsCreados[] = $item;
        }

        $this->command->info('✅ Seeders de inventario creados:');
        $this->command->info('  - ' . count($bodegas) . ' bodegas');
        $this->command->info('  - ' . count($itemsCreados) . ' items');
    }
}
