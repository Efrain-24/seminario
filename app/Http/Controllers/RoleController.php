<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(): View
    {
        $roles = Role::withCount('users')->orderBy('display_name')->paginate(10);
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $availablePermissions = $this->getAvailablePermissions();
        return view('roles.create', compact('availablePermissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'alpha_dash'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        // Validación case-insensitive para nombre único
        $exists = Role::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->exists();
        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Ya existe un rol con ese nombre.']);
        }

        // Validación de permisos duplicados
        $permissions = $request->permissions ?? [];
        $similarRoles = Role::getSimilarRolesInfo($permissions);
        
        if (!empty($similarRoles)) {
            $roleNames = collect($similarRoles)->pluck('display_name')->implode(', ');
            $message = count($similarRoles) === 1 
                ? "No se puede crear el rol. Ya existe un rol ({$roleNames}) con exactamente los mismos permisos."
                : "No se puede crear el rol. Ya existen roles ({$roleNames}) con exactamente los mismos permisos.";
                
            return back()
                ->withInput()
                ->withErrors(['permissions' => $message])
                ->with('similar_roles', $similarRoles);
        }

        Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $permissions,
            'is_active' => true,
        ]);

        return redirect()->route('roles.index')
                        ->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): View
    {
        $role->loadCount('users');
        $users = $role->users()->orderBy('name')->get();
        
        // Procesar permisos para la vista
        $permissionsData = $this->processRolePermissions($role);
        
        return view('roles.show', compact('role', 'users', 'permissionsData'));
    }

    /**
     * Process role permissions for display
     */
    private function processRolePermissions(Role $role): array
    {
        $userPermissions = $role->getPermissionsArray();
        
        $modules = [
            'gestionar_usuarios' => 'Gestión de Usuarios',
            'gestionar_roles' => 'Gestión de Roles', 
            'unidades' => 'Unidades de Producción',
            'lotes' => 'Gestión de Lotes',
            'mantenimientos' => 'Mantenimientos',
            'alimentacion' => 'Alimentación',
            'sanidad' => 'Sanidad',
            'crecimiento' => 'Crecimiento',
            'costos' => 'Costos',
            'monitoreo' => 'Monitoreo Ambiental'
        ];
        
        $permissionLevels = [
            'view' => 'Ver',
            'create' => 'Crear', 
            'edit' => 'Editar',
            'delete' => 'Eliminar'
        ];
        
        // Agrupar permisos por módulo
        $groupedPermissions = [];
        
        foreach($userPermissions as $permission) {
            // Para permisos especiales como gestionar_usuarios, gestionar_roles
            if (str_contains($permission, 'gestionar_usuarios') || str_contains($permission, 'gestionar_roles')) {
                $module = str_contains($permission, 'gestionar_usuarios') ? 'gestionar_usuarios' : 'gestionar_roles';
                if(!isset($groupedPermissions[$module])) {
                    $groupedPermissions[$module] = [];
                }
                // Extraer la acción del permiso gestionar_usuarios.view -> view
                $parts = explode('.', $permission);
                if(count($parts) === 2) {
                    $action = $parts[1];
                    if(isset($permissionLevels[$action])) {
                        $groupedPermissions[$module][] = $action;
                    }
                }
                continue;
            }
            
            // Para permisos con formato nuevo: modulo.accion (ej: unidades.view, lotes.create)
            $parts = explode('.', $permission);
            if(count($parts) === 2) {
                $module = $parts[0];
                $action = $parts[1];
                
                if(isset($modules[$module]) && isset($permissionLevels[$action])) {
                    if(!isset($groupedPermissions[$module])) {
                        $groupedPermissions[$module] = [];
                    }
                    $groupedPermissions[$module][] = $action;
                }
            }
        }
        
        return [
            'userPermissions' => $userPermissions,
            'modules' => $modules,
            'permissionLevels' => $permissionLevels,
            'groupedPermissions' => $groupedPermissions
        ];
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        $availablePermissions = $this->getAvailablePermissions();
        return view('roles.edit', compact('role', 'availablePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'alpha_dash'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
            'is_active' => ['boolean'],
        ]);

        // Validación case-insensitive para nombre único (excepto el actual)
        $exists = Role::whereRaw('LOWER(name) = ?', [strtolower($request->name)])
            ->where('id', '!=', $role->id)
            ->exists();
        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Ya existe un rol con ese nombre (sin importar mayúsculas/minúsculas).']);
        }

        // Validación de permisos duplicados (excluyendo el rol actual)
        $permissions = $request->permissions ?? [];
        $similarRoles = Role::getSimilarRolesInfo($permissions, $role->id);
        
        if (!empty($similarRoles)) {
            $roleNames = collect($similarRoles)->pluck('display_name')->implode(', ');
            $message = count($similarRoles) === 1 
                ? "No se puede actualizar el rol. Ya existe un rol ({$roleNames}) con exactamente los mismos permisos."
                : "No se puede actualizar el rol. Ya existen roles ({$roleNames}) con exactamente los mismos permisos.";
                
            return back()
                ->withInput()
                ->withErrors(['permissions' => $message])
                ->with('similar_roles', $similarRoles);
        }

        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $permissions,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('roles.index')
                        ->with('success', 'Rol actualizado exitosamente.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // Verificar si el rol tiene usuarios asignados
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                            ->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('roles.index')
                        ->with('success', 'Rol eliminado exitosamente.');
    }

    /**
     * Get available permissions for roles
     */
    private function getAvailablePermissions(): array
    {
        return [
            // Usuarios
            'gestionar_usuarios' => 'Gestionar usuarios',
            'ver_usuarios' => 'Ver usuarios',
            'crear_usuarios' => 'Crear usuarios',
            'editar_usuarios' => 'Editar usuarios',
            'eliminar_usuarios' => 'Eliminar usuarios',
            
            // Roles
            'gestionar_roles' => 'Gestionar roles',
            'ver_roles' => 'Ver roles',
            'crear_roles' => 'Crear roles',
            'editar_roles' => 'Editar roles',
            'eliminar_roles' => 'Eliminar roles',
            
            // Unidades de Producción
            'ver_unidades' => 'Ver unidades',
            'crear_unidades' => 'Crear unidades',
            'editar_unidades' => 'Editar unidades',
            'eliminar_unidades' => 'Eliminar unidades',
            
            // Lotes
            'ver_lotes' => 'Ver lotes',
            'crear_lotes' => 'Crear lotes',
            'editar_lotes' => 'Editar lotes',
            'eliminar_lotes' => 'Eliminar lotes',
            
            // Mantenimientos
            'ver_mantenimientos' => 'Ver mantenimientos',
            'crear_mantenimientos' => 'Crear mantenimientos',
            'editar_mantenimientos' => 'Editar mantenimientos',
            'eliminar_mantenimientos' => 'Eliminar mantenimientos',
            
            // Alimentación
            'ver_alimentacion' => 'Ver alimentación',
            'crear_alimentacion' => 'Crear alimentación',
            'editar_alimentacion' => 'Editar alimentación',
            'eliminar_alimentacion' => 'Eliminar alimentación',
            
            // Sanidad
            'ver_sanidad' => 'Ver sanidad',
            'crear_sanidad' => 'Crear sanidad',
            'editar_sanidad' => 'Editar sanidad',
            'eliminar_sanidad' => 'Eliminar sanidad',
            
            // Monitoreo Ambiental
            'ver_monitoreo' => 'Ver monitoreo',
            'crear_monitoreo' => 'Crear monitoreo',
            'editar_monitoreo' => 'Editar monitoreo',
            'eliminar_monitoreo' => 'Eliminar monitoreo',
            
            // Crecimiento
            'ver_crecimiento' => 'Ver crecimiento',
            'crear_crecimiento' => 'Crear crecimiento',
            'editar_crecimiento' => 'Editar crecimiento',
            'eliminar_crecimiento' => 'Eliminar crecimiento',
            
            // Costos
            'ver_costos' => 'Ver costos',
            'crear_costos' => 'Crear costos',
            'editar_costos' => 'Editar costos',
            'eliminar_costos' => 'Eliminar costos',
        ];
    }
}
