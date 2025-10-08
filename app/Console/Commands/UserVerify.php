<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;

class UserVerify extends Command
{
    protected $signature = 'user:verify {email : Correo del usuario}';

    protected $description = 'Marcar como verificado el correo de un usuario';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Usuario con correo {$email} no encontrado");
            return self::FAILURE;
        }
        if ($user->hasVerifiedEmail()) {
            $this->info('El correo ya estaba verificado.');
        } else {
            $user->markEmailAsVerified();
            $this->info('Correo verificado correctamente.');
        }
        $this->table(['ID','Nombre','Correo','Verificado'], [[
            $user->id,
            $user->name,
            $user->email,
            $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : 'NO'
        ]]);
        return self::SUCCESS;
    }
}
