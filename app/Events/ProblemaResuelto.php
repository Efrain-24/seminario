<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProblemaResuelto
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tipoProblema;
    public $identificador;
    public $datos;

    /**
     * Create a new event instance.
     */
    public function __construct(string $tipoProblema, $identificador, array $datos = [])
    {
        $this->tipoProblema = $tipoProblema;
        $this->identificador = $identificador;
        $this->datos = $datos;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
