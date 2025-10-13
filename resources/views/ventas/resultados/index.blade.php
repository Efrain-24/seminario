@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        📈 Análisis de Ventas: Ejecutadas vs Potenciales
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Consolidar costos, descuento por Q y % usando inventario disponible por precio estimado
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('ventas.resultados.consolidado') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        📊 Vista Consolidada
                    </a>
                    <a href="{{ route('ventas.resultados.exportar') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        📊 Exportar CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('ventas.resultados.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
        @if(isset($resultados['resumen_global']) && !empty($resultados['resumen_global']))
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <span class="text-2xl">💰</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ventas Ejecutadas</p>
                        <p class="text-2xl font-bold text-green-600">
                            Q{{ number_format($resultados['resumen_global']['ventas_ejecutadas_total'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <span class="text-2xl">🎯</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ventas Potenciales</p>
                        <p class="text-2xl font-bold text-blue-600">
                            Q{{ number_format($resultados['resumen_global']['ventas_potenciales_total'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <span class="text-2xl">📈</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Oportunidad</p>
                        <p class="text-2xl font-bold text-yellow-600">
                            Q{{ number_format($resultados['resumen_global']['oportunidad_total'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <span class="text-2xl">⚡</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Eficiencia Ventas</p>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ number_format($resultados['resumen_global']['eficiencia_ventas'], 1) }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Análisis por Lote -->
        <div class="space-y-8">
            @forelse($resultados['lotes'] as $loteData)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <!-- Header del Lote -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                🐟 {{ $loteData['lote']['codigo'] }} - {{ $loteData['lote']['especie'] }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Estado: {{ ucfirst($loteData['lote']['estado']) }} | 
                                Biomasa: {{ number_format($loteData['lote']['biomasa_actual_lb'], 1) }} lb
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Iniciado</div>
                            <div class="font-medium text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($loteData['lote']['fecha_inicio'])->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Ventas Ejecutadas -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                                <span class="text-green-600 mr-2">✅</span>
                                Ventas Ejecutadas
                            </h4>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                        <div class="text-sm text-green-600 dark:text-green-400">Peso Vendido</div>
                                        <div class="text-2xl font-bold text-green-800 dark:text-green-200">
                                            {{ number_format($loteData['ventas_ejecutadas']['peso_total_lb'], 1) }} lb
                                        </div>
                                    </div>
                                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                        <div class="text-sm text-green-600 dark:text-green-400">Precio Promedio</div>
                                        <div class="text-2xl font-bold text-green-800 dark:text-green-200">
                                            Q{{ number_format($loteData['ventas_ejecutadas']['precio_promedio_lb'], 2) }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-green-100 dark:bg-green-900/30 p-4 rounded-lg">
                                    <div class="text-sm text-green-600 dark:text-green-400">Ingreso Total Realizado</div>
                                    <div class="text-3xl font-bold text-green-800 dark:text-green-200">
                                        Q{{ number_format($loteData['ventas_ejecutadas']['ingreso_total'], 2) }}
                                    </div>
                                    <div class="text-sm text-green-600 dark:text-green-400 mt-1">
                                        {{ $loteData['ventas_ejecutadas']['numero_ventas'] }} venta(s) realizadas
                                    </div>
                                </div>
                                
                                <!-- Detalle de Ventas -->
                                @if(!empty($loteData['ventas_ejecutadas']['detalle_ventas']))
                                <div class="mt-4">
                                    <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Detalle de Ventas:</h5>
                                    <div class="space-y-2">
                                        @foreach($loteData['ventas_ejecutadas']['detalle_ventas'] as $venta)
                                        <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                            <div class="text-sm">
                                                {{ \Carbon\Carbon::parse($venta['fecha'])->format('d/m/Y') }} - 
                                                {{ number_format($venta['peso_lb'], 1) }} lb
                                            </div>
                                            <div class="text-sm font-medium">
                                                Q{{ number_format($venta['ingreso'], 2) }}
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Ventas Potenciales -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                                <span class="text-blue-600 mr-2">🎯</span>
                                Ventas Potenciales (Inventario Disponible)
                            </h4>
                            
                            <div class="space-y-4">
                                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                    <div class="text-sm text-blue-600 dark:text-blue-400">Biomasa Disponible</div>
                                    <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                                        {{ number_format($loteData['ventas_potenciales']['biomasa_disponible_lb'], 1) }} lb
                                    </div>
                                    <div class="text-sm text-blue-600 dark:text-blue-400">
                                        ≈ {{ number_format($loteData['ventas_potenciales']['cantidad_estimada']) }} peces
                                    </div>
                                </div>
                                
                                <!-- Escenarios de Precio -->
                                <div class="space-y-3">
                                    @foreach($loteData['ventas_potenciales']['escenarios'] as $escenario => $datos)
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-medium text-gray-700 dark:text-gray-300 capitalize">
                                                {{ $escenario }}
                                            </span>
                                            <span class="text-lg font-bold text-blue-600">
                                                Q{{ number_format($datos['precio_por_lb'], 2) }}/lb
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                            {{ $datos['descripcion'] }}
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xl font-bold text-blue-800 dark:text-blue-200">
                                                Q{{ number_format($datos['ingreso_estimado'], 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comparación y Márgenes -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Comparación -->
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                                <h5 class="font-medium text-yellow-800 dark:text-yellow-200 mb-3">📊 Comparación</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-yellow-700 dark:text-yellow-300">% Vendido:</span>
                                        <span class="font-medium">{{ number_format($loteData['comparacion']['porcentaje_vendido'], 1) }}%</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-yellow-700 dark:text-yellow-300">% Disponible:</span>
                                        <span class="font-medium">{{ number_format($loteData['comparacion']['porcentaje_pendiente'], 1) }}%</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2">
                                        <span class="text-sm text-yellow-700 dark:text-yellow-300">Oportunidad:</span>
                                        <span class="font-bold">Q{{ number_format($loteData['comparacion']['oportunidad_mejora']['valor_oportunidad'], 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Márgenes -->
                            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                                <h5 class="font-medium text-purple-800 dark:text-purple-200 mb-3">💹 Márgenes</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-purple-700 dark:text-purple-300">Ejecutado:</span>
                                        <span class="font-medium">{{ number_format($loteData['margenes']['margen_ventas_ejecutadas'], 1) }}%</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-purple-700 dark:text-purple-300">Potencial:</span>
                                        <span class="font-medium">{{ number_format($loteData['margenes']['margen_ventas_potenciales'], 1) }}%</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2">
                                        <span class="text-sm text-purple-700 dark:text-purple-300">Costo/lb:</span>
                                        <span class="font-bold">Q{{ number_format($loteData['margenes']['costo_por_libra'], 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Recomendaciones -->
                            <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg">
                                <h5 class="font-medium text-indigo-800 dark:text-indigo-200 mb-3">💡 Recomendaciones</h5>
                                <div class="space-y-2">
                                    @if(isset($loteData['ventas_potenciales']['recomendacion']['recomendaciones']))
                                        @foreach($loteData['ventas_potenciales']['recomendacion']['recomendaciones'] as $recomendacion)
                                        <div class="text-sm text-indigo-700 dark:text-indigo-300">
                                            • {{ $recomendacion }}
                                        </div>
                                        @endforeach
                                    @endif
                                    <div class="text-sm font-medium text-indigo-800 dark:text-indigo-200 border-t pt-2">
                                        Mejor escenario: <span class="capitalize">{{ $loteData['ventas_potenciales']['recomendacion']['mejor_escenario'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 text-center">
                <div class="text-gray-400 text-6xl mb-4">📊</div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    No hay datos disponibles
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    No se encontraron lotes que coincidan con los filtros aplicados.
                </p>
            </div>
            @endforelse
        </div>

        <!-- Información adicional -->
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
            <h4 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-3">
                📋 Información sobre el Análisis
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800 dark:text-blue-200">
                <div>
                    <strong>Ventas Ejecutadas:</strong>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Basadas en cosechas parciales registradas como "venta"</li>
                        <li>Precios reales de transacciones realizadas</li>
                        <li>Ingresos confirmados y facturados</li>
                    </ul>
                </div>
                <div>
                    <strong>Ventas Potenciales:</strong>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Calculadas sobre inventario disponible actual</li>
                        <li>Tres escenarios de precio por especie</li>
                        <li>Estimaciones basadas en mercado local</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection