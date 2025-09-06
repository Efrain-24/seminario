<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Trazabilidad\TrazabilidadCosecha;

class TrazabilidadPermissionsSeeder extends Seeder
{
    /**
     * Inserta datos de ejemplo para el módulo de trazabilidad.
     */
    public function run(): void
    {
        // Ejemplo de registros de trazabilidad
        $registros = [
            [
                'lote_id' => 1,
                'fecha_cosecha' => now()->subDays(5),
                'tipo_cosecha' => 'total',
                'peso_bruto' => 1200,
                'peso_neto' => 1150,
                'unidades' => 110,
                'costo_mano_obra' => 600,
                'costo_insumos' => 250,
                'costo_operativo' => 180,
                'costo_total' => 600+250+180,
                'destino_tipo' => 'bodega',
                'destino_detalle' => 'Bodega Central',
                'notas' => 'Cosecha principal del lote 1',
            ],
            [
                'lote_id' => 2,
                'fecha_cosecha' => now()->subDays(3),
                'tipo_cosecha' => 'parcial',
                'peso_bruto' => 800,
                'peso_neto' => 760,
                'unidades' => 70,
                'costo_mano_obra' => 400,
                'costo_insumos' => 150,
                'costo_operativo' => 120,
                'costo_total' => 400+150+120,
                'destino_tipo' => 'mercado_local',
                'destino_detalle' => 'Mercado Zonal',
                'notas' => 'Primera cosecha parcial del lote 2',
            ],
            [
                'lote_id' => 1,
                'fecha_cosecha' => now()->subDays(1),
                'tipo_cosecha' => 'parcial',
                'peso_bruto' => 500,
                'peso_neto' => 480,
                'unidades' => 45,
                'costo_mano_obra' => 200,
                'costo_insumos' => 90,
                'costo_operativo' => 60,
                'costo_total' => 200+90+60,
                'destino_tipo' => 'cliente_final',
                'destino_detalle' => 'Cliente especial',
                'notas' => 'Entrega directa a cliente',
            ],
            [
                'lote_id' => 3,
                'fecha_cosecha' => now()->subDays(2),
                'tipo_cosecha' => 'total',
                'peso_bruto' => 1500,
                'peso_neto' => 1450,
                'unidades' => 130,
                'costo_mano_obra' => 700,
                'costo_insumos' => 300,
                'costo_operativo' => 210,
                'costo_total' => 700+300+210,
                'destino_tipo' => 'exportacion',
                'destino_detalle' => 'Exportación USA',
                'notas' => 'Cosecha para exportación',
            ],
        ];

        foreach ($registros as $data) {
            TrazabilidadCosecha::create($data);
        }
    }
}


