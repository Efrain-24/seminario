<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alimentacion;
use App\Models\Lote;
use App\Models\TipoAlimento;
use App\Models\User;
use Carbon\Carbon;

class AlimentacionSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener datos necesarios
        $lotes = Lote::where('estado', 'activo')->get();
        $tiposAlimento = TipoAlimento::where('activo', true)->get();
        $usuarios = User::whereIn('role', ['admin', 'manager', 'empleado'])->get();

        if ($lotes->isEmpty() || $tiposAlimento->isEmpty() || $usuarios->isEmpty()) {
            $this->command->info('No hay lotes, tipos de alimento o usuarios disponibles. Ejecuta primero ProduccionSeeder y TipoAlimentoSeeder.');
            return;
        }

        $metodosAlimentacion = array_keys(Alimentacion::getMetodosAlimentacion());
        $estadosPeces = array_keys(Alimentacion::getEstadosPeces());

        // Crear registros de alimentación para los últimos 30 días
        for ($i = 0; $i < 30; $i++) {
            $fecha = Carbon::now()->subDays($i);
            
            // 2-4 registros por día
            $registrosPorDia = rand(2, 4);
            
            for ($j = 0; $j < $registrosPorDia; $j++) {
                $lote = $lotes->random();
                $tipoAlimento = $tiposAlimento->random();
                $usuario = $usuarios->random();
                
                $hora = sprintf('%02d:%02d:00', rand(6, 18), rand(0, 59));
                // Simular bajo consumo de alimento en algunos casos
                $cantidadKg = $i < 5 ? rand(10, 15) / 10 : rand(22, 220) / 10; // Últimos 5 días con consumo muy bajo
                $costoTotal = $tipoAlimento->costo_por_kg ? $cantidadKg * $tipoAlimento->costo_por_kg : null;
                
                Alimentacion::create([
                    'lote_id' => $lote->id,
                    'tipo_alimento_id' => $tipoAlimento->id,
                    'usuario_id' => $usuario->id,
                    'fecha_alimentacion' => $fecha->toDateString(),
                    'hora_alimentacion' => $hora,
                    'cantidad_kg' => $cantidadKg,
                    'metodo_alimentacion' => $metodosAlimentacion[array_rand($metodosAlimentacion)],
                    'estado_peces' => $estadosPeces[array_rand($estadosPeces)],
                    'porcentaje_consumo' => rand(70, 100), // Entre 70% y 100%
                    'costo_total' => $costoTotal,
                    'observaciones' => $this->getObservacionAleatoria(),
                    'created_at' => $fecha->addMinutes(rand(0, 60)),
                    'updated_at' => $fecha->addMinutes(rand(0, 60)),
                ]);
            }
        }

        $this->command->info('Registros de alimentación creados exitosamente.');
    }

    private function getObservacionAleatoria(): ?string
    {
        $observaciones = [
            'Peces con buen apetito, consumo normal',
            'Peces algo lentos para consumir, posible efecto del clima',
            'Excelente respuesta a la alimentación',
            'Se observó algo de alimento sobrante',
            'Peces muy activos durante la alimentación',
            'Temperatura del agua ligeramente elevada',
            'pH dentro de rangos normales',
            'Alimentación completada sin observaciones',
            null, // Sin observaciones
            null,
            null,
        ];

        return $observaciones[array_rand($observaciones)];
    }
}
