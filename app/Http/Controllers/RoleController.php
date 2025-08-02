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
            'name' => ['required', 'string', 'max:255', 'unique:roles', 'alpha_dash'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
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
        return view('roles.show', compact('role', 'users'));
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
            'name' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:roles,name,'.$role->id],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
            'is_active' => ['boolean'],
        ]);

        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
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
            'user_create' => 'Crear usuarios',
            'user_read' => 'Ver usuarios',
            'user_update' => 'Editar usuarios', 
            'user_delete' => 'Eliminar usuarios',
            'role_create' => 'Crear roles',
            'role_read' => 'Ver roles',
            'role_update' => 'Editar roles',
            'role_delete' => 'Eliminar roles',
            'dashboard_access' => 'Acceso al dashboard',
            'reports_access' => 'Acceso a reportes',
        ];
    }
}
