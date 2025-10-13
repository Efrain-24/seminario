@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        📊 Reporte Consolidado Integrado
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Análisis completo combinando reportes tradicionales con funcionalidades del Sprint 11
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('reportes.panel') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        ← Volver a Reportes
                    </a>
                    <a href="{{ route('reportes.exportar_integrado', ['formato' => 'pdf'] + request()->query()) }}" 
                       class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        📄 Exportar PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">🔍 Filtros de Análisis</h3>
            <form method="GET" action="{{ route('reportes.consolidado') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lote</label>
                    <select name="lote_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Todos los lotes</option>
                        @foreach(\App\Models\Lote::orderBy('codigo_lote', 'desc')->take(20)->get() as $lote)
                            <option value="{{ $lote->id }}" {{ request('lote_id') == $lote->id ? 'selected' : '' }}>
                                {{ $lote->codigo_lote ?? 'Lote #' . $lote->id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unidad</label>
                    <select name="unidad_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Todas las unidades</option>
                        @foreach(\App\Models\UnidadProduccion::all() as $unidad)
                            <option value="{{ $unidad->id }}" {{ request('unidad_id') == $unidad->id ? 'selected' : '' }}>
                                {{ $unidad->codigo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>

        @if(isset($datos))
        <!-- Resumen Ejecutivo -->
        @if(isset($datos['resumen_ejecutivo']))
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/30 dark:to-blue-900/30 rounded-lg p-6 mb-8">
            <h3 class="text-xl font-semibold text-indigo-900 dark:text-indigo-100 mb-6">
                🎯 Resumen Ejecutivo Integrado
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600 mb-1">
                        {{ number_format($datos['resumen_ejecutivo']['total_produccion'] ?? 0, 1) }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">kg Producidos</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600 mb-1">
                        ${{ number_format($datos['resumen_ejecutivo']['total_ventas'] ?? 0, 0) }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Total Ventas</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600 mb-1">
                        ${{ number_format($datos['resumen_ejecutivo']['costo_promedio_libra'] ?? 0, 2) }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Costo/Libra</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600 mb-1">
                        {{ number_format($datos['resumen_ejecutivo']['margen_promedio'] ?? 0, 1) }}%
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Margen Prom.</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-indigo-600 mb-1">
                        {{ number_format($datos['resumen_ejecutivo']['nivel_consistencia'] ?? 0, 1) }}%
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Consistencia</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-red-600 mb-1">
                        {{ $datos['resumen_ejecutivo']['alertas_activas'] ?? 0 }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Alertas</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Análisis de Costos Sprint 11 -->
        @if(isset($datos['costos_detallados']) && count($datos['costos_detallados']) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                💰 Análisis de Costos Detallados (RF22)
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Lote
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Costo Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Costo/Libra
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Margen
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($datos['costos_detallados'] as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item['lote']->codigo_lote ?? 'Lote #' . $item['lote']->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${{ number_format($item['costos']['costo_total'] ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                ${{ number_format($item['costos']['costo_por_libra'] ?? 0, 3) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ number_format($item['costos']['margen_porcentaje'] ?? 0, 1) }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $margen = $item['costos']['margen_porcentaje'] ?? 0; @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $margen > 15 ? 'bg-green-100 text-green-800' : ($margen > 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $margen > 15 ? 'Excelente' : ($margen > 5 ? 'Aceptable' : 'Bajo') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Análisis de Ventas Sprint 11 -->
        @if(isset($datos['ventas_analisis']) && !empty($datos['ventas_analisis']))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                📈 Análisis de Ventas Ejecutadas vs Potenciales (RF36)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-3">Ventas Ejecutadas</h4>
                    <div class="space-y-2">
                        @if(isset($datos['ventas_analisis']['ventas_ejecutadas']))
                        <div class="flex justify-between">
                            <span class="text-sm text-blue-700 dark:text-blue-300">Total Vendido:</span>
                            <span class="font-medium text-blue-900 dark:text-blue-100">
                                ${{ number_format($datos['ventas_analisis']['ventas_ejecutadas']['total'] ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-blue-700 dark:text-blue-300">Cantidad:</span>
                            <span class="font-medium text-blue-900 dark:text-blue-100">
                                {{ number_format($datos['ventas_analisis']['ventas_ejecutadas']['cantidad'] ?? 0, 1) }} kg
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <h4 class="font-medium text-purple-900 dark:text-purple-100 mb-3">Ventas Potenciales</h4>
                    <div class="space-y-2">
                        @if(isset($datos['ventas_analisis']['ventas_potenciales']))
                        <div class="flex justify-between">
                            <span class="text-sm text-purple-700 dark:text-purple-300">Potencial:</span>
                            <span class="font-medium text-purple-900 dark:text-purple-100">
                                ${{ number_format($datos['ventas_analisis']['ventas_potenciales']['total'] ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-purple-700 dark:text-purple-300">Disponible:</span>
                            <span class="font-medium text-purple-900 dark:text-purple-100">
                                {{ number_format($datos['ventas_analisis']['ventas_potenciales']['cantidad'] ?? 0, 1) }} kg
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Análisis de Consistencia Sprint 11 -->
        @if(isset($datos['consistencia_datos']['lotes']) && count($datos['consistencia_datos']['lotes']) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                🎯 Análisis de Consistencia de Datos (RF37)
            </h3>
            <div class="space-y-4">
                @foreach($datos['consistencia_datos']['lotes'] as $item)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            {{ $item['lote']['codigo'] ?? 'Lote #' . $item['lote']['id'] }}
                        </h4>
                        <span class="px-3 py-1 text-sm font-medium rounded-full 
                            {{ ($item['nivel_confianza']['puntuacion'] ?? 0) > 80 ? 'bg-green-100 text-green-800' : 
                               (($item['nivel_confianza']['puntuacion'] ?? 0) > 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ number_format($item['nivel_confianza']['puntuacion'] ?? 0, 1) }}% Confianza
                        </span>
                    </div>
                    @if(isset($item['consistencia']['alertas']) && count($item['consistencia']['alertas']) > 0)
                    <div class="space-y-2">
                        @foreach($item['consistencia']['alertas'] as $alerta)
                        <div class="flex items-center text-sm">
                            <span class="w-2 h-2 bg-{{ ($alerta['gravedad'] ?? 'media') === 'alta' ? 'red' : (($alerta['gravedad'] ?? 'media') === 'media' ? 'yellow' : 'blue') }}-500 rounded-full mr-2"></span>
                            <span class="text-gray-700 dark:text-gray-300">{{ $alerta['mensaje'] ?? 'Alerta de consistencia' }}</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-green-600 dark:text-green-400">✅ Datos consistentes</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Enlaces de Navegación -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
            <h4 class="font-medium text-gray-900 dark:text-white mb-4">🔗 Accesos Directos</h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('reportes.ganancias') }}" 
                   class="flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">📊 Ganancias Tradicional</span>
                </a>
                <a href="{{ route('costos.produccion.index') }}" 
                   class="flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">💰 Costos Sprint 11</span>
                </a>
                <a href="{{ route('ventas.resultados.index') }}" 
                   class="flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">📈 Ventas Sprint 11</span>
                </a>
                <a href="{{ route('panel.indicadores.consolidado') }}" 
                   class="flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">🎯 Dashboard Completo</span>
                </a>
            </div>
        </div>

        @else
        <!-- Estado sin datos -->
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 text-center">
            <div class="text-yellow-600 dark:text-yellow-400 mb-4">
                <svg class="mx-auto w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                No hay datos para mostrar
            </h3>
            <p class="text-yellow-700 dark:text-yellow-300 mb-4">
                Ajusta los filtros o verifica que existan lotes con información suficiente.
            </p>
            <a href="{{ route('reportes.panel') }}" 
               class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                Volver al Panel de Reportes
            </a>
        </div>
        @endif
    </div>
</div>
@endsection