<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Aplicaciones') }} - Sistema de Gestión Piscícola
            </h2>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Bienvenido, <span class="font-medium">{{ Auth::user()->name }}</span>
                ({{ Auth::user()->roleDisplayName }})
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensaje de Bienvenida -->
            <div class="bg-gradient-to-r from-blue-500 to-cyan-500 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-white">
                    <h3 class="text-lg font-semibold mb-2">¡Accede a las Aplicaciones del Sistema!</h3>
                    <p class="text-blue-100">Haz clic en cualquier aplicación para acceder directamente a sus funciones.
                    </p>
                </div>
            </div>

            <!-- Módulos Disponibles -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Aplicaciones Disponibles
                    </h3>

                    @php
                        // Definir las aplicaciones principales del sistema
                        $moduleDefinitions = [
                            'unidades' => [
                                'name' => 'Unidades de Producción',
                                'description' => 'Gestión de estanques y áreas de cultivo acuícola',
                                'icon' =>
                                    '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12c0-2 1-4 3-5 2-1 4 0 5 2 1-2 3-3 5-2 2 1 3 3 3 5 0 2-1 4-3 5-2 1-4 0-5-2-1 2-3 3-5 2-2-1-3-3-3-5z"/><path d="M6 8c1-1 2-1 3 0 1-1 2-1 3 0 1-1 2-1 3 0 1-1 2-1 3 0"/><path d="M6 16c1 1 2 1 3 0 1 1 2 1 3 0 1 1 2 1 3 0 1 1 2 1 3 0"/></svg>',
                                'route' => 'produccion.unidades',
                                'permission_prefix' => 'unidades',
                                'color' => 'from-blue-500 to-cyan-600',
                                'available' => true,
                            ],
                            'lotes' => [
                                'name' => 'Gestión de Lotes',
                                'description' => 'Administración de lotes de peces y ciclos productivos',
                                'icon' =>
                                    '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73L12 2 4 6.27A2 2 0 003 8v8a2 2 0 001 1.73L12 22l8-4.27A2 2 0 0021 16z"/><polyline points="3.29,7 12,12 20.71,7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>',
                                'route' => 'produccion.lotes',
                                'permission_prefix' => 'lotes',
                                'color' => 'from-orange-500 to-red-600',
                                'available' => true,
                            ],
                            'alimentacion' => [
                                'name' => 'Alimentación',
                                'description' => 'Registra y controla la alimentación de los peces',
                                'icon' =>
                                    '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="8" r="2"/><circle cx="16" cy="8" r="2"/><circle cx="8" cy="16" r="2"/><circle cx="16" cy="16" r="2"/><circle cx="12" cy="12" r="3"/><circle cx="4" cy="12" r="1"/><circle cx="20" cy="12" r="1"/><circle cx="12" cy="4" r="1"/><circle cx="12" cy="20" r="1"/></svg>',
                                'route' => 'alimentacion.index',
                                'permission_prefix' => 'alimentacion',
                                'color' => 'from-green-500 to-emerald-600',
                                'available' => true,
                            ],
                            'mantenimientos' => [
                                'name' => 'Mantenimientos',
                                'description' => 'Gestión y control de mantenimientos de infraestructura acuícola',
                                'icon' =>
                                    '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>',
                                'route' => 'produccion.mantenimientos',
                                'permission_prefix' => 'mantenimientos',
                                'color' => 'from-purple-500 to-indigo-600',
                                'available' => true,
                            ],
                            'usuarios' => [
                                'name' => 'Gestión de Usuarios',
                                'description' => 'Administra usuarios y sus datos en el sistema',
                                'icon' =>
                                    '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                                'route' => 'users.index',
                                'permission_prefix' => 'usuarios',
                                'color' => 'from-rose-500 to-pink-600',
                                'available' => true,
                            ],
                            'roles' => [
                                'name' => 'Roles y Permisos',
                                'description' => 'Configura roles y permisos de acceso al sistema',
                                'icon' =>
                                    '<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>',
                                'route' => 'roles.index',
                                'permission_prefix' => 'roles',
                                'color' => 'from-teal-500 to-green-600',
                                'available' => true,
                            ],
                        ];

                        // Filtrar módulos según permisos del usuario
                        $userModules = [];
                        foreach ($moduleDefinitions as $key => $module) {
                            // Para usuarios, usar el permiso gestionar_usuarios
                            if ($module['permission_prefix'] === 'usuarios') {
                                if (Gate::allows('gestionar_usuarios')) {
                                    $userModules[$key] = $module;
                                }
                            } elseif ($module['permission_prefix'] === 'alimentacion') {
                                // Para alimentación, usar el permiso alimentacion.view
                                if (Gate::allows('alimentacion.view')) {
                                    $userModules[$key] = $module;
                                }
                            } else {
                                if (Gate::allows('ver_' . $module['permission_prefix'])) {
                                    $userModules[$key] = $module;
                                }
                            }
                        }
                    @endphp

                    @if (!empty($userModules))
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($userModules as $moduleKey => $module)
                                @php
                                    $hasPermission = false;
                                    if ($module['permission_prefix'] === 'usuarios') {
                                        $hasPermission = Gate::allows('gestionar_usuarios');
                                    } elseif ($module['permission_prefix'] === 'alimentacion') {
                                        $hasPermission = Gate::allows('alimentacion.view');
                                    } else {
                                        $hasPermission = Gate::allows('ver_' . $module['permission_prefix']);
                                    }
                                @endphp
                                @if ($module['available'] && $hasPermission)
                                    <div class="bg-gradient-to-br {{ $module['color'] }} rounded-xl p-6 text-white hover:scale-105 transition-all duration-300 cursor-pointer shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                                        onclick="window.location.href='{{ route($module['route']) }}'">
                                        <div class="flex items-start justify-between mb-4">
                                            <div
                                                class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                                {!! $module['icon'] !!}
                                            </div>
                                            <div class="opacity-50">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path d="M5 12h14" />
                                                    <path d="M12 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>

                                        <div>
                                            <h4 class="text-lg font-bold mb-2">{{ $module['name'] }}</h4>
                                            <p class="text-sm opacity-90">{{ $module['description'] }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-6 relative overflow-hidden">
                                        <div class="flex items-start justify-between mb-4">
                                            <div
                                                class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-xl flex items-center justify-center">
                                                {!! $module['icon'] !!}
                                            </div>
                                        </div>

                                        <div>
                                            <h4 class="text-lg font-bold mb-2 text-gray-600 dark:text-gray-300">
                                                {{ $module['name'] }}</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $module['description'] }}</p>
                                            <div class="mt-3 inline-flex items-center text-sm text-gray-400">
                                                <svg class="mr-1 w-4 h-4" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <polyline points="12,6 12,12 16,14" />
                                                </svg>
                                                Próximamente
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <circle cx="12" cy="16" r="1" />
                                    <path d="M7 11V7a5 5 0 0110 0v4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Sin acceso a
                                aplicaciones</h3>
                            <p class="text-gray-600 dark:text-gray-400">No tienes permisos para acceder a ninguna
                                aplicación. Contacta al administrador para obtener acceso.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
