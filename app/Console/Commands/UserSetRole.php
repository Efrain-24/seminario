<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class UserSetRole extends Command
{
    protected $signature = 'user:set-role {email : Correo del usuario} {role : Rol a asignar} {--verify : Verificar correo si no está verificado} {--modules= : Lista separada por comas de módulos a asignar al rol si no existen} {--add-permissions= : Lista separada por comas de permisos a agregar al rol (sin duplicar)}';

    protected $description = 'Asignar rol a un usuario, opcionalmente verificar el correo, asegurar módulos y agregar permisos';

    public function handle(): int
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');
        $verify = $this->option('verify');
    $modulesOpt = $this->option('modules');
    $addPermissionsOpt = $this->option('add-permissions');

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Usuario con correo {$email} no encontrado");
            return self::FAILURE;
        }

        // Crear rol si no existe
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $role = Role::create([
                'name' => $roleName,
                'display_name' => ucfirst($roleName),
                'is_active' => true,
            ]);
            $this->info("Rol '{$roleName}' creado.");
        }

        $oldRole = $user->role;
        $user->role = $roleName;
        $user->save();
        $this->info("Rol del usuario cambiado de '{$oldRole}' a '{$roleName}'.");

        // Limpiar módulos personalizados del usuario para que herede del rol
        if ($user->modules()->count() > 0) {
            $user->modules()->delete();
            $this->info('Módulos personalizados del usuario eliminados (ahora heredará del rol).');
        }

        // Asignar módulos al rol si se pasan explícitamente
        if ($modulesOpt) {
            $modules = collect(explode(',', $modulesOpt))
                ->map(fn($m) => trim($m))
                ->filter();
            if ($modules->count()) {
                $role->modules()->delete();
                foreach ($modules as $m) {
                    $role->modules()->create(['module' => $m]);
                }
                $this->info('Módulos asignados al rol: ' . $modules->implode(', '));
            }
        }

        // Agregar permisos adicionales sin eliminar los existentes
        if ($addPermissionsOpt) {
            $permsToAdd = collect(explode(',', $addPermissionsOpt))
                ->map(fn($p) => trim($p))
                ->filter();
            if ($permsToAdd->count()) {
                $current = $role->getPermissionsArray();
                $merged = collect($current)->merge($permsToAdd)->unique()->values()->toArray();
                $role->permissions = $merged;
                $role->save();
                $this->info('Permisos agregados al rol: ' . $permsToAdd->implode(', '));
            }
        }

        if ($verify && !$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            $this->info('Correo verificado.');
        } elseif ($verify) {
            $this->info('El correo ya estaba verificado.');
        }

        $this->table(['ID','Nombre','Correo','Rol','Verificado'], [[
            $user->id,
            $user->name,
            $user->email,
            $user->role,
            $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : 'NO'
        ]]);

        return self::SUCCESS;
    }
}
