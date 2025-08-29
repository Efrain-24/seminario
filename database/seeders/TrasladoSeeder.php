<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Traslado;
use App\Models\Lote;
use App\Models\UnidadProduccion;
use App\Models\User;
use Carbon\Carbon;

class TrasladoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen datos
        $registrosExistentes = Traslado::count();
        if ($registrosExistentes > 0) {
            $this->command->info("✅ Ya existen {$registrosExistentes} registros de traslado en la base de datos.");
            return;
        }

        $lotes = Lote::all();
        $unidades = UnidadProduccion::all();
        $usuarios = User::all();

        if ($lotes->count() === 0 || $unidades->count() < 2 || $usuarios->count() === 0) {
            $this->command->warn('Se necesitan al menos 1 lote, 2 unidades y 1 usuario para crear traslados.');
            return;
        }

        // Crear algunos traslados de ejemplo
        foreach ($lotes->take(2) as $index => $lote) {
            // Seleccionar unidades diferentes para origen y destino
            $unidadOrigen = $lote->unidad_produccion_id;
            $unidadesDisponibles = $unidades->where('id', '!=', $unidadOrigen);
            
            if ($unidadesDisponibles->count() === 0) continue;

            $unidadDestino = $unidadesDisponibles->random();
            
            // Crear traslado completado (histórico)
            $fechaTraslado = Carbon::now()->subDays(rand(5, 30));
            $cantidadTrasladada = intval($lote->cantidad_actual * 0.3); // 30% del lote
            $cantidadPerdida = rand(0, intval($cantidadTrasladada * 0.02)); // hasta 2% de pérdidas

            $trasladoCompletado = Traslado::create([
                'lote_id' => $lote->id,
                'unidad_origen_id' => $unidadOrigen,
                'unidad_destino_id' => $unidadDestino->id,
                'user_id' => $usuarios->random()->id,
                'fecha_traslado' => $fechaTraslado,
                'cantidad_trasladada' => $cantidadTrasladada,
                'cantidad_perdida' => $cantidadPerdida,
                'peso_promedio_traslado' => rand(50, 200) / 10, // 5-20g
                'motivo_traslado' => ['crecimiento', 'sobrepoblacion', 'mejores_condiciones'][rand(0, 2)],
                'estado_traslado' => 'completado',
                'observaciones_origen' => 'Lote en buenas condiciones para traslado.',
                'observaciones_destino' => 'Unidad preparada y acondicionada.',
                'hora_inicio' => '08:00',
                'hora_fin' => '10:30',
                'created_at' => $fechaTraslado,
                'updated_at' => $fechaTraslado
            ]);

            // Crear seguimiento asociado
            \App\Models\Seguimiento::create([
                'lote_id' => $lote->id,
                'user_id' => $usuarios->random()->id,
                'fecha_seguimiento' => $fechaTraslado,
                'tipo_seguimiento' => 'traslado',
                'cantidad_actual' => $cantidadTrasladada - $cantidadPerdida,
                'mortalidad' => $cantidadPerdida,
                'peso_promedio' => $trasladoCompletado->peso_promedio_traslado,
                'observaciones' => "Traslado completado desde {$unidadOrigen} hacia {$unidadDestino->nombre}. Cantidad trasladada: {$cantidadTrasladada} peces.",
                'created_at' => $fechaTraslado,
                'updated_at' => $fechaTraslado
            ]);

            // Actualizar el traslado con el ID del seguimiento
            $seguimiento = \App\Models\Seguimiento::latest()->first();
            $trasladoCompletado->update(['seguimiento_id' => $seguimiento->id]);

            // Crear un traslado planificado para el futuro
            if ($index === 0) {
                $fechaFutura = Carbon::now()->addDays(rand(1, 7));
                $otraUnidad = $unidadesDisponibles->where('id', '!=', $unidadDestino->id)->first();
                
                if ($otraUnidad) {
                    Traslado::create([
                        'lote_id' => $lote->id,
                        'unidad_origen_id' => $unidadDestino->id,
                        'unidad_destino_id' => $otraUnidad->id,
                        'user_id' => $usuarios->random()->id,
                        'fecha_traslado' => $fechaFutura,
                        'cantidad_trasladada' => intval($cantidadTrasladada * 0.8),
                        'cantidad_perdida' => 0,
                        'peso_promedio_traslado' => rand(80, 300) / 10,
                        'motivo_traslado' => 'crecimiento',
                        'estado_traslado' => 'planificado',
                        'observaciones_origen' => 'Traslado programado por crecimiento de los peces.',
                        'hora_inicio' => '09:00',
                    ]);
                }
            }
        }

        $totalTraslados = Traslado::count();
        $this->command->info("✅ Traslados procesados: {$totalTraslados} total en la base de datos.");
    }
}
