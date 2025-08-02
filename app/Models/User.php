<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
        
        // Check if user has any permission for this module
        $modulePermissions = collect($role->permissions ?? [])->filter(function($permission) use ($module) {
            return str_starts_with($permission, $module.'.');
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
            return str_starts_with($permission, $module.'.');
        })->map(function($permission) use ($module) {
            return str_replace($module.'.', '', $permission);
        })->toArray();
        
        return $modulePermissions;
    }

    /**
     * Get all accessible modules for the user
     */
    public function getAccessibleModules(): array
    {
        $allModules = [
            'users' => 'Gestión de Usuarios',
            'roles' => 'Gestión de Roles', 
            'production' => 'Módulo de Producción',
            'inventory' => 'Módulo de Inventario',
            'sales' => 'Módulo de Ventas',
            'reports' => 'Módulo de Reportes',
            'finances' => 'Módulo de Finanzas',
            'maintenance' => 'Módulo de Mantenimiento',
            'system' => 'Configuración del Sistema'
        ];
        
        // Admin tiene acceso a todos los módulos automáticamente
        if ($this->isAdmin()) {
            return $allModules;
        }
        
        $role = \App\Models\Role::where('name', $this->role)->where('is_active', true)->first();
        
        if (!$role) {
            return [];
        }
        
        $modules = [];
        foreach ($allModules as $moduleKey => $moduleName) {
            if ($this->canAccessModule($moduleKey)) {
                $modules[$moduleKey] = $moduleName;
            }
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
