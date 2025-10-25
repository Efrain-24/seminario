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
            'dashboard' => 'Dashboard',
            'usuarios_roles' => 'Usuarios y Roles',
            'unidades' => 'Unidades de Producción',
            'produccion' => 'Producción',
            'inventarios' => 'Inventarios',
            'tipos_alimentos' => 'Tipos de Alimentos',
            'acciones_correctivas' => 'Acciones Correctivas',
            'protocolos_limpieza' => 'Protocolos y Limpieza',
            'ventas' => 'Ventas (Cosechas)',
            'compras_proveedores' => 'Compras y Proveedores',
            'reportes' => 'Reportes',
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
            // Dashboard
            'ver_dashboard' => 'Ver dashboard',
            'crear_dashboard' => 'Crear dashboard',
            'editar_dashboard' => 'Editar dashboard',
            'eliminar_dashboard' => 'Eliminar dashboard',
            
            // Usuarios y Roles
            'ver_usuarios_roles' => 'Ver usuarios y roles',
            'crear_usuarios_roles' => 'Crear usuarios y roles',
            'editar_usuarios_roles' => 'Editar usuarios y roles',
            'eliminar_usuarios_roles' => 'Eliminar usuarios y roles',
            
            // Unidades de Producción
            'ver_unidades' => 'Ver unidades',
            'crear_unidades' => 'Crear unidades',
            'editar_unidades' => 'Editar unidades',
            'eliminar_unidades' => 'Eliminar unidades',
            
            // Producción
            'ver_produccion' => 'Ver producción',
            'crear_produccion' => 'Crear producción',
            'editar_produccion' => 'Editar producción',
            'eliminar_produccion' => 'Eliminar producción',
            
            // Inventarios
            'ver_inventarios' => 'Ver inventarios',
            'crear_inventarios' => 'Crear inventarios',
            'editar_inventarios' => 'Editar inventarios',
            'eliminar_inventarios' => 'Eliminar inventarios',
            
            // Tipos de Alimentos (parte de inventarios)
            'ver_tipos_alimentos' => 'Ver tipos de alimentos',
            'crear_tipos_alimentos' => 'Crear tipos de alimentos',
            'editar_tipos_alimentos' => 'Editar tipos de alimentos',
            'eliminar_tipos_alimentos' => 'Eliminar tipos de alimentos',
            
            // Acciones Correctivas
            'ver_acciones_correctivas' => 'Ver acciones correctivas',
            'crear_acciones_correctivas' => 'Crear acciones correctivas',
            'editar_acciones_correctivas' => 'Editar acciones correctivas',
            'eliminar_acciones_correctivas' => 'Eliminar acciones correctivas',
            
            // Protocolos y Limpieza
            'ver_protocolos_limpieza' => 'Ver protocolos y limpieza',
            'crear_protocolos_limpieza' => 'Crear protocolos y limpieza',
            'editar_protocolos_limpieza' => 'Editar protocolos y limpieza',
            'eliminar_protocolos_limpieza' => 'Eliminar protocolos y limpieza',
            
            // Ventas (Cosechas)
            'ver_ventas' => 'Ver ventas',
            'crear_ventas' => 'Crear ventas',
            'editar_ventas' => 'Editar ventas',
            'eliminar_ventas' => 'Eliminar ventas',
            
            // Compras y Proveedores
            'ver_compras_proveedores' => 'Ver compras y proveedores',
            'crear_compras_proveedores' => 'Crear compras y proveedores',
            'editar_compras_proveedores' => 'Editar compras y proveedores',
            'eliminar_compras_proveedores' => 'Eliminar compras y proveedores',
            
            // Reportes
            'ver_reportes' => 'Ver reportes',
            'crear_reportes' => 'Crear reportes',
            'editar_reportes' => 'Editar reportes',
            'eliminar_reportes' => 'Eliminar reportes',
        ];
    }
}
