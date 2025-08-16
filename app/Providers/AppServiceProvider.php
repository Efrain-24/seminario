<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Definir los Gates de permisos
        Gate::define('gestionar_usuarios', function (User $user) {
            return $user->hasPermission('gestionar_usuarios');
        });
        
        Gate::define('gestionar_roles', function (User $user) {
            return $user->hasPermission('gestionar_roles');
        });

        // Permiso específico para ver roles
        Gate::define('ver_roles', function (User $user) {
            return $user->hasPermission('ver_roles') || $user->hasPermission('gestionar_roles');
        });
        
        // Gates para Unidades
        Gate::define('ver_unidades', function (User $user) {
            return $user->hasPermission('ver_unidades');
        });
        
        Gate::define('crear_unidades', function (User $user) {
            return $user->hasPermission('crear_unidades');
        });
        
        Gate::define('editar_unidades', function (User $user) {
            return $user->hasPermission('editar_unidades');
        });
        
        Gate::define('eliminar_unidades', function (User $user) {
            return $user->hasPermission('eliminar_unidades');
        });
        
        // Gates para Lotes
        Gate::define('ver_lotes', function (User $user) {
            return $user->hasPermission('ver_lotes');
        });
        
        Gate::define('crear_lotes', function (User $user) {
            return $user->hasPermission('crear_lotes');
        });
        
        Gate::define('editar_lotes', function (User $user) {
            return $user->hasPermission('editar_lotes');
        });
        
        Gate::define('eliminar_lotes', function (User $user) {
            return $user->hasPermission('eliminar_lotes');
        });
        
        // Gates para Mantenimientos
        Gate::define('ver_mantenimientos', function (User $user) {
            return $user->hasPermission('ver_mantenimientos');
        });
        
        Gate::define('crear_mantenimientos', function (User $user) {
            return $user->hasPermission('crear_mantenimientos');
        });
        
        Gate::define('editar_mantenimientos', function (User $user) {
            return $user->hasPermission('editar_mantenimientos');
        });
        
        Gate::define('eliminar_mantenimientos', function (User $user) {
            return $user->hasPermission('eliminar_mantenimientos');
        });
        
        // Gates para otros módulos (para futuro uso)
        Gate::define('ver_alimentacion', function (User $user) {
            return $user->hasPermission('ver_alimentacion');
        });
        
        Gate::define('crear_alimentacion', function (User $user) {
            return $user->hasPermission('crear_alimentacion');
        });
        
        Gate::define('editar_alimentacion', function (User $user) {
            return $user->hasPermission('editar_alimentacion');
        });
        
        Gate::define('eliminar_alimentacion', function (User $user) {
            return $user->hasPermission('eliminar_alimentacion');
        });
        
        Gate::define('ver_sanidad', function (User $user) {
            return $user->hasPermission('ver_sanidad');
        });
        
        Gate::define('crear_sanidad', function (User $user) {
            return $user->hasPermission('crear_sanidad');
        });
        
        Gate::define('editar_sanidad', function (User $user) {
            return $user->hasPermission('editar_sanidad');
        });
        
        Gate::define('eliminar_sanidad', function (User $user) {
            return $user->hasPermission('eliminar_sanidad');
        });
        
        Gate::define('ver_crecimiento', function (User $user) {
            return $user->hasPermission('ver_crecimiento');
        });
        
        Gate::define('crear_crecimiento', function (User $user) {
            return $user->hasPermission('crear_crecimiento');
        });
        
        Gate::define('editar_crecimiento', function (User $user) {
            return $user->hasPermission('editar_crecimiento');
        });
        
        Gate::define('eliminar_crecimiento', function (User $user) {
            return $user->hasPermission('eliminar_crecimiento');
        });
        
        Gate::define('ver_costos', function (User $user) {
            return $user->hasPermission('ver_costos');
        });
        
        Gate::define('crear_costos', function (User $user) {
            return $user->hasPermission('crear_costos');
        });
        
        Gate::define('editar_costos', function (User $user) {
            return $user->hasPermission('editar_costos');
        });
        
        Gate::define('eliminar_costos', function (User $user) {
            return $user->hasPermission('eliminar_costos');
        });
        
        Gate::define('ver_monitoreo', function (User $user) {
            return $user->hasPermission('ver_monitoreo');
        });
        
        Gate::define('crear_monitoreo', function (User $user) {
            return $user->hasPermission('crear_monitoreo');
        });
        
        Gate::define('editar_monitoreo', function (User $user) {
            return $user->hasPermission('editar_monitoreo');
        });
        
        Gate::define('eliminar_monitoreo', function (User $user) {
            return $user->hasPermission('eliminar_monitoreo');
        });
    }
}
