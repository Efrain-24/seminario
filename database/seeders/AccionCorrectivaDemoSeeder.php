<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccionCorrectiva;
use Illuminate\Support\Str;

class AccionCorrectivaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            [
                'titulo' => 'Revisión de protocolos de limpieza',
                'descripcion' => 'Actualizar y reforzar los protocolos de limpieza en la planta de producción.',
                'user_id' => 1,
                'fecha_detectada' => now()->subDays(10),
                'fecha_limite' => now()->addDays(5),
                'estado' => 'pendiente',
                'observaciones' => 'Se detectó deficiencia en la limpieza de equipos.',
            ],
            [
                'titulo' => 'Capacitación de personal',
                'descripcion' => 'Realizar capacitación sobre manejo de residuos peligrosos.',
                'user_id' => 1,
                'fecha_detectada' => now()->subDays(20),
                'fecha_limite' => now()->addDays(10),
                'estado' => 'en_progreso',
                'observaciones' => 'Capacitación programada para la próxima semana.',
            ],
            [
                'titulo' => 'Reparación de maquinaria',
                'descripcion' => 'Reparar la máquina de empaque que presenta fallas.',
                'user_id' => 1,
                'fecha_detectada' => now()->subDays(5),
                'fecha_limite' => now()->addDays(15),
                'estado' => 'completada',
                'observaciones' => 'Reparación finalizada y verificada.',
            ],
            [
                'titulo' => 'Actualización de manuales',
                'descripcion' => 'Actualizar los manuales de operación y seguridad.',
                'user_id' => 1,
                'fecha_detectada' => now()->subDays(8),
                'fecha_limite' => now()->addDays(12),
                'estado' => 'pendiente',
                'observaciones' => 'Manuales desactualizados detectados en auditoría.',
            ],
            [
                'titulo' => 'Inspección de extintores',
                'descripcion' => 'Verificar y recargar extintores en todas las áreas.',
                'user_id' => 1,
                'fecha_detectada' => now()->subDays(3),
                'fecha_limite' => now()->addDays(7),
                'estado' => 'en_progreso',
                'observaciones' => 'Algunos extintores con carga baja.',
            ],
        ];

        foreach ($registros as $data) {
            AccionCorrectiva::create($data);
        }
    }
}
