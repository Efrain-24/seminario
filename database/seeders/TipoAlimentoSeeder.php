<?php

namespace Database\Seeders;

use App\Models\TipoAlimento;
use Illuminate\Database\Seeder;

class TipoAlimentoSeeder extends Seeder
{
    public function run(): void
    {
        $tiposAlimento = [
            [
                'nombre' => 'Purina Trucha Inicio',
                'marca' => 'Purina',
                'categoria' => 'pellet',
                'proteina' => 45.0,
                'grasa' => 12.0,
                'fibra' => 3.5,
                'presentacion' => 'sacos',
                'peso_presentacion' => 25.0,
                'costo_por_kg' => 24.50,
                'descripcion' => 'Alimento especializado para alevines de trucha en etapa de iniciación',
                'activo' => true
            ],
            [
                'nombre' => 'Purina Trucha Crecimiento',
                'marca' => 'Purina',
                'categoria' => 'pellet',
                'proteina' => 42.0,
                'grasa' => 15.0,
                'fibra' => 4.0,
                'presentacion' => 'sacos',
                'peso_presentacion' => 25.0,
                'costo_por_kg' => 22.00,
                'descripcion' => 'Alimento para truchas juveniles en etapa de crecimiento',
                'activo' => true
            ],
            [
                'nombre' => 'Purina Trucha Engorde',
                'marca' => 'Purina',
                'categoria' => 'pellet',
                'proteina' => 38.0,
                'grasa' => 18.0,
                'fibra' => 4.5,
                'presentacion' => 'sacos',
                'peso_presentacion' => 25.0,
                'costo_por_kg' => 20.00,
                'descripcion' => 'Alimento para truchas adultas en etapa de engorde',
                'activo' => true
            ],
            [
                'nombre' => 'Nicovita Tilapia Inicio',
                'marca' => 'Nicovita',
                'categoria' => 'pellet',
                'proteina' => 40.0,
                'grasa' => 8.0,
                'fibra' => 5.0,
                'presentacion' => 'sacos',
                'peso_presentacion' => 20.0,
                'costo_por_kg' => 18.50,
                'descripcion' => 'Alimento para alevines de tilapia',
                'activo' => true
            ],
            [
                'nombre' => 'Nicovita Tilapia Crecimiento',
                'marca' => 'Nicovita',
                'categoria' => 'pellet',
                'proteina' => 35.0,
                'grasa' => 10.0,
                'fibra' => 6.0,
                'presentacion' => 'sacos',
                'peso_presentacion' => 20.0,
                'costo_por_kg' => 16.00,
                'descripcion' => 'Alimento para tilapias juveniles',
                'activo' => true
            ],
            [
                'nombre' => 'Nicovita Tilapia Engorde',
                'marca' => 'Nicovita',
                'categoria' => 'pellet',
                'proteina' => 30.0,
                'grasa' => 12.0,
                'fibra' => 7.0,
                'presentacion' => 'sacos',
                'peso_presentacion' => 20.0,
                'costo_por_kg' => 14.50,
                'descripcion' => 'Alimento para tilapias adultas',
                'activo' => true
            ],
            [
                'nombre' => 'Malta Cleyton Carpa Inicio',
                'marca' => 'Malta Cleyton',
                'categoria' => 'concentrado',
                'proteina' => 38.0,
                'grasa' => 9.0,
                'fibra' => 4.0,
                'presentacion' => 'bolsas',
                'peso_presentacion' => 15.0,
                'costo_por_kg' => 19.00,
                'descripcion' => 'Alimento para alevines de carpa',
                'activo' => true
            ],
            [
                'nombre' => 'Malta Cleyton Carpa Crecimiento',
                'marca' => 'Malta Cleyton',
                'categoria' => 'concentrado',
                'proteina' => 32.0,
                'grasa' => 11.0,
                'fibra' => 5.5,
                'presentacion' => 'bolsas',
                'peso_presentacion' => 15.0,
                'costo_por_kg' => 17.00,
                'descripcion' => 'Alimento para carpas juveniles',
                'activo' => true
            ],
            [
                'nombre' => 'Alimento Artesanal Mix',
                'marca' => 'Producción Local',
                'categoria' => 'artesanal',
                'proteina' => 25.0,
                'grasa' => 8.0,
                'fibra' => 10.0,
                'presentacion' => 'granel',
                'peso_presentacion' => 50.0,
                'costo_por_kg' => 12.00,
                'descripcion' => 'Mezcla artesanal de granos y harina de pescado',
                'activo' => true
            ],
            [
                'nombre' => 'Concentrado Reproductores',
                'marca' => 'AquaNutri',
                'categoria' => 'concentrado',
                'proteina' => 50.0,
                'grasa' => 16.0,
                'fibra' => 2.5,
                'presentacion' => 'sacos',
                'peso_presentacion' => 25.0,
                'costo_por_kg' => 32.00,
                'descripcion' => 'Alimento especializado para reproductores',
                'activo' => true
            ]
        ];

        foreach ($tiposAlimento as $tipo) {
            TipoAlimento::updateOrCreate(
                ['nombre' => $tipo['nombre'], 'marca' => $tipo['marca']],
                $tipo
            );
        }

        $totalTipos = TipoAlimento::count();
        $this->command->info("✅ Tipos de alimento procesados: {$totalTipos} total en la base de datos.");
    }
}
