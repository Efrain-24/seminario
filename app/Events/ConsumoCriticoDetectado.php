<?php

namespace App\Events;

use App\Models\InventarioItem;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsumoCriticoDetectado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public InventarioItem $item;
    public float $cantidadPlanificada;
    public float $cantidadReal;
    public string $razon;

    /**
     * Create a new event instance.
     */
    public function __construct(InventarioItem $item, float $cantidadPlanificada, float $cantidadReal, string $razon)
    {
        $this->item = $item;
        $this->cantidadPlanificada = $cantidadPlanificada;
        $this->cantidadReal = $cantidadReal;
        $this->razon = $razon;
    }
}