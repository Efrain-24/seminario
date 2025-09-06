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
                'area' => 'Área de producción',
                'descripcion' => 'Limpieza y desinfección diaria de la zona de producción.',
                'responsable' => 'Juan Pérez',
                'protocolo_sanidad_id' => $protocolo->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fecha' => now()->subDay()->toDateString(),
                'area' => 'Bodega',
                'descripcion' => 'Desinfección semanal de la bodega de insumos.',
                'responsable' => 'Ana Gómez',
                'protocolo_sanidad_id' => $protocolo->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
