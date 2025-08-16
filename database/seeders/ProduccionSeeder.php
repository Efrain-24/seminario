<?php

namespace Database\Seeders;

use App\Models\UnidadProduccion;
use App\Models\Lote;
use Illuminate\Database\Seeder;

class ProduccionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear unidades de producción con tipos generales
        $unidades = [
            [
                'codigo' => 'TQ001',
                'nombre' => 'Tanque Principal 1',
                'tipo' => 'tanque',
                'capacidad_maxima' => 5000.00,
                'area' => 25.00,
                'profundidad' => 2.00,
                'estado' => 'activo',
                'descripcion' => 'Tanque principal para alevines y juveniles',
                'fecha_construccion' => '2023-01-15'
            ],
            [
                'codigo' => 'ES001',
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
                'codigo' => 'JL001',
                'nombre' => 'Jaula Flotante 1',
                'tipo' => 'jaula',
                'capacidad_maxima' => 8000.00,
                'area' => 64.00,
                'profundidad' => 3.00,
                'estado' => 'activo',
                'descripcion' => 'Jaula flotante para cultivo en lago',
                'fecha_construccion' => '2023-08-20'
            ],
            [
                'codigo' => 'SE001',
                'nombre' => 'Sistema RAS',
                'tipo' => 'sistema_especializado',
                'capacidad_maxima' => 10000.00,
                'area' => 50.00,
                'profundidad' => 2.50,
                'estado' => 'mantenimiento',
                'descripcion' => 'Sistema de recirculación acuícola',
                'fecha_construccion' => '2024-01-10',
                'ultimo_mantenimiento' => '2025-08-01'
            ]
        ];

        foreach ($unidades as $unidadData) {
            UnidadProduccion::updateOrCreate(
                ['codigo' => $unidadData['codigo']],
                $unidadData
            );
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
            Lote::updateOrCreate(
                ['codigo_lote' => $loteData['codigo_lote']],
                $loteData
            );
        }

        $this->command->info("✅ Datos de producción creados:");
        $this->command->info("- " . count($unidades) . " unidades de producción");
        $this->command->info("- " . count($lotes) . " lotes");
    }
}
