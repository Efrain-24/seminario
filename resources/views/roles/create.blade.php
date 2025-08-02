<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Nuevo Rol') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Botón de regreso -->
                    <div class="mb-6">
                        <a href="{{ route('roles.index') }}" 
                           style="background-color: #4b5563 !important; color: white !important;"
                           class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Volver a Roles
                        </a>
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre del rol -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nombre del Rol *
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-gray-100"
                                       placeholder="ej: manager"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nombre técnico en minúsculas, sin espacios</p>
                            </div>

                            <!-- Nombre para mostrar -->
                            <div>
                                <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nombre de Visualización *
                                </label>
                                <input type="text" 
                                       id="display_name" 
                                       name="display_name" 
                                       value="{{ old('display_name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-gray-100"
                                       placeholder="ej: Manager"
                                       required>
                                @error('display_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nombre que se mostrará en la interfaz</p>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Descripción
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-gray-100"
                                      placeholder="Describe las responsabilidades y características de este rol...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Permisos -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                Permisos del Rol
                            </label>
                            
                            @php
                                $modules = [
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
                                
                                $permissionLevels = [
                                    'view' => 'Ver',
                                    'create' => 'Crear', 
                                    'edit' => 'Editar',
                                    'delete' => 'Eliminar'
                                ];
                            @endphp

                            <div class="space-y-6">
                                @foreach($modules as $moduleKey => $moduleName)
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                {{ $moduleName }}
                                            </h4>
                                            <label class="flex items-center">
                                                <input type="checkbox" 
                                                       class="module-toggle rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                                       data-module="{{ $moduleKey }}"
                                                       onchange="toggleModulePermissions('{{ $moduleKey }}')">
                                                <span class="ml-2 text-xs text-gray-600 dark:text-gray-400">Habilitar módulo</span>
                                            </label>
                                        </div>
                                        
                                        <div class="permission-levels-{{ $moduleKey }} grid grid-cols-2 md:grid-cols-4 gap-3" style="opacity: 0.5; pointer-events: none; transition: opacity 0.3s ease;">
                                            @foreach($permissionLevels as $levelKey => $levelName)
                                                <label class="flex items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                                    <input type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $moduleKey }}.{{ $levelKey }}"
                                                           class="permission-checkbox-{{ $moduleKey }} rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                                           {{ in_array($moduleKey.'.'.$levelKey, old('permissions', [])) ? 'checked' : '' }}>
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



                        <!-- Estado -->
                        <div class="mt-6">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Rol activo</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Los roles inactivos no pueden ser asignados a usuarios</p>
                        </div>

                        <!-- Botones de acción -->
                        <div class="mt-8 flex justify-end space-x-4">
                            <a href="{{ route('roles.index') }}" 
                               style="background-color: #d1d5db !important; color: #1f2937 !important;"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    style="background-color: #9333ea !important; color: white !important;"
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                                Crear Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleModulePermissions(moduleKey) {
        const moduleToggle = document.querySelector(`[data-module="${moduleKey}"]`);
        const permissionLevels = document.querySelector(`.permission-levels-${moduleKey}`);
        const checkboxes = document.querySelectorAll(`.permission-checkbox-${moduleKey}`);
        
        if (moduleToggle && permissionLevels) {
            if (moduleToggle.checked) {
                // Habilitar módulo
                permissionLevels.style.opacity = '1';
                permissionLevels.style.pointerEvents = 'auto';
            } else {
                // Deshabilitar módulo
                permissionLevels.style.opacity = '0.5';
                permissionLevels.style.pointerEvents = 'none';
                // Desmarcar todos los checkboxes del módulo
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
        }
    }
    
    // Inicializar estado al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Lista de módulos
        const modules = {
            'users': 'Gestión de Usuarios',
            'roles': 'Gestión de Roles', 
            'production': 'Módulo de Producción',
            'inventory': 'Módulo de Inventario',
            'sales': 'Módulo de Ventas',
            'reports': 'Módulo de Reportes',
            'finances': 'Módulo de Finanzas',
            'maintenance': 'Módulo de Mantenimiento',
            'system': 'Configuración del Sistema'
        };
        
        // Verificar cada módulo si tiene permisos preseleccionados
        Object.keys(modules).forEach(moduleKey => {
            const modulePermissions = {!! json_encode(old('permissions', [])) !!};
            const hasModulePermissions = modulePermissions.some(perm => perm.startsWith(moduleKey + '.'));
            
            const moduleToggle = document.querySelector(`[data-module="${moduleKey}"]`);
            if (moduleToggle && hasModulePermissions) {
                moduleToggle.checked = true;
                toggleModulePermissions(moduleKey);
            }
        });
    });
    </script>
</x-app-layout>
