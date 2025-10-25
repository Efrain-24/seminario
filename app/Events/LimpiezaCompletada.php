<?php

namespace App\Events;

use App\Models\Limpieza;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LimpiezaCompletada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Limpieza $limpieza;
    public array $insumosConsumidos;

    /**
     * Create a new event instance.
     */
    public function __construct(Limpieza $limpieza, array $insumosConsumidos = [])
    {
        $this->limpieza = $limpieza;
        $this->insumosConsumidos = $insumosConsumidos;
    }
}