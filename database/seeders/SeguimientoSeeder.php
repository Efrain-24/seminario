<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lote;
use App\Models\Seguimiento;
use Carbon\Carbon;

class SeguimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener lotes activos
        $lotes = Lote::where('estado', 'activo')->get();
        
        if ($lotes->isEmpty()) {
            $this->command->warn('No hay lotes activos para crear seguimientos.');
            return;
        }

        $seguimientosCreados = 0;

        foreach ($lotes as $lote) {
            // Calcular cuántas semanas han pasado desde el inicio
            $semanasTranscurridas = $lote->fecha_inicio->diffInWeeks(now());
            
            // Crear seguimientos semanales desde el inicio hasta ahora
            for ($semana = 1; $semana <= $semanasTranscurridas; $semana++) {
                $fechaSeguimiento = $lote->fecha_inicio->copy()->addWeeks($semana);
                
                // Solo crear seguimientos hasta la fecha actual
                if ($fechaSeguimiento->isFuture()) {
                    break;
                }

                // Calcular peso realista basado en la especie y semana
                $pesoPromedio = $this->calcularPesoRealista($lote, $semana);
                $tallaPromedio = $this->calcularTallaRealista($lote, $semana);
                
                // Simular bajo peso en las últimas semanas
                if ($semana > $semanasTranscurridas - 2) {
                    // Últimas dos semanas con peso significativamente bajo (-30% a -20%)
                    $variacion = rand(-30, -20) / 100;
                } else {
                    // Resto del tiempo variabilidad normal
                    $variacion = rand(-10, 10) / 100;
                }
                $pesoPromedio = $pesoPromedio * (1 + $variacion);
                $tallaPromedio = $tallaPromedio * (1 + $variacion);

                Seguimiento::create([
                    'lote_id' => $lote->id,
                    'fecha_seguimiento' => $fechaSeguimiento,
                    'cantidad_actual' => $this->calcularCantidadActual($lote, $semana),
                    'peso_promedio' => round($pesoPromedio, 2),
                    'talla_promedio' => round($tallaPromedio, 2),
                    'temperatura_agua' => rand(18, 26) + (rand(0, 9) / 10), // 18.0 - 26.9°C
                    'ph_agua' => rand(65, 85) / 10, // 6.5 - 8.5
                    'oxigeno_disuelto' => rand(50, 80) / 10, // 5.0 - 8.0 mg/L
                    'tipo_seguimiento' => 'rutinario',
                    'observaciones' => $this->generarObservaciones($semana, $lote->especie),
                    'user_id' => rand(1, 2), // Usuarios que creamos en el seeder
                ]);
                
                $seguimientosCreados++;
            }
        }

        $this->command->info("✅ Seguimientos creados exitosamente: {$seguimientosCreados} registros");
    }

    /**
     * Calcular peso realista basado en curvas de crecimiento reales
     */
    private function calcularPesoRealista($lote, $semana)
    {
        $pesoInicial = $lote->peso_promedio_inicial;
        
        if (strtolower($lote->especie) === 'trucha arcoíris' || strtolower($lote->especie) === 'trucha') {
            // Curva de crecimiento para trucha arcoíris
            // Crecimiento exponencial que se estabiliza
            return $pesoInicial + ($semana * 4.2) + (($semana ** 1.15) * 0.8);
        } else {
            // Tilapia u otras especies
            // Crecimiento más rápido inicialmente
            return $pesoInicial + ($semana * 3.5) + (($semana ** 1.1) * 0.7);
        }
    }

    /**
     * Calcular talla realista
     */
    private function calcularTallaRealista($lote, $semana)
    {
        $tallaInicial = $lote->talla_promedio_inicial;
        
        if (strtolower($lote->especie) === 'trucha arcoíris' || strtolower($lote->especie) === 'trucha') {
            // Trucha crece más en longitud
            return $tallaInicial + ($semana * 0.8) + (($semana ** 0.9) * 0.15);
        } else {
            // Tilapia
            return $tallaInicial + ($semana * 0.6) + (($semana ** 0.85) * 0.12);
        }
    }

    /**
     * Calcular cantidad actual considerando mortalidad natural
     */
    private function calcularCantidadActual($lote, $semana)
    {
        // Mortalidad natural del 0.5-1% por semana
        $tasaMortalidad = rand(5, 10) / 1000; // 0.5% - 1.0%
        $cantidadActual = $lote->cantidad_inicial;
        
        // Aplicar mortalidad acumulativa
        for ($i = 1; $i <= $semana; $i++) {
            $cantidadActual = $cantidadActual * (1 - $tasaMortalidad);
        }
        
        return (int) round($cantidadActual);
    }

    /**
     * Generar observaciones realistas
     */
    private function generarObservaciones($semana, $especie)
    {
        $observaciones = [
            "Seguimiento semanal rutinario - desarrollo normal",
            "Peces activos, buen apetito y comportamiento natural",
            "Crecimiento constante, parámetros dentro de rango óptimo",
            "Actividad alimentaria normal, respuesta rápida al alimento",
            "Desarrollo homogéneo del lote, sin anomalías detectadas",
            "Comportamiento de nado normal, buena dispersión en el agua",
            "Condición corporal óptima, coloración saludable",
            "Seguimiento quincenal - evolución favorable del crecimiento"
        ];

        if ($semana <= 2) {
            return "Adaptación inicial al sistema, seguimiento intensivo de parámetros";
        } elseif ($semana <= 8) {
            return $observaciones[array_rand($observaciones)];
        } else {
            return "Fase de engorde - " . $observaciones[array_rand($observaciones)];
        }
    }
}
