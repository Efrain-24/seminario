<?php
namespace App\Listeners;

use App\Events\DensidadActualizada;
use App\Notifications\AlertaSanidadNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class EvaluarDensidadCritica implements ShouldQueue
{
    public function handle(DensidadActualizada $event): void
    {
        $lote = $event->lote;
        if (!$lote || !$lote->volumen_m3) return;

        $poblacion = $lote->poblacion_actual ?? $lote->poblacion_inicial ?? 0;
        $densidad = $poblacion / max(0.0001, $lote->volumen_m3);

        $umbral = config('sanidad.densidad_umbral_critico', 30);
        if ($densidad >= $umbral) {
            foreach ($this->destinatarios($lote) as $user) {
                $user->notify(new AlertaSanidadNotification(
                    'Densidad crítica',
                    "Lote {$lote->codigo}: densidad " . round($densidad,2) . " peces/m³ (≥ {$umbral})",
                    [
                        'tipo' => 'densidad',
                        'lote_id' => $lote->id,
                        'densidad' => round($densidad,2),
                        'umbral' => $umbral,
                    ]
                ));
            }
        }
    }

    private function destinatarios($lote)
    {
        return \App\Models\User::role('admin')->get();
    }
}
