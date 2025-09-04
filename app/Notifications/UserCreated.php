<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class UserCreated extends Notification
{
    use Queueable;

    protected $temporaryPassword;
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $temporaryPassword)
    {
        $this->user = $user;
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
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Bienvenido al Sistema de Gestión Piscícola')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Tu cuenta ha sido creada exitosamente en el Sistema de Gestión Piscícola.')
            ->line('**Datos de acceso:**')
            ->line('Email: ' . $notifiable->email)
            ->line('Contraseña temporal: **' . $this->temporaryPassword . '**')
            ->line('Por seguridad, debes verificar tu correo electrónico y cambiar tu contraseña temporal.')
            ->action('Verificar Email y Cambiar Contraseña', $verificationUrl)
            ->line('Este enlace expira en 60 minutos.')
            ->line('Si no solicitaste esta cuenta, puedes ignorar este correo.');
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