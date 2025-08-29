<?php

namespace Database\Seeders;

use App\Models\CosechaParcial;
use App\Models\Lote;
use Illuminate\Database\Seeder;

class CosechaParcialSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existen datos
        $registrosExistentes = CosechaParcial::count();
        if ($registrosExistentes > 0) {
            $this->command->info("✅ Ya existen {$registrosExistentes} registros de cosecha parcial en la base de datos.");
            return;
        }

        // Obtener lotes existentes
        $lotes = Lote::all();

        if ($lotes->isEmpty()) {
            $this->command->warn('No hay lotes disponibles para crear cosechas parciales');
            return;
        }

        $cosechasParciales = [
            [
                'lote_id' => $lotes->first()->id,
                'fecha' => '2025-07-15',
                'cantidad_cosechada' => 500,
                'peso_cosechado_kg' => 600.0,
                'destino' => 'venta',
                'responsable' => 'Juan Pérez',
                'observaciones' => 'Cosecha exitosa, peces de excelente calidad y tamaño comercial',
                'user_id' => 1
            ],
            [
                'lote_id' => $lotes->count() > 1 ? $lotes->skip(1)->first()->id : $lotes->first()->id,
                'fecha' => '2025-08-01',
                'cantidad_cosechada' => 300,
                'peso_cosechado_kg' => 450.0,
                'destino' => 'venta',
                'responsable' => 'María González',
                'observaciones' => 'Cosecha parcial programada, buenos resultados de crecimiento',
                'user_id' => 2
            ],
            [
                'lote_id' => $lotes->first()->id,
                'fecha' => '2025-08-20',
                'cantidad_cosechada' => 200,
                'peso_cosechado_kg' => 160.0,
                'destino' => 'muestra',
                'responsable' => 'Carlos López',
                'observaciones' => 'Selección de peces pequeños para liberar espacio en el estanque',
                'user_id' => 1
            ],
            [
                'lote_id' => $lotes->last()->id,
                'fecha' => '2025-08-25',
                'cantidad_cosechada' => 400,
                'peso_cosechado_kg' => 720.0,
                'destino' => 'venta',
                'responsable' => 'Ana Martín',
                'observaciones' => 'Lote selecto para exportación, cumple estándares internacionales',
                'user_id' => 2
            ]
        ];

        foreach ($cosechasParciales as $cosecha) {
            // Verificar si ya existe una cosecha similar
            $existente = CosechaParcial::where([
                'lote_id' => $cosecha['lote_id'],
                'fecha' => $cosecha['fecha'],
                'responsable' => $cosecha['responsable']
            ])->first();

            if (!$existente) {
                CosechaParcial::create($cosecha);
            }
        }

        $registrosCreados = CosechaParcial::count();
        $this->command->info("✅ Cosechas parciales procesadas: {$registrosCreados} total en la base de datos.");
    }
}
