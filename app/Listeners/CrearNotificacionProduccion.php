<?php

namespace App\Listeners;

use App\Events\AlertaProduccionDetectada;
use App\Models\Notificacion;

class CrearNotificacionProduccion
{
    /**
     * Handle the event.
     */
    public function handle(AlertaProduccionDetectada $event): void
    {
        $lote = $event->lote;
        $datos = $event->datosAlerta;
        
        // Verificar si ya existe una notificación reciente
        $existeReciente = Notificacion::where('tipo', 'error')
            ->whereJsonContains('datos->lote_id', $lote->id)
            ->whereJsonContains('datos->tipo_alerta', 'bajo_rendimiento')
            ->where('created_at', '>', now()->subHours(2))
            ->exists();

        if (!$existeReciente) {
            $deficitPct = $datos['deficit_pct'];
            $severidad = $deficitPct >= 40 ? 'crítica' : ($deficitPct >= 25 ? 'alta' : 'media');
            
            Notificacion::create([
                'tipo' => 'error',
                'titulo' => 'Anomalía de Producción Detectada',
                'mensaje' => "El lote {$lote->codigo_lote} presenta bajo rendimiento ({$severidad}). Déficit: {$deficitPct}%",
                'datos' => [
                    'lote_id' => $lote->id,
                    'codigo_lote' => $lote->codigo_lote,
                    'deficit_pct' => $deficitPct,
                    'deficit_kg' => $datos['deficit_kg'],
                    'severidad' => $severidad,
                    'tipo_alerta' => 'bajo_rendimiento',
                    'automatica' => true,
                    'tiempo_real' => true
                ],
                'icono' => 'alert-triangle',
                'url' => route('produccion.alertas.index', ['lote_id' => $lote->id]),
                'fecha_vencimiento' => now()->addDays(7)
            ]);
        }
    }
}
