@extends('layouts.app')

@section('title', 'Panel de Mantenimientos')

@section('content')
<!-- Header manual -->
<header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    🔧 Sistema de Mantenimientos
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Gestión completa de mantenimientos preventivos y correctivos
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('aplicaciones') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm">
                    ← Volver a Aplicaciones
                </a>
                <a href="{{ route('mantenimientos.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    ➕ Nuevo Mantenimiento
                </a>
            </div>
        </div>
    </div>
</header>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Estadísticas Principales -->
        @if(isset($estadisticas))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i data-lucide="clipboard-list" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $estadisticas['total'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completados</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $estadisticas['completados'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <i data-lucide="alert-circle" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Vencidos</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $estadisticas['vencidos'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Próximos 7 días</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $estadisticas['proximos_7_dias'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Submódulos de Mantenimientos -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                    🛠️ Módulos de Mantenimientos
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Gestión General -->
                    <div class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.01] relative overflow-hidden"
                         onclick="window.location.href='{{ route('mantenimientos.index') }}'">
                        
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>
                        
                        <div class="flex items-start justify-between mb-4 relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                <i data-lucide="list" class="w-7 h-7"></i>
                            </div>
                            <div class="opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="relative z-10">
                            <h4 class="text-lg font-bold mb-1">📋 Gestión General</h4>
                            <p class="text-sm opacity-90">Lista completa de mantenimientos con filtros</p>
                        </div>
                    </div>

                    <!-- Crear Mantenimiento -->
                    <div class="group bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.01] relative overflow-hidden"
                         onclick="window.location.href='{{ route('mantenimientos.create') }}'">
                        
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>
                        
                        <div class="flex items-start justify-between mb-4 relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                <i data-lucide="plus-circle" class="w-7 h-7"></i>
                            </div>
                            <div class="opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="relative z-10">
                            <h4 class="text-lg font-bold mb-1">➕ Programar</h4>
                            <p class="text-sm opacity-90">Crear nuevo mantenimiento con insumos</p>
                        </div>
                    </div>

                    <!-- Métricas y Reportes -->
                    <div class="group bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.01] relative overflow-hidden"
                         onclick="window.location.href='{{ route('mantenimientos.metricas') }}'">
                        
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>
                        
                        <div class="flex items-start justify-between mb-4 relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                <i data-lucide="bar-chart-3" class="w-7 h-7"></i>
                            </div>
                            <div class="opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="relative z-10">
                            <h4 class="text-lg font-bold mb-1">📊 Métricas</h4>
                            <p class="text-sm opacity-90">Dashboard de eficiencia y costos</p>
                        </div>
                    </div>

                    <!-- Exportar -->
                    <div class="group bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.01] relative overflow-hidden"
                         onclick="window.location.href='{{ route('mantenimientos.exportar') }}'">
                        
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>
                        
                        <div class="flex items-start justify-between mb-4 relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                <i data-lucide="download" class="w-7 h-7"></i>
                            </div>
                            <div class="opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="relative z-10">
                            <h4 class="text-lg font-bold mb-1">📄 Exportar</h4>
                            <p class="text-sm opacity-90">Descargar reportes y datos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas y Acciones Rápidas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Mantenimientos Vencidos -->
            @if(isset($mantenimientosVencidos) && count($mantenimientosVencidos) > 0)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-4">
                    ⚠️ Mantenimientos Vencidos
                </h4>
                <div class="space-y-3">
                    @foreach($mantenimientosVencidos->take(5) as $mantenimiento)
                    <div class="flex items-center justify-between bg-white dark:bg-gray-800 p-3 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $mantenimiento->unidadProduccion->codigo ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $mantenimiento->tipo_mantenimiento }} - 
                                {{ $mantenimiento->fecha_mantenimiento->format('d/m/Y') }}
                            </p>
                        </div>
                        <a href="{{ route('mantenimientos.show', $mantenimiento) }}" 
                           class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                            Ver
                        </a>
                    </div>
                    @endforeach
                </div>
                @if(count($mantenimientosVencidos) > 5)
                <div class="mt-4 text-center">
                    <a href="{{ route('mantenimientos.index', ['estado' => 'vencido']) }}" 
                       class="text-red-600 hover:text-red-700 text-sm font-medium">
                        Ver todos los vencidos ({{ count($mantenimientosVencidos) }})
                    </a>
                </div>
                @endif
            </div>
            @endif

            <!-- Próximos Mantenimientos -->
            @if(isset($mantenimientosProximos) && count($mantenimientosProximos) > 0)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-4">
                    ⏰ Próximos 7 Días
                </h4>
                <div class="space-y-3">
                    @foreach($mantenimientosProximos->take(5) as $mantenimiento)
                    <div class="flex items-center justify-between bg-white dark:bg-gray-800 p-3 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $mantenimiento->unidadProduccion->codigo ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $mantenimiento->tipo_mantenimiento }} - 
                                {{ $mantenimiento->fecha_mantenimiento->format('d/m/Y') }}
                            </p>
                        </div>
                        <a href="{{ route('mantenimientos.show', $mantenimiento) }}" 
                           class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700">
                            Ver
                        </a>
                    </div>
                    @endforeach
                </div>
                @if(count($mantenimientosProximos) > 5)
                <div class="mt-4 text-center">
                    <a href="{{ route('mantenimientos.index', ['proximos' => '7']) }}" 
                       class="text-yellow-600 hover:text-yellow-700 text-sm font-medium">
                        Ver todos los próximos ({{ count($mantenimientosProximos) }})
                    </a>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Información de Integración -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center">
                        <i data-lucide="info" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-2">
                        🔧 Sistema de Mantenimientos Completamente Funcional
                    </h4>
                    <div class="text-sm text-blue-800 dark:text-blue-200">
                        <p class="mb-2">
                            <strong>✅ Integración Completa:</strong> El sistema de mantenimientos ahora está 
                            completamente integrado con el Sprint 11 y utiliza insumos del inventario.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <strong>Funcionalidades Principales:</strong>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li>🛠️ Mantenimientos preventivos y correctivos</li>
                                    <li>📦 Gestión de insumos utilizados</li>
                                    <li>💰 Cálculo de costos por mantenimiento</li>
                                    <li>📊 Métricas y reportes de eficiencia</li>
                                </ul>
                            </div>
                            <div>
                                <strong>Integración Sprint 11:</strong>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li>🔗 Costos incluidos en RF22</li>
                                    <li>🎯 Consistencia validada en RF37</li>
                                    <li>🔍 Trazabilidad completa en RF38</li>
                                    <li>📈 Dashboard integrado en RF39</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para los iconos Lucide -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>
    lucide.createIcons();
</script>
@endsection