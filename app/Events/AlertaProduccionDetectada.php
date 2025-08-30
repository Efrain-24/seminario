<?php

namespace App\Events;

use App\Models\Lote;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlertaProduccionDetectada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lote $lote;
    public array $datosAlerta;

    /**
     * Create a new event instance.
     */
    public function __construct(Lote $lote, array $datosAlerta)
    {
        $this->lote = $lote;
        $this->datosAlerta = $datosAlerta;
    }
}
