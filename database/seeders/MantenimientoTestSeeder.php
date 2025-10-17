<?php

namespace Database\Seeders;

use App\Models\MantenimientoUnidad;
use App\Models\InventarioItem;
use App\Models\UnidadProduccion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MantenimientoTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first unit and user
        $unidad = UnidadProduccion::first();
        $user = User::first();
        $item = InventarioItem::first();
        
        if (!$unidad || !$user) {
            $this->command->info('Skipping: Missing required records');
            return;
        }

        // Create a test maintenance with activities and products
        $mantenimiento = MantenimientoUnidad::create([
            'unidad_produccion_id' => $unidad->id,
            'tipo_mantenimiento' => 'preventivo',
            'descripcion_trabajo' => 'Mantenimiento de prueba con insumos y actividades',
            'fecha_mantenimiento' => now()->addDays(1)->format('Y-m-d'),
            'prioridad' => 'media',
            'user_id' => $user->id,
            'estado_mantenimiento' => 'programado',
            'actividades' => ['Revisar tuberías', 'Limpiar filtros', 'Cambiar arandelas'],
            'actividades_ejecutadas' => [],
            'observaciones_antes' => 'Unidad funcionando normalmente'
        ]);

        // Add products if available
        if ($item) {
            $mantenimiento->insumos()->attach($item->id, [
                'cantidad' => 2,
                'costo_unitario' => $item->costo_unitario ?? 50,
                'costo_total' => ($item->costo_unitario ?? 50) * 2
            ]);
        }

        $this->command->info("Created test maintenance ID: {$mantenimiento->id}");
    }
}
