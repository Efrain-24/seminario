<?php

namespace App\Events;

use App\Models\InventarioItem;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockBajoDetectado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public InventarioItem $item;
    public float $stockActual;

    /**
     * Create a new event instance.
     */
    public function __construct(InventarioItem $item, float $stockActual)
    {
        $this->item = $item;
        $this->stockActual = $stockActual;
    }
}
