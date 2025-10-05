<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Lote;

class AlertaAnomaliaSeeder extends Seeder
{
    public function run(): void
    {
        // -------- Helpers --------
        $insert = function (int $loteId, string $tipo, string $periodo, string $detalles, array $metrics = []) {
            $data = [
                'lote_id'     => $loteId,
                'tipo_alerta' => $tipo,
                'detalles'    => $detalles,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            // Campos opcionales (solo si existen en la tabla)
            if (Schema::hasColumn('alertas', 'periodo') && $periodo !== null) {
                $data['periodo'] = $periodo;
            }
            foreach ([
                'peso_actual',
                'peso_esperado',
                'porcentaje_desviacion',
                'tasa_crecimiento',
                'consumo_alimento_reciente',
                'factor_conversion_alimento',
                'dias_desviacion',
                'observaciones_alimentacion',
                'historico_pesos',
            ] as $k) {
                if (isset($metrics[$k]) && Schema::hasColumn('alertas', $k)) {
                    $data[$k] = $metrics[$k];
                }
            }

            DB::table('alertas')->insert($data);
        };

        // -------- Datos base para "bajo peso" --------
        $pesoEsperado1 = 250;   // g
        $pesoActual1   = 200;   // g
        $desv1         = (($pesoActual1 - $pesoEsperado1) / $pesoEsperado1) * 100;

        $metricsBajoPeso = [
            'peso_actual'                 => $pesoActual1,
            'peso_esperado'               => $pesoEsperado1,
            'porcentaje_desviacion'       => $desv1,
            'tasa_crecimiento'            => 0.8,
            'consumo_alimento_reciente'   => 25.5,
            'factor_conversion_alimento'  => 1.8,
            'dias_desviacion'             => 10,
            'observaciones_alimentacion'  => 'Se observa reducción en el consumo de alimento durante la última semana.',
            'historico_pesos'             => json_encode([
                ['fecha' => now()->subDays(14)->format('Y-m-d'), 'peso' => 180],
                ['fecha' => now()->subDays(7)->format('Y-m-d'),  'peso' => 190],
                ['fecha' => now()->format('Y-m-d'),              'peso' => 200],
            ]),
        ];

        // -------- Datos base para "bajo peso" crítica --------
        $pesoEsperado2 = 300;   // g
        $pesoActual2   = 220;   // g
        $desv2         = (($pesoActual2 - $pesoEsperado2) / $pesoEsperado2) * 100;

        $metricsBajoPesoCritica = [
            'peso_actual'                 => $pesoActual2,
            'peso_esperado'               => $pesoEsperado2,
            'porcentaje_desviacion'       => $desv2,
            'tasa_crecimiento'            => 0.5,
            'consumo_alimento_reciente'   => 20.0,
            'factor_conversion_alimento'  => 2.2,
            'dias_desviacion'             => 15,
            'observaciones_alimentacion'  => 'Consumo irregular de alimento. Posible problema en la calidad del agua.',
            'historico_pesos'             => json_encode([
                ['fecha' => now()->subDays(21)->format('Y-m-d'), 'peso' => 200],
                ['fecha' => now()->subDays(14)->format('Y-m-d'), 'peso' => 210],
                ['fecha' => now()->subDays(7)->format('Y-m-d'),  'peso' => 215],
                ['fecha' => now()->format('Y-m-d'),              'peso' => 220],
            ]),
        ];

        // Crear un lote de prueba si no existe ninguno
        if (Lote::count() === 0) {
            $loteFijo = Lote::create([
                'codigo_lote' => 'LOTE-001',
                'especie' => 'Tilapia',
                'cantidad_inicial' => 1000,
                'cantidad_actual' => 1000,
                'peso_promedio_inicial' => 150.00,
                'talla_promedio_inicial' => 20.00,
                'fecha_inicio' => now(),
                'estado' => 'activo',
                'observaciones' => 'Lote de prueba para alertas'
            ]);
        } else {
            $loteFijo = Lote::first();
        }
        if ($loteFijo) {
            // Mortalidad
            $insert(
                loteId: $loteFijo->id,
                tipo: 'mortalidad',
                periodo: 'Últimos 30 días',
                detalles: 'El lote presenta una tasa de mortalidad elevada en el último mes.'
            );

            // Bajo peso
            $insert(
                loteId: $loteFijo->id,
                tipo: 'bajo peso',
                periodo: 'Última semana',
                detalles: 'El lote presenta un peso promedio por debajo del esperado para su edad.',
                metrics: $metricsBajoPeso
            );

            // Bajo peso crítica
            $insert(
                loteId: $loteFijo->id,
                tipo: 'bajo peso',
                periodo: 'Últimos 15 días',
                detalles: 'El lote presenta un retraso significativo en el crecimiento.',
                metrics: $metricsBajoPesoCritica
            );
        }

        // =========================
        // 2) Alertas para TODOS los lotes (evita duplicar el lote fijo)
        // =========================
        $q = Lote::query();
        if ($loteFijo) $q->whereKeyNot($loteFijo->id);

        foreach ($q->get() as $lote) {
            // Mortalidad
            $insert(
                loteId: $lote->id,
                tipo: 'mortalidad',
                periodo: 'Últimos 30 días',
                detalles: 'El lote presenta una tasa de mortalidad elevada en el último mes.'
            );

            // Bajo peso
            $insert(
                loteId: $lote->id,
                tipo: 'bajo peso',
                periodo: 'Última semana',
                detalles: 'El lote presenta un peso promedio por debajo del esperado para su edad.',
                metrics: $metricsBajoPeso
            );

            // Bajo peso crítica
            $insert(
                loteId: $lote->id,
                tipo: 'bajo peso',
                periodo: 'Últimos 15 días',
                detalles: 'El lote presenta un retraso significativo en el crecimiento.',
                metrics: $metricsBajoPesoCritica
            );
        }
    }
}
