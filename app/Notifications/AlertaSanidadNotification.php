<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AlertaSanidadNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $titulo,
        public string $mensaje,
        public array $metadata = []
    ) {}

    public function via($notifiable): array
    {
        return config('sanidad.notificacion_canal', ['database']);
    }

    public function toArray($notifiable): array
    {
        return [
            'titulo'   => $this->titulo,
            'mensaje'  => $this->mensaje,
            'metadata' => $this->metadata,
        ];
    }

    // Si quieres correo:
    // public function toMail($notifiable) { ... }
}
