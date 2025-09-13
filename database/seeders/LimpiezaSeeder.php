<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Limpieza;
use App\Models\ProtocoloSanidad;

class LimpiezaSeeder extends Seeder
{
    public function run(): void
    {
        $protocolo = ProtocoloSanidad::first();
        if (!$protocolo) {
            $protocolo = ProtocoloSanidad::create([
                'nombre' => 'Protocolo General',
                'descripcion' => 'Protocolo de ejemplo para limpieza',
                'fecha_implementacion' => now(),
                'responsable' => 'Admin',
            ]);
        }

        Limpieza::insert([
            [
                'fecha' => now()->subDays(2)->toDateString(),
                'area' => 'Unidad: Tanque 1 - Cultivo de Tilapia',
                'responsable' => 'Juan Pérez',
                'protocolo_sanidad_id' => $protocolo->id,
                'estado' => 'completado',
                'observaciones' => 'Limpieza y desinfección diaria de la zona de producción.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fecha' => now()->subDay()->toDateString(),
                'area' => 'Bodega: Almacén General',
                'responsable' => 'Ana Gómez',
                'protocolo_sanidad_id' => $protocolo->id,
                'estado' => 'en_progreso',
                'observaciones' => 'Desinfección semanal de la bodega de insumos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fecha' => now()->toDateString(),
                'area' => 'Unidad: Tanque 2 - Cultivo de Bagre',
                'responsable' => 'Carlos López',
                'protocolo_sanidad_id' => $protocolo->id,
                'estado' => 'no_ejecutado',
                'observaciones' => 'Programada limpieza general de tanque.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
