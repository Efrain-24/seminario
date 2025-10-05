<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;

class RepararPermisosRoles extends Command
{
    protected $signature = 'roles:reparar-permisos';
    protected $description = 'Repara la codificación de permisos en la tabla roles (quita doble codificación JSON)';

    public function handle(): int
    {
        $roles = Role::all();
        $reparados = 0;
        foreach ($roles as $role) {
            $perms = $role->permissions;
            if (is_string($perms)) {
                $decoded = json_decode($perms, true);
                // Si el resultado es un string, decodificar de nuevo
                if (is_string($decoded)) {
                    $decoded = json_decode($decoded, true);
                }
                if (is_array($decoded)) {
                    $role->permissions = $decoded;
                    $role->save();
                    $this->info("Rol {$role->name} reparado");
                    $reparados++;
                }
            }
        }
        $this->info("Total roles reparados: $reparados");
        return self::SUCCESS;
    }
}
