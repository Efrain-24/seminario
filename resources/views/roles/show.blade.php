<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalles del Rol: ') . $role->display_name }}
            </h2>
            <div class="flex space-x-3">
                @if(auth()->user()->hasPermission('roles.update'))
                    <a href="{{ route('roles.edit', $role) }}" 
                       style="background-color: #9333ea !important; color: white !important;"
                       class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar Rol
                    </a>
                @endif
                
                @if(auth()->user()->hasPermission('roles.delete'))
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este rol? Esta acción no se puede deshacer y afectará a todos los usuarios que tengan este rol.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                style="background-color: #ef4444 !important; color: white !important;"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Eliminar Rol
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Información del Rol -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-center">
                                <!-- Avatar del rol -->
                                <div class="mx-auto h-24 w-24 rounded-full bg-purple-500 flex items-center justify-center text-white font-bold text-2xl mb-4">
                                    {{ strtoupper(substr($role->display_name, 0, 1)) }}
                                </div>
                                
                                <!-- Información básica -->
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ $role->display_name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    {{ $role->name }}
                                </p>

                                <!-- Estado -->
                                <div class="mb-4">
                                    @if($role->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Inactivo
                                        </span>
                                    @endif
                                </div>

                                <!-- Estadísticas -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                            {{ $role->users_count ?? 0 }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            Usuarios Asignados
                                        </div>
                                    </div>
                                </div>

                                <!-- Fecha de creación -->
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <p>Creado el {{ $role->created_at->format('d/m/Y H:i') }}</p>
                                    @if($role->updated_at != $role->created_at)
                                        <p>Actualizado el {{ $role->updated_at->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenido principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Descripción -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Descripción del Rol
                            </h4>
                            <p class="text-gray-700 dark:text-gray-300">
                                {{ $role->description ?? 'Sin descripción proporcionada.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Permisos -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Permisos Asignados
                            </h4>
                            
                            @if($permissionsData['userPermissions'] && count($permissionsData['userPermissions']) > 0)
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($permissionsData['groupedPermissions'] as $moduleKey => $permissions)
                                        @if(isset($permissionsData['modules'][$moduleKey]))
                                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                                <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                                    <div class="h-3 w-3 rounded-full bg-purple-500 mr-2"></div>
                                                    {{ $permissionsData['modules'][$moduleKey] }}
                                                </h5>
                                                <div class="grid grid-cols-2 gap-2">
                                                    @foreach($permissions as $level)
                                                        @if($level === 'gestionar')
                                                            <div class="flex items-center p-2 bg-gray-50 dark:bg-gray-700 rounded text-xs col-span-2">
                                                                <svg class="w-3 h-3 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                <span class="text-gray-700 dark:text-gray-300">Acceso Completo</span>
                                                            </div>
                                                        @elseif(isset($permissionsData['permissionLevels'][$level]))
                                                            <div class="flex items-center p-2 bg-gray-50 dark:bg-gray-700 rounded text-xs">
                                                                <svg class="w-3 h-3 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                <span class="text-gray-700 dark:text-gray-300">{{ $permissionsData['permissionLevels'][$level] }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-10a3 3 0 00-3 3v1m0 0h6m-6 0v4"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sin permisos asignados</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Este rol no tiene permisos específicos asignados.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Usuarios asignados -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Usuarios con este Rol
                            </h4>
                            
                            @if($users && $users->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Usuario
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Email
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Fecha de Registro
                                                </th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Acciones
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($users as $user)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-8 w-8">
                                                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-700 font-medium text-sm">
                                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                                </div>
                                                            </div>
                                                            <div class="ml-3">
                                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                    {{ $user->name }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                        {{ $user->email }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                        {{ $user->created_at->format('d/m/Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <a href="{{ route('users.show', $user) }}" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">
                                                            Ver Usuario
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sin usuarios asignados</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ningún usuario tiene este rol asignado actualmente.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
