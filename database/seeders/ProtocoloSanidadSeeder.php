<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProtocoloSanidad;

class ProtocoloSanidadSeeder extends Seeder
{
    public function run(): void
    {
        ProtocoloSanidad::insert([
            [
                'nombre' => 'Protocolo General de Limpieza',
                'descripcion' => 'Limpieza diaria de todas las áreas de producción con desinfectante aprobado.',
                'fecha_implementacion' => '2025-01-01',
                'responsable' => 'Jefe de Sanidad',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Bioseguridad para Visitantes',
                'descripcion' => 'Control de acceso y uso de indumentaria especial para visitantes.',
                'fecha_implementacion' => '2025-02-15',
                'responsable' => 'Supervisor de Bioseguridad',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Desinfección de Equipos',
                'descripcion' => 'Desinfección semanal de herramientas y equipos críticos.',
                'fecha_implementacion' => '2025-03-10',
                'responsable' => 'Encargado de Equipos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
