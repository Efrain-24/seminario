<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    /**
     * Relación con los módulos visibles para el rol
     */
    public function modules()
    {
        return $this->hasMany(\App\Models\RoleModule::class);
    }
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Relación con usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role', 'name');
    }

    /**
     * Scope para roles activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtener permisos como array
     */
    public function getPermissionsListAttribute()
    {
        return $this->getPermissionsArray();
    }

    /**
     * Obtener permisos como array - método directo
     */
    public function getPermissionsArray()
    {
        // Obtener el valor raw directamente de la base de datos
        $permissions = $this->getRawOriginal('permissions') ?? $this->getOriginal('permissions') ?? $this->attributes['permissions'] ?? null;
        
        if (is_null($permissions) || $permissions === '') {
            return [];
        }
        
        if (is_string($permissions)) {
            // Primero intentar decodificar una vez
            $decoded = json_decode($permissions, true);
            
            // Si el resultado es aún un string, decodificar nuevamente (doble codificación)
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            
            return is_array($decoded) ? $decoded : [];
        }
        
        return is_array($permissions) ? $permissions : [];
    }

    /**
     * Buscar roles existentes con el mismo conjunto de permisos
     */
    public static function findRolesWithSamePermissions(array $permissions, ?int $excludeRoleId = null)
    {
        // Normalizar y ordenar los permisos para comparación
        $normalizedPermissions = collect($permissions)->filter()->sort()->values()->toArray();
        
        // Si no hay permisos, buscar otros roles sin permisos
        if (empty($normalizedPermissions)) {
            $query = self::active()
                ->where(function($q) {
                    $q->whereNull('permissions')
                      ->orWhere('permissions', '[]')
                      ->orWhere('permissions', '')
                      ->orWhere('permissions', '[""]')
                      ->orWhere('permissions', '{}');
                });
        } else {
            // Buscar roles con permisos exactamente iguales
            $query = self::active();
        }
        
        if ($excludeRoleId) {
            $query->where('id', '!=', $excludeRoleId);
        }
        
        return $query->get()->filter(function($role) use ($normalizedPermissions) {
            $rolePermissions = collect($role->getPermissionsArray())->filter()->sort()->values()->toArray();
            return $rolePermissions === $normalizedPermissions;
        });
    }

    /**
     * Verificar si ya existe un rol con los mismos permisos
     */
    public static function hasRoleWithSamePermissions(array $permissions, ?int $excludeRoleId = null): bool
    {
        return self::findRolesWithSamePermissions($permissions, $excludeRoleId)->count() > 0;
    }

    /**
     * Obtener información de roles con permisos similares
     */
    public static function getSimilarRolesInfo(array $permissions, ?int $excludeRoleId = null): array
    {
        $similarRoles = self::findRolesWithSamePermissions($permissions, $excludeRoleId);
        
        return $similarRoles->map(function($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description,
                'permissions_count' => count($role->getPermissionsArray()),
                'users_count' => $role->users()->count()
            ];
        })->toArray();
    }
}
