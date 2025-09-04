<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class PasswordReset extends Notification
{
    use Queueable;

    protected $temporaryPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct($temporaryPassword)
    {
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $changePasswordUrl = route('password.change');

        return (new MailMessage)
            ->subject('Contraseña Reiniciada - Sistema de Gestión Piscícola')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Tu contraseña ha sido reiniciada por el administrador.')
            ->line('**Nueva contraseña temporal: **' . $this->temporaryPassword . '**')
            ->line('Por seguridad, debes cambiar esta contraseña temporal al iniciar sesión.')
            ->action('Cambiar Contraseña', $changePasswordUrl)
            ->line('Si no solicitaste este cambio, contacta al administrador inmediatamente.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}