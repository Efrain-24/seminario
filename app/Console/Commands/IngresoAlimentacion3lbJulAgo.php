<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alimentacion;
use Carbon\Carbon;

class IngresoAlimentacion3lbJulAgo extends Command
{
    protected $signature = 'alimentacion:3lb-jul-ago';
    protected $description = 'Genera registros de alimentación de 3 lb por hora en los horarios y fechas indicados (julio-agosto)';

    public function handle()
    {
        $loteId = \App\Models\Lote::where('codigo_lote', 'TIL-2025-001')->value('id');
        $tipoAlimento = \App\Models\TipoAlimento::where('nombre', 'alimentacion para pez')->first();
        $tipoAlimentoId = $tipoAlimento ? $tipoAlimento->id : null;
        $inventarioItemId = $tipoAlimento && $tipoAlimento->inventario_item_id ? $tipoAlimento->inventario_item_id : null;
        $usuarioId = \App\Models\User::where('role', 'tecnico')->value('id');
        $bodegaId = \App\Models\Bodega::where('nombre', 'concentrados y alimentos de pez')->value('id');
        $cantidad_kg = 3; // 3 lb por hora
        $costo_total = 15.00;
        $porcentaje_consumo = 90.00;
        $horas = [6, 9, 12, 15, 17];
        $fecha_inicio = Carbon::create(2025, 7, 1);
        $fecha_fin = Carbon::create(2025, 8, 30);

        $total = 0;
        for ($fecha = $fecha_inicio->copy(); $fecha->lte($fecha_fin); $fecha->addDay()) {
            foreach ($horas as $hora) {
                $registro = new Alimentacion();
                $registro->lote_id = $loteId;
                $registro->tipo_alimento_id = $tipoAlimentoId;
                $registro->inventario_item_id = $inventarioItemId;
                $registro->bodega_id = $bodegaId;
                $registro->usuario_id = $usuarioId;
                $registro->fecha_alimentacion = $fecha->format('Y-m-d');
                $registro->hora_alimentacion = sprintf('%02d:00:00', $hora);
                $registro->cantidad_kg = $cantidad_kg;
                $registro->costo_total = $costo_total;
                $registro->porcentaje_consumo = $porcentaje_consumo;
                $registro->metodo_alimentacion = 'manual';
                $registro->estado_peces = 'normal';
                $registro->save();
                $total++;
            }
        }
        $this->info("Registros generados: $total");
    }
}
