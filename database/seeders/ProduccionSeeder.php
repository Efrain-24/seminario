<?php

namespace Database\Seeders;

use App\Models\UnidadProduccion;
use App\Models\Lote;
use Illuminate\Database\Seeder;

class ProduccionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear unidades de producción
        $unidades = [
            [
                'codigo' => 'TQ-001',
                'nombre' => 'Tanque Principal 1',
                'tipo' => 'tanque',
                'capacidad_maxima' => 5000.00,
                'area' => 25.00,
                'profundidad' => 2.00,
                'estado' => 'activo',
                'descripcion' => 'Tanque principal para alevines',
                'fecha_construccion' => '2023-01-15'
            ],
            [
                'codigo' => 'TQ-002',
                'nombre' => 'Tanque Principal 2',
                'tipo' => 'tanque',
                'capacidad_maxima' => 5000.00,
                'area' => 25.00,
                'profundidad' => 2.00,
                'estado' => 'activo',
                'descripcion' => 'Tanque principal para juveniles',
                'fecha_construccion' => '2023-01-15'
            ],
            [
                'codigo' => 'EST-001',
                'nombre' => 'Estanque de Engorde 1',
                'tipo' => 'estanque',
                'capacidad_maxima' => 15000.00,
                'area' => 100.00,
                'profundidad' => 1.50,
                'estado' => 'activo',
                'descripcion' => 'Estanque para engorde de tilapias',
                'fecha_construccion' => '2022-06-10'
            ],
            [
                'codigo' => 'EST-002',
                'nombre' => 'Estanque de Engorde 2',
                'tipo' => 'estanque',
                'capacidad_maxima' => 15000.00,
                'area' => 100.00,
                'profundidad' => 1.50,
                'estado' => 'mantenimiento',
                'descripcion' => 'Estanque en mantenimiento',
                'fecha_construccion' => '2022-06-10',
                'ultimo_mantenimiento' => '2025-08-01'
            ]
        ];

        foreach ($unidades as $unidadData) {
            UnidadProduccion::create($unidadData);
        }

        // Crear lotes
        $lotes = [
            [
                'codigo_lote' => 'TIL-2025-001',
                'especie' => 'Tilapia Nilótica',
                'cantidad_inicial' => 2000,
                'cantidad_actual' => 1950,
                'peso_promedio_inicial' => 0.5,
                'talla_promedio_inicial' => 2.5,
                'fecha_inicio' => '2025-07-01',
                'unidad_produccion_id' => 1,
                'estado' => 'activo',
                'observaciones' => 'Lote de alevines en buen estado'
            ],
            [
                'codigo_lote' => 'TRU-2025-001',
                'especie' => 'Trucha Arcoíris',
                'cantidad_inicial' => 1500,
                'cantidad_actual' => 1480,
                'peso_promedio_inicial' => 1.2,
                'talla_promedio_inicial' => 4.0,
                'fecha_inicio' => '2025-06-15',
                'unidad_produccion_id' => 2,
                'estado' => 'activo',
                'observaciones' => 'Lote de juveniles, crecimiento normal'
            ],
            [
                'codigo_lote' => 'TIL-2025-002',
                'especie' => 'Tilapia Nilótica',
                'cantidad_inicial' => 5000,
                'cantidad_actual' => 4800,
                'peso_promedio_inicial' => 15.0,
                'talla_promedio_inicial' => 8.0,
                'fecha_inicio' => '2025-03-01',
                'unidad_produccion_id' => 3,
                'estado' => 'activo',
                'observaciones' => 'Lote en fase de engorde'
            ],
            [
                'codigo_lote' => 'TIL-2024-010',
                'especie' => 'Tilapia Nilótica',
                'cantidad_inicial' => 3000,
                'cantidad_actual' => 0,
                'peso_promedio_inicial' => 0.3,
                'talla_promedio_inicial' => 2.0,
                'fecha_inicio' => '2024-12-01',
                'unidad_produccion_id' => null,
                'estado' => 'cosechado',
                'observaciones' => 'Lote cosechado en julio 2025'
            ]
        ];

        foreach ($lotes as $loteData) {
            Lote::create($loteData);
        }

        echo "✅ Datos de producción creados:\n";
        echo "- " . count($unidades) . " unidades de producción\n";
        echo "- " . count($lotes) . " lotes\n";
    }
}
