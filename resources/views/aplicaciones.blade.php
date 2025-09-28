{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Aplicaciones del Sistema')

@section('content')
<!-- Header manual -->
<header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Módulos del Sistema
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Aplicaciones del Sistema</p>
        </div>
    </div>
</header>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @php
                    // Aquí puedes mantener la definición de módulos si después quieres filtrar permisos
                    $moduleDefinitions = [
                        'unidades' => [
                            'name' => 'Unidades',
                            'description' => 'Gestión de unidades de producción y mantenimiento',
                            'icon' => '<i data-lucide="building-2"></i>',
                            'route' => 'unidades.panel',
                            'color' => 'from-blue-500 to-blue-700',
                            'available' => true,
                        ],
                        'produccion' => [
                            'name' => 'Producción',
                            'description' => 'Lotes, seguimientos, alimentación y tipos de alimentos',
                            'icon' => '<i data-lucide="factory"></i>',
                            'route' => 'produccion.panel',
                            'color' => 'from-green-500 to-emerald-600',
                            'available' => true,
                        ],
                        'inventario' => [
                            'name' => 'Inventarios',
                            'description' => 'Gestión de inventarios y traslados',
                            'icon' => '<i data-lucide="boxes"></i>',
                            'route' => 'inventarios.panel',
                            'color' => 'from-amber-500 to-orange-600',
                            'available' => true,
                        ],
                        'usuarios_roles' => [
                            'name' => 'Usuarios y Roles',
                            'description' => 'Gestión de usuarios, roles y permisos del sistema',
                            'icon' => '<i data-lucide="users"></i>',
                            'route' => 'usuarios.panel',
                            'color' => 'from-purple-500 to-indigo-600',
                            'available' => true,
                        ],
                        'acciones_correctivas' => [
                            'name' => 'Acciones Correctivas',
                            'description' => 'Gestión de no conformidades y acciones correctivas',
                            'icon' => '<i data-lucide="check-circle"></i>',
                            'route' => 'acciones-correctivas.panel',
                            'color' => 'from-red-500 to-pink-600',
                            'available' => true,
                        ],
                        'protocolos' => [
                            'name' => 'Protocolos y Limpieza',
                            'description' => 'Protocolos de limpieza y procedimientos operativos',
                            'icon' => '<i data-lucide="clipboard-check"></i>',
                            'route' => 'protocolos.panel',
                            'color' => 'from-indigo-500 to-indigo-700',
                            'available' => true,
                        ],
                        'ventas' => [
                            'name' => 'Ventas (Cosechas)',
                            'description' => 'Gestión de cosechas, ventas y reportes comerciales',
                            'icon' => '<i data-lucide="trending-up"></i>',
                            'route' => 'ventas.panel',
                            'color' => 'from-emerald-500 to-teal-600',
                            'available' => true,
                        ],
                        'compras' => [
                            'name' => 'Compras y Proveedores',
                            'description' => 'Gestión de órdenes de compra, proveedores y recepciones',
                            'icon' => '<i data-lucide="shopping-cart"></i>',
                            'route' => 'compras.panel',
                            'color' => 'from-cyan-500 to-blue-600',
                            'available' => true,
                        ],

                    ];
                    $userModules = $moduleDefinitions; // ahora todos disponibles
                @endphp

                @if (!empty($userModules))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($userModules as $module)
                            <div
                                class="group bg-gradient-to-br {{ $module['color'] }} rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.01] relative overflow-hidden"
                                onclick="window.location.href='{{ route($module['route']) }}'">

                                {{-- Efecto brillo --}}
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>

                                <div class="flex items-start justify-between mb-4 relative z-10">
                                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                        {!! $module['icon'] !!}
                                    </div>
                                    <div class="opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                        <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14" />
                                            <path d="M12 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="relative z-10">
                                    <h4 class="text-lg font-bold mb-1">{{ $module['name'] }}</h4>
                                    <p class="text-sm opacity-90">{{ $module['description'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Sin acceso a aplicaciones</h3>
                        <p class="text-gray-600 dark:text-gray-400">No tienes permisos para acceder a ninguna aplicación. Contacta al administrador para obtener acceso.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
