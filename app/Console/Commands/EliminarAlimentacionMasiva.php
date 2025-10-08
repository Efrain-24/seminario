<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alimentacion;
use Carbon\Carbon;

class EliminarAlimentacionMasiva extends Command
{
    protected $signature = 'alimentacion:eliminar-masiva';
    protected $description = 'Elimina registros de alimentación masiva generados por el comando';

    public function handle()
    {
        $loteId = \App\Models\Lote::where('codigo_lote', 'TIL-2025-001')->value('id');
        $usuarioId = \App\Models\User::where('role', 'tecnico')->value('id');
        $fecha_inicio = Carbon::create(2025, 3, 29);
        $fecha_fin = Carbon::create(2025, 4, 30);

        $eliminados = Alimentacion::where('lote_id', $loteId)
            ->where('usuario_id', $usuarioId)
            ->whereBetween('fecha_alimentacion', [$fecha_inicio->toDateString(), $fecha_fin->toDateString()])
            ->delete();

        $this->info("Registros eliminados: $eliminados");
    }
}
