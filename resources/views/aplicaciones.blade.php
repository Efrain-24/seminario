@extends('layouts.app')

@section('title', 'Aplicaciones del Sistema')

@section('content')
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
        <div class="overflow-hidden sm:rounded-lg">
            <div class="p-6">
                @php
                    $moduleDefinitions = [
                        'reportes' => [
                            'name' => 'Reportes',
                            'description' => 'Reportes de ganancias, costos y análisis financiero',
                            'icon' => '<i data-lucide="bar-chart-3"></i>',
                            'route' => 'reportes.ganancias',
                            'color' => '#1E293B', // gris oscuro
                        ],
                        'unidades' => [
                            'name' => 'Unidades',
                            'description' => 'Gestión de unidades de producción y mantenimiento',
                            'icon' => '<i data-lucide="building-2"></i>',
                            'route' => 'unidades.panel',
                            'color' => '#2563EB', // azul
                        ],
                        'produccion' => [
                            'name' => 'Producción',
                            'description' => 'Lotes, seguimientos, alimentación y tipos de alimentos',
                            'icon' => '<i data-lucide="factory"></i>',
                            'route' => 'produccion.panel',
                            'color' => '#16A34A', // verde
                        ],
                        'inventarios' => [
                            'name' => 'Inventarios',
                            'description' => 'Gestión de inventarios y traslados',
                            'icon' => '<i data-lucide="boxes"></i>',
                            'route' => 'inventarios.panel',
                            'color' => '#7E22CE', // morado
                        ],
                        'usuarios_roles' => [
                            'name' => 'Usuarios y Roles',
                            'description' => 'Gestión de usuarios, roles y permisos del sistema',
                            'icon' => '<i data-lucide="users"></i>',
                            'route' => 'usuarios.panel',
                            'color' => '#DB2777', // rosa fuerte
                        ],
                        'acciones_correctivas' => [
                            'name' => 'Acciones Correctivas',
                            'description' => 'Gestión de no conformidades y acciones correctivas',
                            'icon' => '<i data-lucide="check-circle"></i>',
                            'route' => 'acciones-correctivas.panel',
                            'color' => '#DC2626', // rojo
                        ],
                        'protocolos_limpieza' => [
                            'name' => 'Protocolos y Limpieza',
                            'description' => 'Protocolos de limpieza y procedimientos operativos',
                            'icon' => '<i data-lucide="clipboard-check"></i>',
                            'route' => 'protocolos.panel',
                            'color' => '#78350F', // café oscuro
                        ],
                        'ventas' => [
                            'name' => 'Ventas (Cosechas)',
                            'description' => 'Gestión de cosechas, ventas y reportes comerciales',
                            'icon' => '<i data-lucide="trending-up"></i>',
                            'route' => 'ventas.panel',
                            'color' => '#EA580C', // naranja
                        ],
                        'compras_proveedores' => [
                            'name' => 'Compras y Proveedores',
                            'description' => 'Gestión de órdenes de compra, proveedores y recepciones',
                            'icon' => '<i data-lucide="shopping-cart"></i>',
                            'route' => 'compras.panel',
                            'color' => '#EAB308', // amarillo fuerte
                        ],
                    ];

                    $allowed = auth()->user()->getAllowedModules();
                    $userModules = collect($moduleDefinitions)
                        ->filter(fn($v, $k) => in_array($k, $allowed));
                @endphp

                @if ($userModules->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($userModules as $module)
                            <a href="{{ route($module['route']) }}"
                               class="group rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.02]"
                               style="background-color: {{ $module['color'] }};">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                        {!! $module['icon'] !!}
                                    </div>
                                    <svg class="w-6 h-6 opacity-70 group-hover:opacity-100 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12h14" />
                                        <path d="M12 5l7 7-7 7" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold mb-1">{{ $module['name'] }}</h4>
                                <p class="text-sm opacity-90">{{ $module['description'] }}</p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Sin acceso a aplicaciones</h3>
                        <p class="text-gray-600 dark:text-gray-400">No tienes permisos para acceder a ninguna aplicación del sistema.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
