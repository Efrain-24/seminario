@extends('layouts.app')

@section('title', 'Reportes Integrados del Sistema')

@section('content')
<!-- Header manual -->
<header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    📊 Sistema de Reportes Integrados
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Reportes tradicionales + Funcionalidades avanzadas Sprint 11
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('aplicaciones') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm">
                    ← Volver a Aplicaciones
                </a>
            </div>
        </div>
    </div>
</header>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Submódulos de Reportes -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                    📋 Módulos de Reportes Disponibles
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Reportes Tradicionales -->
                    <div class="group bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.01] relative overflow-hidden"
                         onclick="window.location.href='{{ route('reportes.ganancias') }}'">
                        
                        <!-- Efecto brillo -->
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>
                        
                        <div class="flex items-start justify-between mb-4 relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                <i data-lucide="pie-chart" class="w-7 h-7"></i>
                            </div>
                            <div class="opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="relative z-10">
                            <h4 class="text-lg font-bold mb-1">📊 Ganancias Tradicional</h4>
                            <p class="text-sm opacity-90">Reportes clásicos de ganancias por lote y análisis básico</p>
                            <div class="mt-3 flex items-center text-xs opacity-80">
                                <span class="bg-white/20 px-2 py-1 rounded-full">Sistema Legacy</span>
                            </div>
                        </div>
                    </div>

                    <!-- RF22: Costos Detallados -->
                    <div class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.01] relative overflow-hidden"
                         onclick="window.location.href='{{ route('costos.produccion.index') }}'">
                        
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>
                        
                        <div class="flex items-start justify-between mb-4 relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                <i data-lucide="calculator" class="w-7 h-7"></i>
                            </div>
                            <div class="opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="relative z-10">
                            <h4 class="text-lg font-bold mb-1">💰 Costos Detallados</h4>
                            <p class="text-sm opacity-90">RF22 - Cálculo preciso del costo por libra producida</p>
                            <div class="mt-3 flex items-center text-xs opacity-80">
                                <span class="bg-white/20 px-2 py-1 rounded-full">Sprint 11</span>
                            </div>
                        </div>
                    </div>

                    <!-- RF36: Ventas y Resultados -->
                    <div class="group bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.01] relative overflow-hidden"
                         onclick="window.location.href='{{ route('ventas.resultados.index') }}'">
                        
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>
                        
                        <div class="flex items-start justify-between mb-4 relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                <i data-lucide="trending-up" class="w-7 h-7"></i>
                            </div>
                            <div class="opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="relative z-10">
                            <h4 class="text-lg font-bold mb-1">📈 Ventas vs Potencial</h4>
                            <p class="text-sm opacity-90">RF36 - Análisis de ventas ejecutadas vs inventario disponible</p>
                            <div class="mt-3 flex items-center text-xs opacity-80">
                                <span class="bg-white/20 px-2 py-1 rounded-full">Sprint 11</span>
                            </div>
                        </div>
                    </div>

                    <!-- Reporte Consolidado -->
                    <div class="group bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.01] relative overflow-hidden"
                         onclick="window.location.href='{{ route('reportes.consolidado') }}'">
                        
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>
                        
                        <div class="flex items-start justify-between mb-4 relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                <i data-lucide="layout-dashboard" class="w-7 h-7"></i>
                            </div>
                            <div class="opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="relative z-10">
                            <h4 class="text-lg font-bold mb-1">📊 Reporte Consolidado</h4>
                            <p class="text-sm opacity-90">Vista unificada con análisis completo Sprint 11</p>
                            <div class="mt-3 flex items-center text-xs opacity-80">
                                <span class="bg-white/20 px-2 py-1 rounded-full">Integrado</span>
                            </div>
                        </div>
                    </div>

                    <!-- RF37: Consistencia -->
                    <div class="group bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.01] relative overflow-hidden"
                         onclick="window.location.href='{{ route('panel.indicadores.consolidado', ['mostrar_consistencia' => true]) }}'">
                        
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>
                        
                        <div class="flex items-start justify-between mb-4 relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                <i data-lucide="shield-check" class="w-7 h-7"></i>
                            </div>
                            <div class="opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="relative z-10">
                            <h4 class="text-lg font-bold mb-1">🎯 Consistencia</h4>
                            <p class="text-sm opacity-90">RF37 - Validación entre Producción/Inventario/Insumos</p>
                            <div class="mt-3 flex items-center text-xs opacity-80">
                                <span class="bg-white/20 px-2 py-1 rounded-full">Sprint 11</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Ejecutivo -->
                    <div class="group bg-gradient-to-br from-slate-500 to-gray-700 rounded-2xl p-6 text-white cursor-pointer shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-[1.01] relative overflow-hidden"
                         onclick="window.location.href='{{ route('panel.indicadores.consolidado') }}'">
                        
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transform skew-x-12 -translate-x-full group-hover:translate-x-full transition-all duration-1000"></div>
                        
                        <div class="flex items-start justify-between mb-4 relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/30 transition-all duration-300">
                                <i data-lucide="monitor" class="w-7 h-7"></i>
                            </div>
                            <div class="opacity-70 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14" />
                                    <path d="M12 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="relative z-10">
                            <h4 class="text-lg font-bold mb-1">🚀 Dashboard Ejecutivo</h4>
                            <p class="text-sm opacity-90">RF38-39 - Panel completo con trazabilidad y control</p>
                            <div class="mt-3 flex items-center text-xs opacity-80">
                                <span class="bg-white/20 px-2 py-1 rounded-full">Sprint 11</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        @if(isset($estadisticas) && !empty($estadisticas))
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/30 dark:to-blue-900/30 rounded-lg p-6 mb-8">
            <h4 class="text-lg font-semibold text-indigo-900 dark:text-indigo-100 mb-6">
                📈 Estadísticas del Sistema
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-blue-600 mb-1">
                        {{ $estadisticas['total_lotes'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Lotes</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-green-600 mb-1">
                        {{ $estadisticas['total_unidades'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Unidades</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-purple-600 mb-1">
                        {{ $estadisticas['lotes_activos'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Lotes Activos</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-indigo-600 mb-1">6</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Módulos Sprint 11</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Accesos Rápidos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                🔗 Accesos Rápidos y Herramientas
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('reportes.comparativa') }}" 
                   class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <div>
                        <h5 class="font-medium text-gray-900 dark:text-white">🔄 Comparativa</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tradicional vs Sprint 11</p>
                    </div>
                    <i data-lucide="arrow-right" class="w-5 h-5 text-gray-400"></i>
                </a>
                
                <a href="{{ route('reportes.exportar_integrado', ['formato' => 'pdf']) }}" 
                   class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <div>
                        <h5 class="font-medium text-gray-900 dark:text-white">📄 Exportar PDF</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Reporte consolidado</p>
                    </div>
                    <i data-lucide="download" class="w-5 h-5 text-gray-400"></i>
                </a>
                
                <a href="{{ route('panel.indicadores.index') }}" 
                   class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <div>
                        <h5 class="font-medium text-gray-900 dark:text-white">📊 Resumen Sprint 11</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Vista general</p>
                    </div>
                    <i data-lucide="external-link" class="w-5 h-5 text-gray-400"></i>
                </a>
            </div>
        </div>

        <!-- Información del Sprint -->
        <div class="mt-8 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-lg p-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-800 rounded-full flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="text-lg font-medium text-emerald-900 dark:text-emerald-100 mb-2">
                        🚀 Sistema de Reportes Completamente Integrado
                    </h4>
                    <div class="text-sm text-emerald-800 dark:text-emerald-200">
                        <p class="mb-2">
                            <strong>✅ Integración Exitosa:</strong> El Sprint 11 ha sido completamente integrado 
                            con el sistema de reportes, proporcionando acceso unificado a todas las funcionalidades.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <strong>Funcionalidades Disponibles:</strong>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li>✅ RF22: Costos detallados por libra</li>
                                    <li>✅ RF36: Ventas ejecutadas vs potenciales</li>
                                    <li>✅ RF37: Consistencia de datos</li>
                                    <li>✅ RF38-39: Trazabilidad y control</li>
                                </ul>
                            </div>
                            <div>
                                <strong>Accesos Disponibles:</strong>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li>🔗 Reportes tradicionales (compatibilidad)</li>
                                    <li>🆕 Módulos avanzados Sprint 11</li>
                                    <li>📊 Dashboard ejecutivo consolidado</li>
                                    <li>📄 Exportación en múltiples formatos</li>
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