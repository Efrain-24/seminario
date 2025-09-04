<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'estado',
        'password_changed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Generar una contraseña temporal segura de 8 caracteres
     */
    public static function generateTemporaryPassword(): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $specials = '@$!%*#?&._-';
        
        // Asegurar al menos uno de cada tipo
        $password = '';
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)]; // minúscula
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)]; // mayúscula
        $password .= $numbers[rand(0, strlen($numbers) - 1)]; // número
        $password .= $specials[rand(0, strlen($specials) - 1)]; // especial
        
        // Completar los 4 caracteres restantes aleatoriamente
        $allChars = $lowercase . $uppercase . $numbers . $specials;
        for ($i = 4; $i < 8; $i++) {
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }
        
        // Mezclar los caracteres para que no sigan un patrón
        return str_shuffle($password);
    }

    /**
     * Marcar que el usuario necesita cambiar su contraseña
     */
    public function markPasswordAsTemporary(): void
    {
        $this->update(['password_changed_at' => null]);
    }

    /**
     * Verificar si el usuario tiene una contraseña temporal
     */
    public function hasTemporaryPassword(): bool
    {
        return is_null($this->password_changed_at);
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'role' => 'generico',
    ];

    /**
     * Scope para usuarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Get the role display name
     */
    public function getRoleDisplayNameAttribute(): string
    {
        $role = \App\Models\Role::where('name', $this->role)->first();
        return $role ? $role->display_name : ucfirst($this->role);
    }

    /**
     * Relación con rol
     */
    public function roleModel()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role', 'name');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is supervisor
     */
    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Admin tiene acceso a todo automáticamente
        if ($this->isAdmin()) {
            return true;
        }
        
        // Get the user's role from database
        $role = \App\Models\Role::where('name', $this->role)->where('is_active', true)->first();
        
        if (!$role) {
            return false;
        }
        
        return in_array($permission, $role->permissions ?? []);
    }

    /**
     * Check if user can access a module
     */
    public function canAccessModule(string $module): bool
    {
        $role = \App\Models\Role::where('name', $this->role)->where('is_active', true)->first();
        
        if (!$role) {
            return false;
        }
        
        // Check if user has any permission for this module with the new naming convention
        $modulePermissions = collect($role->permissions ?? [])->filter(function($permission) use ($module) {
            return str_starts_with($permission, 'ver_' . $module) || 
                   str_starts_with($permission, 'crear_' . $module) || 
                   str_starts_with($permission, 'editar_' . $module) || 
                   str_starts_with($permission, 'eliminar_' . $module) ||
                   $permission === $module;
        });
        
        return $modulePermissions->isNotEmpty();
    }

    /**
     * Get user permissions for a specific module
     */
    public function getModulePermissions(string $module): array
    {
        $role = \App\Models\Role::where('name', $this->role)->where('is_active', true)->first();
        
        if (!$role) {
            return [];
        }
        
        $modulePermissions = collect($role->permissions ?? [])->filter(function($permission) use ($module) {
            return str_starts_with($permission, 'ver_' . $module) || 
                   str_starts_with($permission, 'crear_' . $module) || 
                   str_starts_with($permission, 'editar_' . $module) || 
                   str_starts_with($permission, 'eliminar_' . $module) ||
                   $permission === $module;
        })->map(function($permission) use ($module) {
            // Extract the action from permission name (ver_, crear_, editar_, eliminar_)
            if (str_contains($permission, '_')) {
                $parts = explode('_', $permission, 2);
                if (count($parts) === 2 && $parts[1] === $module) {
                    return $parts[0]; // Return the action (ver, crear, editar, eliminar)
                }
                if (str_starts_with($permission, 'ver_' . $module)) return 'ver';
                if (str_starts_with($permission, 'crear_' . $module)) return 'crear';
                if (str_starts_with($permission, 'editar_' . $module)) return 'editar';
                if (str_starts_with($permission, 'eliminar_' . $module)) return 'eliminar';
            }
            return $permission;
        })->toArray();
        
        return $modulePermissions;
    }

    /**
     * Get all accessible modules for the user
     */
    public function getAccessibleModules(): array
    {
        // Admin tiene acceso a todos los módulos automáticamente
        if ($this->isAdmin()) {
            return [
                'users' => 'Gestión de Usuarios',
                'roles' => 'Gestión de Roles',
                'production' => 'Producción',
                'inventory' => 'Inventario',
                'sales' => 'Ventas',
                'reports' => 'Reportes',
                'finances' => 'Finanzas',
                'system' => 'Sistema'
            ];
        }
        
        $role = \App\Models\Role::where('name', $this->role)->where('is_active', true)->first();
        
        if (!$role) {
            return [];
        }
        
        $modules = [];
        
        // Mapear permisos específicos a módulos de navegación
        $permissions = $role->permissions ?? [];
        
        // Gestión de usuarios
        if (collect($permissions)->contains(fn($p) => str_contains($p, 'usuarios') || str_contains($p, 'gestionar_usuarios'))) {
            $modules['users'] = 'Gestión de Usuarios';
        }
        
        // Gestión de roles
        if (collect($permissions)->contains(fn($p) => str_contains($p, 'roles') || str_contains($p, 'gestionar_roles'))) {
            $modules['roles'] = 'Gestión de Roles';
        }
        
        // Producción (unidades, lotes, mantenimientos, alimentación, sanidad, crecimiento, monitoreo)
        if (collect($permissions)->contains(fn($p) => 
            str_contains($p, 'unidades') || str_contains($p, 'lotes') || 
            str_contains($p, 'mantenimientos') || str_contains($p, 'alimentacion') ||
            str_contains($p, 'sanidad') || str_contains($p, 'crecimiento') ||
            str_contains($p, 'monitoreo')
        )) {
            $modules['production'] = 'Producción';
        }
        
        // Inventario
        if (collect($permissions)->contains(fn($p) => str_contains($p, 'inventario'))) {
            $modules['inventory'] = 'Inventario';
        }
        
        // Ventas
        if (collect($permissions)->contains(fn($p) => str_contains($p, 'ventas'))) {
            $modules['sales'] = 'Ventas';
        }
        
        // Reportes
        if (collect($permissions)->contains(fn($p) => str_contains($p, 'reportes'))) {
            $modules['reports'] = 'Reportes';
        }
        
        // Finanzas/Costos
        if (collect($permissions)->contains(fn($p) => str_contains($p, 'costos') || str_contains($p, 'finanzas'))) {
            $modules['finances'] = 'Finanzas';
        }
        
        // Sistema
        if (collect($permissions)->contains(fn($p) => str_contains($p, 'sistema'))) {
            $modules['system'] = 'Sistema';
        }
        
        return $modules;
    }

    /**
     * Get available roles from database
     */
    public static function getAvailableRoles(): array
    {
        $roles = \App\Models\Role::active()->pluck('display_name', 'name')->toArray();
        
        // Fallback si no hay roles en la base de datos
        if (empty($roles)) {
            return [
                'admin' => 'Administrador',
                'supervisor' => 'Supervisor', 
                'usuario' => 'Usuario'
            ];
        }
        
        return $roles;
    }
}
