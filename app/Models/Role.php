<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_active'
    ];

    protected $casts = [
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
}
