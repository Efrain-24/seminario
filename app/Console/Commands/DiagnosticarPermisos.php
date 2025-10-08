<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DiagnosticarPermisos extends Command
{
    protected $signature = 'permisos:diagnosticar {user_id} {permiso?}';
    protected $description = 'Muestra los permisos efectivos de un usuario y prueba uno opcional.';

    public function handle(): int
    {
        $userId = $this->argument('user_id');
        $permiso = $this->argument('permiso');
        $user = User::find($userId);
        if (!$user) {
            $this->error('Usuario no encontrado');
            return self::FAILURE;
        }
        $this->info("Usuario: {$user->id} - {$user->name} (rol: {$user->role})");
        $role = $user->roleModel;
        if (!$role) {
            $this->warn('Rol no encontrado o inactivo.');
        }
        $perms = $role ? $role->getPermissionsArray() : [];
        $this->line('Permisos del rol:');
        foreach ($perms as $p) {
            $this->line(' - ' . $p);
        }
        if ($permiso) {
            $this->line('---------------------------------------');
            $this->line("Probando permiso: $permiso");
            $resultado = $user->hasPermission($permiso) ? 'SI' : 'NO';
            $this->info("Resultado hasPermission('$permiso'): $resultado");
        }
        return self::SUCCESS;
    }
}
