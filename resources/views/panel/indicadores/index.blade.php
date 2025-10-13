@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        📊 Panel de Indicadores - Sprint 11
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Implementación completa de los requerimientos RF22, RF36, RF37, RF38 y RF39
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('panel.indicadores.consolidado') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        📊 Vista Consolidada
                    </a>
                </div>
            </div>
        </div>

        <!-- Resumen General -->
        @if(isset($panelData['resumen_general']))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <span class="text-2xl">📈</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Lotes</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $panelData['resumen_general']['total_lotes'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <span class="text-2xl">✅</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lotes Consistentes</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $panelData['resumen_general']['lotes_consistentes'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <span class="text-2xl">⚠️</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Alertas Totales</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $panelData['resumen_general']['alertas_totales'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <span class="text-2xl">🎯</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Confianza Promedio</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($panelData['resumen_general']['nivel_confianza_promedio'] ?? 0, 1) }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Accesos Rápidos a Módulos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- RF22: Costos de Producción -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">💰</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        RF22: Costos de Producción
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Cálculo detallado del costo por libra producida utilizando registros de insumos y producción existentes
                    </p>
                    <a href="{{ route('costos.produccion.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Ver Análisis
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- RF36: Ventas y Resultados -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">📈</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        RF36: Ventas y Resultados
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Obtener ventas ejecutadas y ventas potenciales (inventario disponible por precio estimado)
                    </p>
                    <a href="{{ route('ventas.resultados.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Ver Resultados
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- RF37: Consistencia y Estimación -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">🎯</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        RF37: Consistencia
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Los valores deben coincidir con Producción/Inventario/Insumos. Mostrar la Ganancia (realizada)
                    </p>
                    <a href="{{ route('panel.indicadores.consolidado', ['mostrar_consistencia' => true]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        Ver Consistencia
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- RF38 & RF39: Panel Completo -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">🔍</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        RF38-39: Panel Completo
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Filtros y trazabilidad. Confirmación y alerta al ocultar módulo. Panel de indicadores HU-008
                    </p>
                    <a href="{{ route('panel.indicadores.consolidado') }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Panel Completo
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Indicadores Clave -->
        @if(isset($panelData['indicadores_clave']))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
                📊 Indicadores Clave del Sistema
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">
                        {{ number_format($panelData['indicadores_clave']['fcr_promedio'] ?? 0, 2) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">FCR Promedio</div>
                    <div class="text-xs text-gray-500">Factor Conversión Alimenticia</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-600 mb-2">
                        {{ number_format($panelData['indicadores_clave']['mortalidad_promedio'] ?? 0, 1) }}%
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Mortalidad Promedio</div>
                    <div class="text-xs text-gray-500">Porcentaje de pérdidas</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">
                        {{ number_format($panelData['indicadores_clave']['biomasa_total'] ?? 0, 1) }} kg
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Biomasa Total</div>
                    <div class="text-xs text-gray-500">Producción actual</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-2">
                        {{ number_format($panelData['indicadores_clave']['alimento_total'] ?? 0, 1) }} kg
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Alimento Total</div>
                    <div class="text-xs text-gray-500">Consumo acumulado</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Estado del Sistema -->
        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-6">
            <h4 class="text-lg font-medium text-indigo-900 dark:text-indigo-100 mb-3">
                🚀 Sprint 11 - Implementación Completada
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-indigo-800 dark:text-indigo-200">
                <div>
                    <strong>Requerimientos Implementados:</strong>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>✅ RF22: Cálculo detallado del costo por libra producida</li>
                        <li>✅ RF36: Obtener ventas ejecutadas y ventas potenciales</li>
                        <li>✅ RF37: Consistencia y estimación entre datos</li>
                        <li>✅ RF38: Filtros y trazabilidad parametrizables</li>
                        <li>✅ RF39: Confirmación al ocultar módulos</li>
                    </ul>
                </div>
                <div>
                    <strong>Componentes del Sistema:</strong>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>🔧 4 Servicios especializados</li>
                        <li>🎮 3 Controladores principales</li>
                        <li>📄 6+ Vistas responsivas</li>
                        <li>🛣️ Rutas organizadas y protegidas</li>
                        <li>📊 Panel de indicadores consolidado</li>
                    </ul>
                </div>
            </div>
            <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-lg">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    <strong>📋 Nota del desarrollador:</strong> Todas las funcionalidades del Sprint 11 han sido implementadas 
                    utilizando los registros de insumos y producción existentes en el sistema. Los cálculos de costos, 
                    ventas potenciales y análisis de consistencia se basan en datos reales del sistema de gestión piscícola.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection