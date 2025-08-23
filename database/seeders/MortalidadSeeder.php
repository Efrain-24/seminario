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
                    'fecha_registro' => $fechaBase->format('Y-m-d'),
                    'cantidad' => rand(5, 50),
                    'causa_principal' => $causas[array_rand($causas)],
                    'sintomas_observados' => $this->generarSintomas(),
                    'acciones_tomadas' => $this->generarAcciones(),
                    'peso_promedio_afectado' => rand(50, 200) / 100, // 0.5 - 2.0 kg
                    'observaciones' => 'Registro de mortalidad - seguimiento continuo de la salud del lote',
                    'reportado_por' => 'Sistema de Monitoreo',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        // Insertar en lotes para mejorar rendimiento
        $chunks = array_chunk($mortalidades, 10);
        foreach ($chunks as $chunk) {
            Mortalidad::insert($chunk);
        }

        $this->command->info('✅ Seeders de mortalidad creados: ' . count($mortalidades) . ' registros');
    }

    private function generarSintomas(): string
    {
        $sintomas = [
            'Letargo y nado irregular',
            'Pérdida de apetito',
            'Cambios en la coloración',
            'Lesiones en la piel',
            'Respiración acelerada',
            'Comportamiento errático',
            'Pérdida de equilibrio',
            'Ojos opacos o hinchados',
            'Aletas dañadas',
            'Secreciones anormales'
        ];

        $numSintomas = rand(1, 3);
        $sintomasSeleccionados = array_rand($sintomas, $numSintomas);
        
        if (is_array($sintomasSeleccionados)) {
            return implode(', ', array_map(fn($i) => $sintomas[$i], $sintomasSeleccionados));
        }
        
        return $sintomas[$sintomasSeleccionados];
    }

    private function generarAcciones(): string
    {
        $acciones = [
            'Mejora de la calidad del agua',
            'Ajuste en la alimentación',
            'Tratamiento con medicamentos específicos',
            'Aislamiento de individuos afectados',
            'Desinfección del área',
            'Consulta veterinaria',
            'Monitoreo intensivo',
            'Cambio de dieta',
            'Mejora de la oxigenación',
            'Reducción de la densidad poblacional'
        ];

        $numAcciones = rand(1, 2);
        $accionesSeleccionadas = array_rand($acciones, $numAcciones);
        
        if (is_array($accionesSeleccionadas)) {
            return implode(', ', array_map(fn($i) => $acciones[$i], $accionesSeleccionadas));
        }
        
        return $acciones[$accionesSeleccionadas];
    }
}
