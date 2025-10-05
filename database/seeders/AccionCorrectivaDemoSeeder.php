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
                'fecha_prevista' => now()->addDays(5),
                'fecha_limite' => now()->addDays(15),
                'estado' => 'pendiente',
            ],
            [
                'titulo' => 'Capacitación de personal',
                'descripcion' => 'Realizar capacitación sobre manejo de residuos peligrosos.',
                'user_id' => 1,
                'fecha_prevista' => now()->addDays(7),
                'fecha_limite' => now()->addDays(20),
                'estado' => 'en_progreso',
            ],
            [
                'titulo' => 'Reparación de maquinaria',
                'descripcion' => 'Reparar la máquina de empaque que presenta fallas.',
                'user_id' => 1,
                'fecha_prevista' => now()->addDays(3),
                'fecha_limite' => now()->addDays(10),
                'estado' => 'completada',
            ],
            [
                'titulo' => 'Actualización de manuales',
                'descripcion' => 'Actualizar los manuales de operación y seguridad.',
                'user_id' => 1,
                'fecha_prevista' => now()->addDays(8),
                'fecha_limite' => now()->addDays(25),
                'estado' => 'pendiente',
            ],
            [
                'titulo' => 'Inspección de extintores',
                'descripcion' => 'Verificar y recargar extintores en todas las áreas.',
                'user_id' => 1,
                'fecha_prevista' => now()->addDays(2),
                'fecha_limite' => now()->addDays(7),
                'estado' => 'en_progreso',
            ],
        ];

        foreach ($registros as $data) {
            AccionCorrectiva::create($data);
        }
    }
}
