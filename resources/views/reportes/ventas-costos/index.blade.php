@extends('layouts.app')

@section('title', 'Reporte de Ventas y Costos - Piscicultura')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado del Reporte -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    📊 Reporte de Ventas y Costos
                </h1>
                <p class="text-gray-600 dark:text-gray-300">
                    Análisis consolidado del módulo de piscicultura - {{ now()->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                    🖨️ Imprimir Reporte
                </button>
            </div>
        </div>
    </div>

    <!-- Resumen Ejecutivo -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Ventas Ejecutadas -->
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 border border-green-200 dark:border-green-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-600 dark:text-green-400 text-sm font-medium">Ventas Ejecutadas</p>
                    <p class="text-2xl font-bold text-green-800 dark:text-green-200">
                        Q{{ number_format($reporteData['ventas_ejecutadas_Q'], 2) }}
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-800 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ventas Potenciales -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-600 dark:text-blue-400 text-sm font-medium">Ventas Potenciales</p>
                    <p class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                        Q{{ number_format($reporteData['ventas_potenciales_Q'], 2) }}
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-800 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Margen Ejecutado -->
        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-6 border border-purple-200 dark:border-purple-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-600 dark:text-purple-400 text-sm font-medium">Margen Ejecutado</p>
                    <p class="text-2xl font-bold text-purple-800 dark:text-purple-200">
                        {{ number_format($reporteData['margen_ejecutado_%'], 1) }}%
                    </p>
                    <p class="text-sm text-purple-600 dark:text-purple-400">
                        Q{{ number_format($reporteData['margen_ejecutado_Q'], 2) }}
                    </p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-800 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Margen Potencial -->
        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-6 border border-orange-200 dark:border-orange-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-600 dark:text-orange-400 text-sm font-medium">Margen Potencial</p>
                    <p class="text-2xl font-bold text-orange-800 dark:text-orange-200">
                        {{ number_format($reporteData['margen_potencial_%'], 1) }}%
                    </p>
                    <p class="text-sm text-orange-600 dark:text-orange-400">
                        Q{{ number_format($reporteData['margen_potencial_Q'], 2) }}
                    </p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-800 p-3 rounded-full">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Principal del Reporte -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                📋 Resumen Consolidado
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Concepto
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Monto (Q)
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Porcentaje
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Ventas Ejecutadas -->
                    <tr class="bg-green-50 dark:bg-green-900/20">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            💰 Ventas Ejecutadas
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-600 dark:text-green-400">
                            Q{{ number_format($reporteData['ventas_ejecutadas_Q'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                            -
                        </td>
                    </tr>
                    
                    <!-- Ventas Potenciales -->
                    <tr class="bg-blue-50 dark:bg-blue-900/20">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            📈 Ventas Potenciales
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-blue-600 dark:text-blue-400">
                            Q{{ number_format($reporteData['ventas_potenciales_Q'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                            -
                        </td>
                    </tr>
                    
                    <!-- Separador de Costos -->
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <td colspan="3" class="px-6 py-2 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">
                            Costos por Rubro
                        </td>
                    </tr>
                    
                    <!-- Costos: Semilla -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            🌱 Costos: Semilla
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                            Q{{ number_format($reporteData['costos']['semilla_Q'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                            {{ $reporteData['ventas_ejecutadas_Q'] > 0 ? number_format(($reporteData['costos']['semilla_Q'] / $reporteData['ventas_ejecutadas_Q']) * 100, 1) : '0.0' }}%
                        </td>
                    </tr>
                    
                    <!-- Costos: Alimento -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            🐟 Costos: Alimento
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                            Q{{ number_format($reporteData['costos']['alimento_Q'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                            {{ $reporteData['ventas_ejecutadas_Q'] > 0 ? number_format(($reporteData['costos']['alimento_Q'] / $reporteData['ventas_ejecutadas_Q']) * 100, 1) : '0.0' }}%
                        </td>
                    </tr>
                    
                    <!-- Costos: Mortalidad -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            ⚠️ Costos: Mortalidad
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                            Q{{ number_format($reporteData['costos']['mortalidad_Q'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                            {{ $reporteData['ventas_ejecutadas_Q'] > 0 ? number_format(($reporteData['costos']['mortalidad_Q'] / $reporteData['ventas_ejecutadas_Q']) * 100, 1) : '0.0' }}%
                        </td>
                    </tr>
                    
                    <!-- Costos: Otros -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            🔧 Costos: Otros
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                            Q{{ number_format($reporteData['costos']['otros_Q'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                            {{ $reporteData['ventas_ejecutadas_Q'] > 0 ? number_format(($reporteData['costos']['otros_Q'] / $reporteData['ventas_ejecutadas_Q']) * 100, 1) : '0.0' }}%
                        </td>
                    </tr>
                    
                    <!-- Total Costos -->
                    <tr class="bg-red-50 dark:bg-red-900/20 border-t-2 border-red-200 dark:border-red-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                            📊 Total Costos
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-red-700 dark:text-red-400">
                            Q{{ number_format($reporteData['costos']['semilla_Q'] + $reporteData['costos']['alimento_Q'] + $reporteData['costos']['mortalidad_Q'] + $reporteData['costos']['otros_Q'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-700 dark:text-gray-300">
                            {{ $reporteData['ventas_ejecutadas_Q'] > 0 ? number_format((($reporteData['costos']['semilla_Q'] + $reporteData['costos']['alimento_Q'] + $reporteData['costos']['mortalidad_Q'] + $reporteData['costos']['otros_Q']) / $reporteData['ventas_ejecutadas_Q']) * 100, 1) : '0.0' }}%
                        </td>
                    </tr>
                    
                    <!-- Separador de Márgenes -->
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <td colspan="3" class="px-6 py-2 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">
                            Márgenes de Ganancia
                        </td>
                    </tr>
                    
                    <!-- Margen Ejecutado -->
                    <tr class="bg-purple-50 dark:bg-purple-900/20">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                            💎 Margen Ejecutado
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-purple-600 dark:text-purple-400">
                            Q{{ number_format($reporteData['margen_ejecutado_Q'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-purple-600 dark:text-purple-400">
                            {{ number_format($reporteData['margen_ejecutado_%'], 1) }}%
                        </td>
                    </tr>
                    
                    <!-- Margen Potencial -->
                    <tr class="bg-orange-50 dark:bg-orange-900/20">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                            🚀 Margen Potencial
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-orange-600 dark:text-orange-400">
                            Q{{ number_format($reporteData['margen_potencial_Q'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-orange-600 dark:text-orange-400">
                            {{ number_format($reporteData['margen_potencial_%'], 1) }}%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Detalles de Análisis -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                📈 Información del Análisis
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-300">Lotes analizados:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $reporteData['detalles']['lotes_analizados'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-300">Ventas directas:</span>
                    <span class="font-medium text-gray-900 dark:text-white">Q{{ number_format($reporteData['detalles']['ventas_ejecutadas_detalles']['total_ventas_directas'] ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-300">Cosechas vendidas:</span>
                    <span class="font-medium text-gray-900 dark:text-white">Q{{ number_format($reporteData['detalles']['ventas_ejecutadas_detalles']['total_cosechas_vendidas'] ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-300">Lotes con inventario:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ count($reporteData['detalles']['inventario_disponible'] ?? []) }}</span>
                </div>
            </div>
        </div>

        <!-- Notas Metodológicas -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                📋 Notas Metodológicas
            </h3>
            <div class="space-y-2 text-sm text-blue-700 dark:text-blue-300">
                <p><strong>Ventas Ejecutadas:</strong> Incluye ventas directas registradas y cosechas parciales vendidas.</p>
                <p><strong>Ventas Potenciales:</strong> Inventario actual × precio estimado por libra.</p>
                <p><strong>Costos Semilla:</strong> Estimado basado en cantidad inicial y costo promedio por alevín.</p>
                <p><strong>Costos Alimento:</strong> Suma de todos los registros de alimentación con costo.</p>
                <p><strong>Costos Mortalidad:</strong> Pérdida estimada por peces muertos × costo promedio.</p>
                <p><strong>Costos Otros:</strong> Insumos de mantenimiento y protocolos de sanidad.</p>
            </div>
        </div>
    </div>

    <!-- Botón de Regreso -->
    <div class="text-center">
        <a href="{{ route('dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg inline-flex items-center">
            ← Volver al Dashboard
        </a>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            background: white !important;
        }
        
        .bg-gray-800, .dark\:bg-gray-800 {
            background: white !important;
            color: black !important;
        }
        
        .text-white, .dark\:text-white {
            color: black !important;
        }
    }
</style>
@endpush
@endsection