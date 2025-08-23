<?php

namespace Database\Seeders;

use App\Models\InventarioItem;
use App\Models\Bodega;
use App\Models\InventarioExistencia;
use App\Models\InventarioMovimiento;
use App\Models\InventarioLote;
use Illuminate\Database\Seeder;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        // Crear bodegas
        $bodegas = [
            [
                'nombre' => 'Bodega Principal',
                'codigo' => 'BP001',
                'ubicacion' => 'Instalación Central - Bloque A',
                'descripcion' => 'Bodega principal para almacenamiento de alimentos y suministros',
                'capacidad_maxima' => 1000.00,
                'temperatura_min' => 15.0,
                'temperatura_max' => 25.0,
                'humedad_max' => 65.0,
                'activa' => true
            ],
            [
                'nombre' => 'Bodega de Medicamentos',
                'codigo' => 'BM002',
                'ubicacion' => 'Área Veterinaria',
                'descripcion' => 'Almacenamiento especializado para medicamentos y productos veterinarios',
                'capacidad_maxima' => 200.00,
                'temperatura_min' => 2.0,
                'temperatura_max' => 8.0,
                'humedad_max' => 40.0,
                'activa' => true
            ],
            [
                'nombre' => 'Bodega de Equipos',
                'codigo' => 'BE003',
                'ubicacion' => 'Taller de Mantenimiento',
                'descripcion' => 'Herramientas, equipos y repuestos para mantenimiento',
                'capacidad_maxima' => 500.00,
                'temperatura_min' => 10.0,
                'temperatura_max' => 40.0,
                'humedad_max' => 80.0,
                'activa' => true
            ]
        ];

        foreach ($bodegas as $bodegaData) {
            Bodega::create($bodegaData);
        }

        // Crear items de inventario
        $items = [
            // Alimentos
            [
                'codigo' => 'ALM001',
                'nombre' => 'Alimento Concentrado Premium',
                'descripcion' => 'Alimento balanceado para peces adultos, alto en proteína',
                'categoria' => 'alimento',
                'unidad_medida' => 'kg',
                'precio_unitario' => 8500.00,
                'stock_minimo' => 100.0,
                'stock_maximo' => 1000.0,
                'activo' => true
            ],
            [
                'codigo' => 'ALM002',
                'nombre' => 'Alimento Juvenil Pellets',
                'descripcion' => 'Pellets especializados para peces juveniles',
                'categoria' => 'alimento',
                'unidad_medida' => 'kg',
                'precio_unitario' => 9200.00,
                'stock_minimo' => 50.0,
                'stock_maximo' => 500.0,
                'activo' => true
            ],
            // Medicamentos
            [
                'codigo' => 'MED001',
                'nombre' => 'Antibiótico Acuícola',
                'descripcion' => 'Tratamiento para infecciones bacterianas en peces',
                'categoria' => 'medicamento',
                'unidad_medida' => 'unidad',
                'precio_unitario' => 45000.00,
                'stock_minimo' => 5.0,
                'stock_maximo' => 50.0,
                'activo' => true
            ],
            [
                'codigo' => 'MED002',
                'nombre' => 'Desinfectante para Estanques',
                'descripcion' => 'Desinfectante especializado para sistemas acuícolas',
                'categoria' => 'medicamento',
                'unidad_medida' => 'litro',
                'precio_unitario' => 25000.00,
                'stock_minimo' => 10.0,
                'stock_maximo' => 100.0,
                'activo' => true
            ],
            // Equipos
            [
                'codigo' => 'EQP001',
                'nombre' => 'Bomba de Agua Sumergible',
                'descripcion' => 'Bomba para recirculación de agua en estanques',
                'categoria' => 'equipo',
                'unidad_medida' => 'unidad',
                'precio_unitario' => 350000.00,
                'stock_minimo' => 2.0,
                'stock_maximo' => 10.0,
                'activo' => true
            ],
            [
                'codigo' => 'EQP002',
                'nombre' => 'Oxímetro Digital',
                'descripcion' => 'Medidor de oxígeno disuelto en agua',
                'categoria' => 'equipo',
                'unidad_medida' => 'unidad',
                'precio_unitario' => 180000.00,
                'stock_minimo' => 1.0,
                'stock_maximo' => 5.0,
                'activo' => true
            ],
            // Insumos
            [
                'codigo' => 'INS001',
                'nombre' => 'Redes de Pesca',
                'descripcion' => 'Redes especializadas para manejo de peces',
                'categoria' => 'insumo',
                'unidad_medida' => 'unidad',
                'precio_unitario' => 75000.00,
                'stock_minimo' => 3.0,
                'stock_maximo' => 20.0,
                'activo' => true
            ],
            [
                'codigo' => 'INS002',
                'nombre' => 'Tubería PVC 4 pulgadas',
                'descripción' => 'Tubería para sistemas de recirculación',
                'categoria' => 'insumo',
                'unidad_medida' => 'metro',
                'precio_unitario' => 15000.00,
                'stock_minimo' => 20.0,
                'stock_maximo' => 200.0,
                'activo' => true
            ]
        ];

        $itemsCreados = [];
        foreach ($items as $itemData) {
            $item = InventarioItem::create($itemData);
            $itemsCreados[] = $item;
        }

        // Crear existencias iniciales
        $bodegas = Bodega::all();
        foreach ($itemsCreados as $item) {
            $bodega = $bodegas->random();
            $cantidadInicial = rand(50, 200);

            InventarioExistencia::create([
                'inventario_item_id' => $item->id,
                'bodega_id' => $bodega->id,
                'cantidad_actual' => $cantidadInicial,
                'costo_promedio' => $item->precio_unitario
            ]);

            // Crear lote de inventario
            InventarioLote::create([
                'inventario_item_id' => $item->id,
                'codigo_lote' => 'LT' . str_pad($item->id, 4, '0', STR_PAD_LEFT) . date('m'),
                'fecha_ingreso' => now()->subDays(rand(1, 30)),
                'fecha_vencimiento' => $item->categoria === 'alimento' ? now()->addMonths(6) : 
                                     ($item->categoria === 'medicamento' ? now()->addYears(2) : null),
                'cantidad_inicial' => $cantidadInicial,
                'cantidad_actual' => $cantidadInicial,
                'precio_compra' => $item->precio_unitario,
                'proveedor' => 'Proveedor Demo S.A.S.',
                'numero_factura' => 'FC-' . rand(1000, 9999),
                'activo' => true
            ]);

            // Crear movimiento de entrada inicial
            InventarioMovimiento::create([
                'inventario_item_id' => $item->id,
                'bodega_id' => $bodega->id,
                'tipo_movimiento' => 'entrada',
                'cantidad' => $cantidadInicial,
                'precio_unitario' => $item->precio_unitario,
                'valor_total' => $cantidadInicial * $item->precio_unitario,
                'fecha_movimiento' => now()->subDays(rand(1, 15)),
                'motivo' => 'stock_inicial',
                'documento_referencia' => 'INIT-' . $item->codigo,
                'observaciones' => 'Inventario inicial del sistema',
                'usuario_id' => 1 // Asumiendo que existe un usuario con ID 1
            ]);
        }

        $this->command->info('✅ Seeders de inventario creados:');
        $this->command->info('  - ' . count($bodegas) . ' bodegas');
        $this->command->info('  - ' . count($itemsCreados) . ' items');
        $this->command->info('  - ' . count($itemsCreados) . ' existencias');
        $this->command->info('  - ' . count($itemsCreados) . ' lotes');
        $this->command->info('  - ' . count($itemsCreados) . ' movimientos iniciales');
    }
}
