<?php
namespace App\Listeners;

use App\Events\EnfermedadRegistrada;
use App\Notifications\AlertaSanidadNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class EvaluarRegistroEnfermedad implements ShouldQueue
{
    public function handle(EnfermedadRegistrada $event): void
    {
        $e = $event->enfermedad;
        $lote = $e->lote; // relación Enfermedad belongsTo Lote
        if (!$lote) return;

        foreach ($this->destinatarios($lote) as $user) {
            $user->notify(new AlertaSanidadNotification(
                'Registro de enfermedad',
                "Lote {$lote->codigo}: se registró {$e->nombre} el {$e->fecha->toDateString()}",
                [
                    'tipo' => 'enfermedad',
                    'lote_id' => $lote->id,
                    'enfermedad' => $e->nombre,
                    'fecha' => $e->fecha->toDateString(),
                ]
            ));
        }
    }

    private function destinatarios($lote)
    {
        return \App\Models\User::role('admin')->get();
    }
}
