@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        📊 Análisis de Costos de Producción
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Cálculo detallado del costo por libra producida utilizando registros de insumos y producción existentes
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('costos.produccion.exportar') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        📊 Exportar CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('costos.produccion.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Especie</label>
                    <input type="text" name="especie" value="{{ request('especie') }}" 
                           placeholder="Ej: Tilapia" 
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado</label>
                    <select name="estado" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="cosechado" {{ request('estado') == 'cosechado' ? 'selected' : '' }}>Cosechado</option>
                        <option value="vendido" {{ request('estado') == 'vendido' ? 'selected' : '' }}>Vendido</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" 
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        🔍 Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Resumen Global -->
        @if(isset($reporteCostos['resumen']) && !empty($reporteCostos['resumen']))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <span class="text-2xl">📈</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Lotes</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $reporteCostos['resumen']['total_lotes'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <span class="text-2xl">💰</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Costo Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            Q{{ number_format($reporteCostos['resumen']['costo_total_operacion'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <span class="text-2xl">⚖️</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Producción Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($reporteCostos['resumen']['produccion_total_libras'], 1) }} lb
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <span class="text-2xl">🎯</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Costo/Libra Promedio</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            Q{{ number_format($reporteCostos['resumen']['costo_promedio_por_libra'], 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tabla de Lotes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Análisis Detallado por Lote
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Costo por libra calculado con base en registros de insumos y producción
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Lote
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Costos (Q)
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Producción
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Indicadores
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Rentabilidad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($reporteCostos['lotes'] as $loteData)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $loteData['codigo_lote'] }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $loteData['especie'] }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <div>Insumos: <span class="font-medium">Q{{ number_format($loteData['costos']['insumos'], 2) }}</span></div>
                                    <div>Alimentación: <span class="font-medium">Q{{ number_format($loteData['costos']['alimentacion'], 2) }}</span></div>
                                    <div class="font-bold border-t pt-1">Total: Q{{ number_format($loteData['costos']['total'], 2) }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <div>Total: <span class="font-medium">{{ number_format($loteData['produccion']['total_libras'], 1) }} lb</span></div>
                                    <div>Biomasa actual: <span class="text-green-600">{{ number_format($loteData['produccion']['biomasa_actual'], 1) }} lb</span></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                    Q{{ number_format($loteData['indicadores']['costo_por_libra'], 2) }}/lb
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Margen: {{ number_format($loteData['indicadores']['margen_estimado'], 1) }}%
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="text-green-600 font-medium">
                                        Ganancia: Q{{ number_format($loteData['indicadores']['ganancia_realizada'], 2) }}
                                    </div>
                                    <div class="text-gray-600 dark:text-gray-400">
                                        Potencial: Q{{ number_format($loteData['indicadores']['venta_potencial'], 2) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('costos.produccion.show', $loteData['lote_id']) }}" 
                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    Ver Detalle
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                No hay lotes disponibles para analizar
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
            <h4 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-3">
                📋 Información sobre el Cálculo
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800 dark:text-blue-200">
                <div>
                    <strong>Costos incluidos:</strong>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Insumos utilizados en mantenimientos</li>
                        <li>Insumos de protocolos de sanidad</li>
                        <li>Alimentos suministrados (registros existentes)</li>
                    </ul>
                </div>
                <div>
                    <strong>Producción considerada:</strong>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Biomasa actual del lote</li>
                        <li>Cosechas parciales realizadas</li>
                        <li>Conversión automática kg a libras (1 kg = 2.20462 lb)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection