<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Lote;
use Carbon\Carbon;

class MortalidadSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener lotes existentes
        $lotes = Lote::take(4)->pluck('id');
        
        if ($lotes->isEmpty()) {
            $this->command->warn('No hay lotes disponibles para crear registros de mortalidad.');
            return;
        }

        // Datos de muestra para registros de mortalidad
        $mortalidades = [
            [
                'lote_id' => $lotes->first(),
                'fecha' => Carbon::now()->subDays(15),
                'cantidad' => 25,
                'causa' => 'Predación natural',
                'observaciones' => 'Pérdidas por aves pescadoras durante las primeras horas de la mañana',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lote_id' => $lotes->first(),
                'fecha' => Carbon::now()->subDays(30),
                'cantidad' => 18,
                'causa' => 'Estrés por transporte',
                'observaciones' => 'Mortalidad post-traslado, dentro de rangos normales esperados',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lote_id' => $lotes->count() > 1 ? $lotes[1] : $lotes->first(),
                'fecha' => Carbon::now()->subDays(8),
                'cantidad' => 12,
                'causa' => 'Calidad del agua',
                'observaciones' => 'Niveles elevados de amoníaco detectados y corregidos inmediatamente',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lote_id' => $lotes->count() > 1 ? $lotes[1] : $lotes->first(),
                'fecha' => Carbon::now()->subDays(22),
                'cantidad' => 31,
                'causa' => 'Cambio brusco de temperatura',
                'observaciones' => 'Fluctuación térmica nocturna afectó especímenes más pequeños',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lote_id' => $lotes->count() > 2 ? $lotes[2] : $lotes->first(),
                'fecha' => Carbon::now()->subDays(45),
                'cantidad' => 8,
                'causa' => 'Enfermedad bacteriana',
                'observaciones' => 'Tratamiento aplicado exitosamente, situación controlada',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lote_id' => $lotes->count() > 2 ? $lotes[2] : $lotes->first(),
                'fecha' => Carbon::now()->subDays(60),
                'cantidad' => 14,
                'causa' => 'Deficiencia nutricional',
                'observaciones' => 'Ajustada formulación del alimento, problema resuelto',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lote_id' => $lotes->last(),
                'fecha' => Carbon::now()->subDays(5),
                'cantidad' => 6,
                'causa' => 'Manejo inadecuado',
                'observaciones' => 'Incidente durante limpieza del estanque, procedimientos mejorados',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lote_id' => $lotes->last(),
                'fecha' => Carbon::now()->subDays(35),
                'cantidad' => 20,
                'causa' => 'Causa desconocida',
                'observaciones' => 'Mortalidad repentina sin síntomas previos, bajo investigación',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('mortalidades')->insert($mortalidades);
        
        $this->command->info('✅ Registros de mortalidad creados exitosamente: ' . count($mortalidades) . ' registros');
    }
}
