<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;

class UserShow extends Command
{
    protected $signature = 'user:show {email : Correo del usuario}';

    protected $description = 'Mostrar información básica de un usuario';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Usuario con correo {$email} no encontrado");
            return self::FAILURE;
        }
        $this->table(['ID','Nombre','Correo','Rol','Verificado','Creado'], [[
            $user->id,
            $user->name,
            $user->email,
            $user->role,
            $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : 'NO',
            $user->created_at?->toDateTimeString(),
        ]]);
        return self::SUCCESS;
    }
}
