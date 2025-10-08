<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Rol: ') . $role->display_name }}
        </h2>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Botones de navegación -->
                    <div class="mb-6 flex justify-between items-center">
                        <a href="{{ route('roles.index') }}" 
                           style="background-color: #4b5563 !important; color: white !important;"
                           class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Volver a Roles
                        </a>
                        
                        <div class="flex items-center">
                            <a href="{{ route('roles.show', $role) }}" 
                               style="background-color: #2563eb !important; color: white !important;"
                               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Ver Rol
                            </a>

                            <a href="{{ route('roles.ocultar-modulos', $role) }}"
                               style="background-color: #f59e42 !important; color: white !important;"
                               class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded inline-flex items-center ml-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-1.657.403-3.221 1.125-4.575M15 9h.01M19.938 19.938A10.05 10.05 0 0021 12c0-5.523-4.477-10-10-10S1 6.477 1 12c0 2.21.715 4.25 1.938 5.938M9 15h.01" />
                                </svg>
                                Ocultar módulos de aplicación
                            </a>
                        </div>
                    </div>

                    <!-- Información del rol -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                <div class="h-12 w-12 rounded-full bg-purple-500 flex items-center justify-center text-white font-semibold text-lg">
                                    {{ strtoupper(substr($role->display_name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $role->display_name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Usuario asignado: 
                                    @php
                                        $user = $role->users->first();
                                    @endphp
                                    {{ $user ? $user->name : 'Sin usuario asignado' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Alerta de roles similares -->
                    @if(session('similar_roles') && count(session('similar_roles')) > 0)
                        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                        ❌ No se puede actualizar el rol
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                        <p>Ya existen otros roles con exactamente los mismos permisos:</p>
                                        <ul class="mt-2 space-y-1">
                                            @foreach(session('similar_roles') as $similarRole)
                                                <li class="flex items-center justify-between bg-red-100 dark:bg-red-800/30 px-3 py-2 rounded">
                                                    <div>
                                                        <span class="font-medium">{{ $similarRole['display_name'] }}</span>
                                                        <span class="text-xs text-red-600 dark:text-red-400 ml-2">
                                                            ({{ $similarRole['permissions_count'] }} permisos, {{ $similarRole['users_count'] }} usuarios)
                                                        </span>
                                                    </div>
                                                    <a href="{{ route('roles.show', $similarRole['id']) }}" 
                                                       class="text-xs bg-red-200 dark:bg-red-700 text-red-800 dark:text-red-200 px-2 py-1 rounded hover:bg-red-300 dark:hover:bg-red-600"
                                                       target="_blank">
                                                        Ver detalles
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <p class="mt-3 text-xs">
                                            💡 <strong>Solución:</strong> Modifica los permisos para diferenciar este rol de los existentes.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Formulario -->
                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Permisos -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                Permisos del Rol
                            </label>
                            @php
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
                                $rolePermissions = old('permissions');
                                if (is_null($rolePermissions)) {
                                    // Si no hay old, usar el valor del modelo
                                    $perms = $role->permissions ?? [];
                                    if (is_string($perms)) {
                                        $decoded = json_decode($perms, true);
                                        $rolePermissions = is_array($decoded) ? $decoded : [];
                                    } elseif (is_array($perms)) {
                                        $rolePermissions = $perms;
                                    } else {
                                        $rolePermissions = [];
                                    }
                                }
                            @endphp
                            <div class="space-y-6">
                                @foreach($modules as $moduleKey => $moduleName)
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                {{ $moduleName }}
                                            </h4>
                                            <div class="flex items-center space-x-2">
                                                <label class="flex items-center">
                                                    <input type="checkbox" 
                                                           class="module-toggle rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                                           data-module="{{ $moduleKey }}"
                                                           onchange="toggleModulePermissions('{{ $moduleKey }}')">
                                                    <span class="ml-2 text-xs text-gray-600 dark:text-gray-400">Habilitar módulo</span>
                                                </label>
                                                <button type="button" onclick="checkAllPermissions('{{ $moduleKey }}')"
                                                    class="ml-2 px-2 py-1 bg-purple-500 hover:bg-purple-700 text-white text-xs rounded transition duration-150"
                                                    title="Activar todos los permisos de este módulo">
                                                    Activar todos
                                                </button>
                                            </div>
                                        </div>
                                        <div class="permission-levels-{{ $moduleKey }} grid grid-cols-2 md:grid-cols-4 gap-3" style="opacity: 1; pointer-events: auto; transition: opacity 0.3s ease;">
                                            @foreach($permissionLevels as $levelKey => $levelName)
                                                <label class="flex items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                               <input type="checkbox" 
                                   name="permissions[]" 
                                   value="{{ $moduleKey }}.{{ $levelKey }}"
                                   class="permission-checkbox-{{ $moduleKey }} rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                   {{ in_array($moduleKey.'.'.$levelKey, $rolePermissions) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $levelName }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8 flex justify-end space-x-4">
                            <a href="{{ route('roles.index') }}" 
                               style="background-color: #d1d5db !important; color: #1f2937 !important;"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    style="background-color: #9333ea !important; color: white !important;"
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                                Actualizar Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Habilita/deshabilita los checkboxes de permisos según el estado del toggle de módulo
    function toggleModulePermissions(moduleKey) {
        const toggle = document.querySelector(`input[data-module='${moduleKey}']`);
        const container = document.querySelector(`.permission-levels-${moduleKey}`);
        if (!toggle || !container) return;
        const checkboxes = container.querySelectorAll('input[type=checkbox]');
        if (toggle.checked) {
            container.style.opacity = '1';
            container.style.pointerEvents = 'auto';
            checkboxes.forEach(cb => cb.disabled = false);
        } else {
            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';
            checkboxes.forEach(cb => cb.disabled = true);
        }
    }
    // Activa todos los permisos de un módulo
    function checkAllPermissions(moduleKey) {
        const container = document.querySelector(`.permission-levels-${moduleKey}`);
        if (!container) return;
        const checkboxes = container.querySelectorAll('input[type=checkbox]');
        checkboxes.forEach(cb => { if (!cb.disabled) cb.checked = true; });
    }
    // Al cargar la página, activa los toggles de módulos que tengan algún permiso marcado
    window.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-module]').forEach(function(toggle) {
            const moduleKey = toggle.getAttribute('data-module');
            const container = document.querySelector(`.permission-levels-${moduleKey}`);
            if (!container) return;
            const checkboxes = container.querySelectorAll('input[type=checkbox]');
            const algunoMarcado = Array.from(checkboxes).some(cb => cb.checked);
            toggle.checked = algunoMarcado;
            toggleModulePermissions(moduleKey);
        });
    });
    </script>
</x-app-layout>
