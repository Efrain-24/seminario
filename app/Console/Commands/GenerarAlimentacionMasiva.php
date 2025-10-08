<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alimentacion;
use Carbon\Carbon;

class GenerarAlimentacionMasiva extends Command
{
    protected $signature = 'alimentacion:masiva';
    protected $description = 'Genera registros de alimentación 5 veces al día para un rango de fechas';

    public function handle()
    {
        // Buscar los IDs necesarios
        $loteId = \App\Models\Lote::where('codigo_lote', 'TIL-2025-001')->value('id');
        $tipoAlimento = \App\Models\TipoAlimento::where('nombre', 'Concentrado para pez')->first();
        $tipoAlimentoId = $tipoAlimento ? $tipoAlimento->id : null;
        $inventarioItemId = $tipoAlimento && $tipoAlimento->inventario_item_id ? $tipoAlimento->inventario_item_id : null;
        $usuarioId = \App\Models\User::where('role', 'tecnico')->value('id');
        $bodegaId = \App\Models\Bodega::where('nombre', 'Concentrados y mas alimentos de pez')->value('id');
    $cantidad_kg = 1; // 1 lb por hora de alimentación
        $costo_total = 15.00;
        $porcentaje_consumo = 90.00;
        $horas = [6, 9, 12, 15, 18];
        $fecha_inicio = Carbon::create(2025, 3, 29);
        $fecha_fin = Carbon::create(2025, 4, 30);

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
