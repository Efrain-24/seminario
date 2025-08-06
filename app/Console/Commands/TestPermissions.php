<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class TestPermissions extends Command
{
    protected $signature = 'test:permissions {email?}';
    protected $description = 'Test the permission system for a specific user';

    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario no encontrado: {$email}");
            return 1;
        }
        
        $this->info("=== PRUEBA DEL SISTEMA DE PERMISOS ===");
        $this->info("Usuario: {$user->name} ({$user->email})");
        $this->info("Rol: {$user->role} - {$user->roleDisplayName}");
        $this->newLine();
        
        // Probar módulos accesibles
        $this->info("MÓDULOS ACCESIBLES:");
        $accessibleModules = $user->getAccessibleModules();
        
        if (empty($accessibleModules)) {
            $this->warn("- Sin acceso a módulos");
        } else {
            foreach ($accessibleModules as $moduleKey => $moduleName) {
                $this->line("- {$moduleKey}: {$moduleName}");
            }
        }
        $this->newLine();
        
        // Probar permisos específicos por módulo
        $modules = ['users', 'roles', 'production', 'inventory', 'sales', 'reports', 'finances', 'maintenance', 'system'];
        
        $this->info("PERMISOS DETALLADOS POR MÓDULO:");
        foreach ($modules as $module) {
            $permissions = $user->getModulePermissions($module);
            
            if (!empty($permissions)) {
                $this->info("📁 {$module}:");
                foreach ($permissions as $permission) {
                    $icon = match($permission) {
                        'view' => '👀',
                        'create' => '➕',
                        'edit' => '✏️',
                        'delete' => '🗑️',
                        default => '📝'
                    };
                    $this->line("  {$icon} {$permission}");
                }
            } else {
                $this->line("❌ {$module}: Sin acceso");
            }
        }
        $this->newLine();
        
        // Verificar algunos permisos específicos
        $this->info("VERIFICACIÓN DE PERMISOS ESPECÍFICOS:");
        $testPermissions = [
            'users.view' => 'Ver usuarios',
            'users.create' => 'Crear usuarios',
            'roles.edit' => 'Editar roles',
            'production.delete' => 'Eliminar producción',
            'system.view' => 'Ver sistema'
        ];
        
        foreach ($testPermissions as $permission => $description) {
            $hasPermission = $user->hasPermission($permission);
            $icon = $hasPermission ? '✅' : '❌';
            $this->line("{$icon} {$description} ({$permission})");
        }
        
        $this->newLine();
        $this->info("=== FIN DE LA PRUEBA ===");
        
        return 0;
    }
}
