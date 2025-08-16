<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualizar permisos de roles existentes
        $rolesPermisos = [
            'admin' => [
                'users.view', 'users.create', 'users.edit', 'users.delete',
                'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                'production.view', 'production.create', 'production.edit', 'production.delete',
                'unidades.view', 'unidades.create', 'unidades.edit', 'unidades.delete',
                'lotes.view', 'lotes.create', 'lotes.edit', 'lotes.delete',
                'mantenimientos.view', 'mantenimientos.create', 'mantenimientos.edit', 'mantenimientos.delete',
                'editar_mantenimientos',
                'alimentacion.view', 'alimentacion.create', 'alimentacion.edit', 'alimentacion.delete',
                'sanidad.view', 'sanidad.create', 'sanidad.edit', 'sanidad.delete',
                'crecimiento.view', 'crecimiento.create', 'crecimiento.edit', 'crecimiento.delete',
                'costos.view', 'costos.create', 'costos.edit', 'costos.delete',
                'monitoreo_ambiental.view', 'monitoreo_ambiental.create', 'monitoreo_ambiental.edit', 'monitoreo_ambiental.delete',
                'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
                'sales.view', 'sales.create', 'sales.edit', 'sales.delete',
                'reports.view', 'reports.create', 'reports.edit', 'reports.delete',
                'finances.view', 'finances.create', 'finances.edit', 'finances.delete',
                'system.view', 'system.create', 'system.edit', 'system.delete'
            ],
            'manager' => [
                'users.view', 'users.edit',
                'roles.view',
                'production.view', 'production.create', 'production.edit', 'production.delete',
                'unidades.view', 'unidades.create', 'unidades.edit', 'unidades.delete',
                'lotes.view', 'lotes.create', 'lotes.edit', 'lotes.delete',
                'mantenimientos.view', 'mantenimientos.create', 'mantenimientos.edit', 'mantenimientos.delete',
                'editar_mantenimientos',
                'alimentacion.view', 'alimentacion.create', 'alimentacion.edit', 'alimentacion.delete',
                'sanidad.view', 'sanidad.create', 'sanidad.edit', 'sanidad.delete',
                'crecimiento.view', 'crecimiento.create', 'crecimiento.edit', 'crecimiento.delete',
                'costos.view', 'costos.create', 'costos.edit', 'costos.delete',
                'monitoreo_ambiental.view', 'monitoreo_ambiental.create', 'monitoreo_ambiental.edit', 'monitoreo_ambiental.delete',
                'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
                'sales.view', 'sales.create', 'sales.edit',
                'reports.view', 'reports.create',
                'finances.view'
            ],
            'empleado' => [
                'users.view',
                'production.view', 'production.create', 'production.edit',
                'unidades.view', 'unidades.create', 'unidades.edit',
                'lotes.view', 'lotes.create', 'lotes.edit',
                'mantenimientos.view', 'mantenimientos.create',
                'alimentacion.view', 'alimentacion.create', 'alimentacion.edit',
                'sanidad.view', 'sanidad.create', 'sanidad.edit',
                'crecimiento.view', 'crecimiento.create', 'crecimiento.edit',
                'costos.view', 'costos.create',
                'monitoreo_ambiental.view', 'monitoreo_ambiental.create', 'monitoreo_ambiental.edit',
                'inventory.view', 'inventory.create',
                'sales.view', 'sales.create',
                'reports.view'
            ],
            'generico' => [
                'production.view',
                'unidades.view',
                'lotes.view',
                'mantenimientos.view',
                'alimentacion.view',
                'sanidad.view',
                'crecimiento.view',
                'costos.view',
                'monitoreo_ambiental.view',
                'inventory.view',
                'sales.view',
                'reports.view'
            ]
        ];

        foreach ($rolesPermisos as $roleName => $permisos) {
            Role::where('name', $roleName)->update([
                'permissions' => $permisos
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a permisos anteriores si es necesario
        $rolesPermisosAnteriores = [
            'admin' => [
                'users.view', 'users.create', 'users.edit', 'users.delete',
                'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                'production.view', 'production.create', 'production.edit', 'production.delete',
                'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
                'sales.view', 'sales.create', 'sales.edit', 'sales.delete',
                'reports.view', 'reports.create', 'reports.edit', 'reports.delete',
                'finances.view', 'finances.create', 'finances.edit', 'finances.delete',
                'maintenance.view', 'maintenance.create', 'maintenance.edit', 'maintenance.delete',
                'system.view', 'system.create', 'system.edit', 'system.delete'
            ],
            'manager' => [
                'users.view', 'users.edit',
                'roles.view',
                'production.view', 'production.create', 'production.edit', 'production.delete',
                'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.delete',
                'sales.view', 'sales.create', 'sales.edit',
                'reports.view', 'reports.create',
                'finances.view',
                'maintenance.view', 'maintenance.create'
            ],
            'empleado' => [
                'users.view',
                'production.view', 'production.create', 'production.edit',
                'inventory.view', 'inventory.create',
                'sales.view', 'sales.create',
                'reports.view',
                'maintenance.view', 'maintenance.create'
            ],
            'generico' => [
                'production.view',
                'inventory.view',
                'sales.view',
                'reports.view'
            ]
        ];

        foreach ($rolesPermisosAnteriores as $roleName => $permisos) {
            Role::where('name', $roleName)->update([
                'permissions' => $permisos
            ]);
        }
    }
};
