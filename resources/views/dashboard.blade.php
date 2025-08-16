<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }} - Sistema de Gestión Piscícola
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
                    <h3 class="text-lg font-semibold mb-2">¡Bienvenido al Sistema de Gestión Piscícola!</h3>
                    <p class="text-blue-100">Gestiona de manera integral todos los aspectos de tu cultivo piscícola desde este panel central.</p>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                @can('ver_unidades')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Unidades</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">12 unidades activas</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                @can('ver_lotes')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Lotes</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">8 lotes en producción</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                @can('ver_mantenimientos')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Mantenimientos</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">3 pendientes</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            </div>

            <!-- Resumen General de Producción -->
            @can('ver_unidades')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Resumen General de Producción</h3>
                    
                    <!-- Navegación de Pestañas -->
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="-mb-px flex space-x-8">
                            <button onclick="showTab('resumen')" id="tab-resumen" class="tab-button py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-blue-500 text-blue-600 dark:text-blue-400">
                                Resumen General
                            </button>
                            <button onclick="showTab('unidades')" id="tab-unidades" class="tab-button py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                                Unidades de Producción
                            </button>
                            <button onclick="showTab('mantenimientos')" id="tab-mantenimientos" class="tab-button py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                                Mantenimientos Recientes
                            </button>
                        </nav>
                    </div>

                    <!-- Contenido de las Pestañas -->
                    <div class="mt-6">
                        <!-- Pestaña Resumen General -->
                        <div id="content-resumen" class="tab-content">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-green-800 dark:text-green-200">Total Unidades</p>
                                            <p class="text-2xl font-bold text-green-900 dark:text-green-100">12</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Unidades Activas</p>
                                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">8</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Mantenimientos Pendientes</p>
                                            <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">3</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña Unidades de Producción -->
                        <div id="content-unidades" class="tab-content hidden">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                                            <span class="text-white font-bold">P1</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Estanque Principal 001</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Capacidad: 10,000L • Tipo: Estanque</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full dark:bg-green-900 dark:text-green-200 mr-2">Activo</span>
                                        <span class="text-sm text-gray-500">Lote: LT-2024-001</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                                            <span class="text-white font-bold">J1</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Jaula Flotante 001</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Capacidad: 5,000L • Tipo: Jaula</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full dark:bg-green-900 dark:text-green-200 mr-2">Activo</span>
                                        <span class="text-sm text-gray-500">Lote: LT-2024-002</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-400 rounded-lg flex items-center justify-center mr-4">
                                            <span class="text-white font-bold">T1</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Tanque de Alevines 001</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Capacidad: 2,000L • Tipo: Tanque</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full dark:bg-red-900 dark:text-red-200 mr-2">Mantenimiento</span>
                                        <span class="text-sm text-gray-500">Sin lote</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña Mantenimientos -->
                        <div id="content-mantenimientos" class="tab-content hidden">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border-l-4 border-yellow-400">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-4">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Limpieza de filtros - Tanque T1</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Programado: 18 Agosto 2025 • Tipo: Preventivo</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full dark:bg-yellow-900 dark:text-yellow-200">Pendiente</span>
                                </div>
                                
                                <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-400">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Revisión de bombas - Estanque P1</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Completado: 15 Agosto 2025 • Tipo: Correctivo</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full dark:bg-green-900 dark:text-green-200">Completado</span>
                                </div>
                                
                                <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border-l-4 border-green-400">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Calibración de oxímetros - Jaula J1</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Completado: 14 Agosto 2025 • Tipo: Preventivo</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full dark:bg-green-900 dark:text-green-200">Completado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan

            <script>
                function showTab(tabName) {
                    // Ocultar todos los contenidos
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Mostrar el contenido seleccionado
                    document.getElementById('content-' + tabName).classList.remove('hidden');
                    
                    // Actualizar estilos de las pestañas
                    document.querySelectorAll('.tab-button').forEach(button => {
                        button.className = 'tab-button py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300';
                    });
                    
                    // Activar la pestaña seleccionada
                    document.getElementById('tab-' + tabName).className = 'tab-button py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap border-blue-500 text-blue-600 dark:text-blue-400';
                }

                // Inicializar con la primera pestaña activa
                document.addEventListener('DOMContentLoaded', function() {
                    showTab('resumen');
                });

                function loadOverviewData() {
                    // Simular carga de datos del resumen
                    console.log('Cargando datos del resumen general...');
                }

                function loadUnitsData() {
                    // Simular carga de datos de unidades
                    console.log('Cargando datos de unidades...');
                }

                function loadMaintenanceData() {
                    // Simular carga de datos de mantenimientos
                    console.log('Cargando datos de mantenimientos...');
                }
            </script>

            <!-- Módulos Disponibles -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Módulos Disponibles</h3>
                    
                    @php
                        // Definir todos los módulos disponibles con sus rutas y permisos
                        $moduleDefinitions = [
                            'usuarios' => [
                                'name' => 'Gestión de Usuarios',
                                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
                                'route' => 'users.index',
                                'permission_prefix' => 'usuarios',
                                'available' => true
                            ],
                            'roles' => [
                                'name' => 'Gestión de Roles',
                                'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                                'route' => 'roles.index',
                                'permission_prefix' => 'roles',
                                'available' => true
                            ],
                            'unidades' => [
                                'name' => 'Unidades de Producción',
                                'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547A8.014 8.014 0 004 21h4.838a2 2 0 001.414-.586l.172-.172a2 2 0 012.828 0l.172.172A2 2 0 0014.838 21H19a8.014 8.014 0 00-.572-5.572z',
                                'route' => 'produccion.unidades',
                                'permission_prefix' => 'unidades',
                                'available' => true
                            ],
                            'lotes' => [
                                'name' => 'Gestión de Lotes',
                                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                'route' => 'produccion.lotes',
                                'permission_prefix' => 'lotes',
                                'available' => true
                            ],
                            'mantenimientos' => [
                                'name' => 'Mantenimientos',
                                'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                                'route' => 'produccion.mantenimientos',
                                'permission_prefix' => 'mantenimientos',
                                'available' => true
                            ],
                            'alimentacion' => [
                                'name' => 'Alimentación',
                                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                'route' => 'alimentacion.index',
                                'permission_prefix' => 'alimentacion',
                                'available' => false
                            ],
                            'sanidad' => [
                                'name' => 'Sanidad',
                                'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                'route' => 'sanidad.index',
                                'permission_prefix' => 'sanidad',
                                'available' => false
                            ],
                            'monitoreo' => [
                                'name' => 'Monitoreo Ambiental',
                                'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                                'route' => 'monitoreo.index',
                                'permission_prefix' => 'monitoreo',
                                'available' => false
                            ],
                            'crecimiento' => [
                                'name' => 'Crecimiento',
                                'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                                'route' => 'crecimiento.index',
                                'permission_prefix' => 'crecimiento',
                                'available' => false
                            ],
                            'costos' => [
                                'name' => 'Costos',
                                'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4',
                                'route' => 'costos.index',
                                'permission_prefix' => 'costos',
                                'available' => false
                            ]
                        ];
                        
                        // Filtrar módulos según permisos del usuario
                        $userModules = [];
                        foreach ($moduleDefinitions as $key => $module) {
                            if (Gate::allows('ver_' . $module['permission_prefix'])) {
                                $userModules[$key] = $module;
                            }
                        }
                    @endphp

                    @if(!empty($userModules))
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($userModules as $moduleKey => $module)
                                @php
                                    $prefix = $module['permission_prefix'];
                                    $canView = Gate::allows('ver_' . $prefix);
                                    $canCreate = Gate::allows('crear_' . $prefix);
                                    $canEdit = Gate::allows('editar_' . $prefix);
                                    $canDelete = Gate::allows('eliminar_' . $prefix);
                                @endphp
                                
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $module['name'] }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Permisos disponibles:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @if($canView)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full dark:bg-green-900 dark:text-green-300">Ver</span>
                                            @endif
                                            @if($canCreate)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full dark:bg-blue-900 dark:text-blue-300">Crear</span>
                                            @endif
                                            @if($canEdit)
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full dark:bg-yellow-900 dark:text-yellow-300">Editar</span>
                                            @endif
                                            @if($canDelete)
                                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full dark:bg-red-900 dark:text-red-300">Eliminar</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($module['available'] && $canView)
                                        <a href="{{ route($module['route']) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                            Acceder al módulo
                                            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <div class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Próximamente disponible
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H9m3-7V6a3 3 0 00-6 0v3"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Sin acceso a módulos</h3>
                            <p class="text-gray-600 dark:text-gray-400">No tienes permisos para acceder a ningún módulo. Contacta al administrador para obtener acceso.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
