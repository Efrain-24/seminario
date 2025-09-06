<?php
namespace App\Listeners;

use App\Events\MortalidadRegistrada;
use App\Notifications\AlertaSanidadNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class EvaluarMortalidadElevada implements ShouldQueue
{
    public function handle(MortalidadRegistrada $event): void
    {
        $m = $event->mortalidad;
        $lote = $m->lote;               // relación: Mortalidad belongsTo Lote
        if (!$lote) return;

        // mortalidad del día
        $mortalidadDia = $lote->mortalidades()
            ->whereDate('fecha', $m->fecha)
            ->sum('cantidad');

        $base = max(1, $lote->poblacion_actual ?? $lote->poblacion_inicial ?? 0);
        $porcentaje = ($mortalidadDia / $base) * 100;

        $umbral = config('sanidad.mortalidad_umbral_porcentual', 5);
        if ($porcentaje >= $umbral) {
            foreach ($this->destinatarios($lote) as $user) {
                $user->notify(new AlertaSanidadNotification(
                    'Mortalidad elevada',
                    "Lote {$lote->codigo}: " . round($porcentaje,2) . "% de mortalidad el {$m->fecha->toDateString()}",
                    [
                        'tipo' => 'mortalidad',
                        'lote_id' => $lote->id,
                        'porcentaje' => round($porcentaje,2),
                        'mortalidad_dia' => $mortalidadDia,
                        'fecha' => $m->fecha->toDateString(),
                    ]
                ));
            }
        }
    }

    private function destinatarios($lote)
    {
        // ajusta a tu lógica; por defecto, notifica a usuarios con rol admin
        return \App\Models\User::role('admin')->get(); // o $lote->responsablesNotificables()
    }
}
