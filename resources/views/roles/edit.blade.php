<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Rol: ') . $role->display_name }}
        </h2>
    </x-slot>

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
                        
                        <a href="{{ route('roles.show', $role) }}" 
                           style="background-color: #2563eb !important; color: white !important;"
                           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Rol
                        </a>
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
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $role->users_count ?? 0 }} usuarios asignados</p>
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
                                </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Formulario -->
                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre del rol -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nombre del Rol *
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $role->name) }}"
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
                                       value="{{ old('display_name', $role->display_name) }}"
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
                                      placeholder="Describe las responsabilidades y características de este rol...">{{ old('description', $role->description) }}</textarea>
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
                                
                                $currentPermissions = old('permissions', $role->getPermissionsArray());
                            @endphp

                            <div class="space-y-6">
                                @foreach($modules as $moduleKey => $moduleName)
                                    @php
                                        $hasModulePermissions = collect($currentPermissions)->filter(function($perm) use ($moduleKey) {
                                            return str_starts_with($perm, $moduleKey.'.');
                                        })->isNotEmpty();
                                    @endphp
                                    
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
                                                           {{ $hasModulePermissions ? 'checked' : '' }}
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
                                        
                                                                                <div class="permission-levels-{{ $moduleKey }} grid grid-cols-2 md:grid-cols-4 gap-3" style="opacity: {{ $hasModulePermissions ? '1' : '0.5' }}; pointer-events: {{ $hasModulePermissions ? 'auto' : 'none' }}; transition: opacity 0.3s ease;">
                                            @foreach($permissionLevels as $levelKey => $levelName)
                                                <label class="flex items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                                    <input type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $moduleKey }}.{{ $levelKey }}"
                                                           class="permission-checkbox-{{ $moduleKey }} rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                                           {{ in_array($moduleKey.'.'.$levelKey, $currentPermissions) ? 'checked' : '' }}>
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
                                       {{ old('is_active', $role->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Rol activo</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Los roles inactivos no pueden ser asignados a usuarios</p>
                            
                            @if($role->users_count > 0 && !$role->is_active)
                                <div class="mt-2 p-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                                    <p class="text-sm">
                                        ⚠️ Este rol tiene {{ $role->users_count }} usuarios asignados. Desactivarlo podría afectar su acceso al sistema.
                                    </p>
                                </div>
                            @endif
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
                                Actualizar Rol
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
        // Obtener permisos actuales del rol
        const currentPermissions = {!! json_encode($role->getPermissionsArray()) !!};
        
        // Lista de módulos
        const modules = {
            'gestionar_usuarios': 'Gestión de Usuarios',
            'gestionar_roles': 'Gestión de Roles', 
            'unidades': 'Unidades de Producción',
            'lotes': 'Gestión de Lotes',
            'mantenimientos': 'Mantenimientos',
            'alimentacion': 'Alimentación',
            'sanidad': 'Sanidad',
            'crecimiento': 'Crecimiento',
            'costos': 'Costos',
            'monitoreo': 'Monitoreo Ambiental'
        };
        
        // Verificar cada módulo y habilitar si tiene permisos
        Object.keys(modules).forEach(moduleKey => {
            const hasModulePermissions = currentPermissions.some(perm => perm.startsWith(moduleKey + '.'));
            const moduleToggle = document.querySelector(`[data-module="${moduleKey}"]`);
            
            if (moduleToggle) {
                if (hasModulePermissions) {
                    moduleToggle.checked = true;
                    toggleModulePermissions(moduleKey);
                } else {
                    moduleToggle.checked = false;
                    toggleModulePermissions(moduleKey);
                }
            }
        });
    });
    // Activar todos los permisos de un módulo
    function checkAllPermissions(moduleKey) {
        const moduleToggle = document.querySelector(`[data-module="${moduleKey}"]`);
        const permissionLevels = document.querySelector(`.permission-levels-${moduleKey}`);
        const checkboxes = document.querySelectorAll(`.permission-checkbox-${moduleKey}`);
        if (moduleToggle && permissionLevels) {
            moduleToggle.checked = true;
            permissionLevels.style.opacity = '1';
            permissionLevels.style.pointerEvents = 'auto';
        }
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    }
    </script>
</x-app-layout>
