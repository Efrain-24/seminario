<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Aplicaciones') }} - Sistema de Gestión Piscícola
            </h2>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Bienvenido, <span class="font-medium">{{ Auth::user() ? Auth::user()->name : 'Usuario' }}</span>
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
                                'icon' => '<i data-lucide="waves"></i>',
                                'route' => 'produccion.unidades',
                                'permission_prefix' => 'unidades',
                                'color' => 'from-blue-500 to-cyan-600',
                                'available' => true,
                            ],
                            'lotes' => [
                                'name' => 'Gestión de Lotes',
                                'description' => 'Administración de lotes de peces y ciclos productivos',
                                'icon' => '<i data-lucide="package"></i>',
                                'route' => 'produccion.lotes',
                                'permission_prefix' => 'lotes',
                                'color' => 'from-orange-500 to-red-600',
                                'available' => true,
                            ],
                            'alimentacion' => [
                                'name' => 'Alimentación',
                                'description' => 'Registra y controla la alimentación de los peces',
                                'icon' => '<i data-lucide="apple"></i>',
                                'route' => 'alimentacion.index',
                                'permission_prefix' => 'alimentacion',
                                'color' => 'from-green-500 to-emerald-600',
                                'available' => true,
                            ],
                            'mantenimientos' => [
                                'name' => 'Mantenimientos',
                                'description' => 'Gestión y control de mantenimientos de infraestructura acuícola',
                                'icon' => '<i data-lucide="wrench"></i>',
                                'route' => 'produccion.mantenimientos',
                                'permission_prefix' => 'mantenimientos',
                                'color' => 'from-purple-500 to-indigo-600',
                                'available' => true,
                            ],
                            'inventario' => [
                                'name' => 'Inventario',
                                'description' => 'Control de existencias, bodegas y movimientos de inventario',
                                'icon' => '<i data-lucide="boxes"></i>',
                                'route' => 'produccion.inventario.index',
                                'permission_prefix' => 'inventario',
                                'color' => 'from-amber-500 to-orange-600',
                                'available' => true,
                            ],
                            'cosechas' => [
                                'name' => 'Cosechas Parciales',
                                'description' => 'Registro y seguimiento de cosechas parciales y comercialización',
                                'icon' => '<i data-lucide="fish"></i>',
                                'route' => 'produccion.cosechas.index',
                                'permission_prefix' => 'cosechas',
                                'color' => 'from-emerald-500 to-teal-600',
                                'available' => true,
                            ],
                            'mortalidad' => [
                                'name' => 'Control de Mortalidad',
                                'description' => 'Registro y análisis de mortalidad con reportes estadísticos',
                                'icon' => '<i data-lucide="activity"></i>',
                                'route' => 'produccion.mortalidades.index',
                                'permission_prefix' => 'mortalidad',
                                'color' => 'from-red-500 to-pink-600',
                                'available' => true,
                            ],
                            'control_produccion' => [
                                'name' => 'Control de Producción',
                                'description' => 'Análisis de biomasa y predicciones de crecimiento',
                                'icon' => '<i data-lucide="trending-up"></i>',
                                'route' => 'produccion.control.index',
                                'permission_prefix' => 'control_produccion',
                                'color' => 'from-violet-500 to-purple-600',
                                'available' => true,
                            ],
                            'alertas' => [
                                'name' => 'Alertas y Anomalías',
                                'description' => 'Sistema de alertas automáticas y detección de anomalías',
                                'icon' => '<i data-lucide="alert-triangle"></i>',
                                'route' => 'produccion.alertas.index',
                                'permission_prefix' => 'alertas',
                                'color' => 'from-yellow-500 to-amber-600',
                                'available' => true,
                            ],
                            'sanidad' => [
                                'name' => 'Sanidad y Bioseguridad',
                                'description' => 'Registro de protocolos de sanidad y eventos de limpieza',
                                'icon' => '<i data-lucide="shield"></i>',
                                'route' => 'protocolo-sanidad.index',
                                'permission_prefix' => 'sanidad',
                                'color' => 'from-lime-500 to-green-600',
                                'available' => true,
                            ],
                            'usuarios' => [
                                'name' => 'Gestión de Usuarios',
                                'description' => 'Administra usuarios y sus datos en el sistema',
                                'icon' => '<i data-lucide="users"></i>',
                                'route' => 'users.index',
                                'permission_prefix' => 'usuarios',
                                'color' => 'from-rose-500 to-pink-600',
                                'available' => true,
                            ],
                            'roles' => [
                                'name' => 'Roles y Permisos',
                                'description' => 'Configura roles y permisos de acceso al sistema',
                                'icon' => '<i data-lucide="shield-check"></i>',
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
                            } elseif (in_array($module['permission_prefix'], ['inventario', 'cosechas', 'mortalidad', 'control_produccion', 'alertas'])) {
                                // Para los nuevos módulos, siempre mostrar (por ahora)
                                $userModules[$key] = $module;
                            } else {
                                if (Gate::allows('ver_' . $module['permission_prefix'])) {
                                    $userModules[$key] = $module;
                                }
                            }
                        }
                    @endphp

                    @if (!empty($userModules))
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach ($userModules as $moduleKey => $module)
                                @php
                                    $hasPermission = false;
                                    if ($module['permission_prefix'] === 'usuarios') {
                                        $hasPermission = Gate::allows('gestionar_usuarios');
                                    } elseif ($module['permission_prefix'] === 'alimentacion') {
                                        $hasPermission = Gate::allows('alimentacion.view');
                                    } elseif (in_array($module['permission_prefix'], ['inventario', 'cosechas', 'mortalidad', 'control_produccion', 'alertas'])) {
                                        // Para los nuevos módulos, siempre permitir acceso (por ahora)
                                        $hasPermission = true;
                                    } else {
                                        $hasPermission = Gate::allows('ver_' . $module['permission_prefix']);
                                    }
                                @endphp
                                @if ($module['available'] && $hasPermission)
                                    <div class="group bg-gradient-to-br {{ $module['color'] }} rounded-2xl p-6 text-white hover:scale-105 transition-all duration-300 cursor-pointer shadow-lg hover:shadow-2xl transform hover:-translate-y-2 relative overflow-hidden"
                                        onclick="window.location.href='{{ route($module['route']) }}'">
                                        <!-- Efecto de brillo -->
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>
                                        
                                        <div class="flex items-start justify-between mb-4 relative z-10">
                                            <div
                                                class="w-14 h-14 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-opacity-30 transition-all duration-300 group-hover:scale-110">
                                                {!! $module['icon'] !!}
                                            </div>
                                            <div class="opacity-60 group-hover:opacity-100 transition-opacity duration-300">
                                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path d="M5 12h14" />
                                                    <path d="M12 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="relative z-10">
                                            <h4 class="text-lg font-bold mb-2 group-hover:text-opacity-100">{{ $module['name'] }}</h4>
                                            <p class="text-sm opacity-90 group-hover:opacity-100 leading-relaxed">{{ $module['description'] }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl p-6 relative overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-600">
                                        <div class="flex items-start justify-between mb-4">
                                            <div
                                                class="w-14 h-14 bg-gray-300 dark:bg-gray-600 rounded-2xl flex items-center justify-center">
                                                <div class="text-gray-500 dark:text-gray-400">
                                                    {!! $module['icon'] !!}
                                                </div>
                                            </div>
                                            <div class="text-gray-400">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="15" y1="9" x2="9" y2="15" />
                                                    <line x1="9" y1="9" x2="15" y2="15" />
                                                </svg>
                                            </div>
                                        </div>

                                        <div>
                                            <h4 class="text-lg font-bold mb-2 text-gray-600 dark:text-gray-300">
                                                {{ $module['name'] }}</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                                {{ $module['description'] }}</p>
                                            <div class="inline-flex items-center text-sm text-red-500 bg-red-100 dark:bg-red-900 px-3 py-1 rounded-full">
                                                <svg class="mr-2 w-4 h-4" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="15" y1="9" x2="9" y2="15" />
                                                    <line x1="9" y1="9" x2="15" y2="15" />
                                                </svg>
                                                Sin permisos
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
