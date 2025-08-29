<?php

namespace Database\Seeders;

use App\Models\Mortalidad;
use App\Models\Lote;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MortalidadSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existen datos
        $registrosExistentes = Mortalidad::count();
        if ($registrosExistentes > 0) {
            $this->command->info("✅ Ya existen {$registrosExistentes} registros de mortalidad en la base de datos.");
            return;
        }

        // Obtener lotes existentes
        $lotes = Lote::all();

        if ($lotes->isEmpty()) {
            $this->command->warn('No hay lotes disponibles para crear registros de mortalidad');
            return;
        }

        $causas = [
            'enfermedad_bacteriana',
            'enfermedad_viral',
            'parasitos',
            'estres_ambiental',
            'mala_calidad_agua',
            'sobrepoblacion',
            'deficiencia_nutricional',
            'manejo_inadecuado',
            'predacion',
            'causa_desconocida'
        ];

        $mortalidades = [];

        foreach ($lotes as $lote) {
            // Crear 2-4 registros de mortalidad por lote
            $numRegistros = rand(2, 4);
            
            for ($i = 0; $i < $numRegistros; $i++) {
                $fechaBase = Carbon::parse($lote->fecha_inicio)->addDays(rand(15, 120));
                
                $mortalidades[] = [
                    'lote_id' => $lote->id,
                    'fecha' => $fechaBase->format('Y-m-d'),
                    'cantidad' => rand(5, 50),
                    'causa' => $causas[array_rand($causas)],
                    'observaciones' => 'Registro de mortalidad - seguimiento continuo de la salud del lote',
                    'user_id' => 1, // Asumiendo que existe usuario con ID 1
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        // Crear registros individuales en lugar de insertar en lotes para evitar duplicados
        $registrosCreados = 0;
        foreach ($mortalidades as $mortalidad) {
            // Verificar si ya existe un registro similar
            $existente = Mortalidad::where([
                'lote_id' => $mortalidad['lote_id'],
                'fecha' => $mortalidad['fecha'],
                'causa' => $mortalidad['causa']
            ])->first();

            if (!$existente) {
                Mortalidad::create($mortalidad);
                $registrosCreados++;
            }
        }

        $totalRegistros = Mortalidad::count();
        $this->command->info("✅ Mortalidad procesada: {$registrosCreados} nuevos registros creados, {$totalRegistros} total en la base de datos.");
    }
}
