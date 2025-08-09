<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Seguimiento;
use App\Models\Lote;
use App\Models\User;
use Carbon\Carbon;

class SeguimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lotes = Lote::all();
        $usuarios = User::all();

        if ($lotes->count() === 0 || $usuarios->count() === 0) {
            $this->command->warn('No hay lotes o usuarios disponibles para crear seguimientos.');
            return;
        }

        foreach ($lotes as $lote) {
            // Crear seguimientos históricos para cada lote (últimos 30 días)
            $fechaInicio = $lote->fecha_inicio->copy();
            $hoy = Carbon::today();
            
            // Crear seguimientos cada 3-7 días
            $fechaActual = $fechaInicio->copy();
            $cantidadActual = $lote->cantidad_inicial;
            $pesoInicial = $lote->peso_promedio_inicial ?? 5.0;
            $tallaInicial = $lote->talla_promedio_inicial ?? 3.0;
            
            $contador = 0;
            while ($fechaActual->lte($hoy) && $contador < 10) {
                $tipoSeguimiento = match($contador % 4) {
                    0, 1 => 'rutinario',
                    2 => 'muestreo',
                    3 => 'mortalidad',
                    default => 'rutinario'
                };
                
                // Simular mortalidad gradual (1-3%)
                $mortalidad = rand(0, max(1, intval($cantidadActual * 0.03)));
                $cantidadActual -= $mortalidad;
                
                // Simular crecimiento
                $diasTranscurridos = $fechaActual->diffInDays($fechaInicio);
                $factorCrecimiento = 1 + ($diasTranscurridos * 0.02); // 2% de crecimiento por día
                $pesoActual = $pesoInicial * $factorCrecimiento;
                $tallaActual = $tallaInicial * (1 + ($diasTranscurridos * 0.01)); // 1% de crecimiento en talla
                
                Seguimiento::create([
                    'lote_id' => $lote->id,
                    'user_id' => $usuarios->random()->id,
                    'fecha_seguimiento' => $fechaActual->format('Y-m-d'),
                    'tipo_seguimiento' => $tipoSeguimiento,
                    'cantidad_actual' => $cantidadActual,
                    'mortalidad' => $mortalidad,
                    'peso_promedio' => round($pesoActual, 2),
                    'talla_promedio' => round($tallaActual, 2),
                    'temperatura_agua' => rand(220, 280) / 10, // 22-28°C
                    'ph_agua' => rand(65, 85) / 10, // 6.5-8.5
                    'oxigeno_disuelto' => rand(50, 80) / 10, // 5.0-8.0 mg/L
                    'observaciones' => $this->generarObservacion($tipoSeguimiento),
                    'created_at' => $fechaActual,
                    'updated_at' => $fechaActual
                ]);
                
                // Actualizar cantidad actual del lote
                $lote->update(['cantidad_actual' => $cantidadActual]);
                
                // Siguiente fecha (3-7 días después)
                $fechaActual->addDays(rand(3, 7));
                $contador++;
            }
        }
        
        $this->command->info('Seguimientos de prueba creados exitosamente.');
    }

    private function generarObservacion($tipo)
    {
        $observaciones = [
            'rutinario' => [
                'Peces activos y con buen apetito.',
                'Comportamiento normal observado.',
                'Agua clara, sin signos de contaminación.',
                'Alimentación regular completada.',
                'No se observan anomalías.'
            ],
            'muestreo' => [
                'Muestreo biométrico realizado en 30 ejemplares.',
                'Crecimiento uniforme observado en la población.',
                'Índices de crecimiento dentro de parámetros esperados.',
                'Variabilidad de tallas dentro del rango normal.',
                'Muestreo completado sin estrés aparente en los peces.'
            ],
            'mortalidad' => [
                'Mortalidad natural observada.',
                'Peces retirados y registrados.',
                'Posible causa: estrés por manejo.',
                'Revisar parámetros de calidad del agua.',
                'Mortalidad dentro de rangos esperados.'
            ],
            'traslado' => [
                'Preparativos para traslado iniciados.',
                'Peces en ayunas para procedimiento.',
                'Equipos de traslado preparados.',
                'Revisión de unidad de destino completada.'
            ]
        ];

        return $observaciones[$tipo][array_rand($observaciones[$tipo])];
    }
}
