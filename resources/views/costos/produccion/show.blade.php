@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('costos.produccion.index') }}" class="text-blue-600 hover:text-blue-800">
                                    📊 Costos de Producción
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <span class="text-gray-400">/</span>
                                    <span class="ml-1 text-gray-500 md:ml-2">{{ $lote->codigo_lote }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        📋 Detalle de Costos - {{ $lote->codigo_lote }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Análisis detallado del costo de producción para {{ $lote->especie }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Información del Lote -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    🐟 Información del Lote
                </h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Código:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $lote->codigo_lote }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Especie:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $lote->especie }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Cantidad Inicial:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ number_format($lote->cantidad_inicial) }} peces</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Fecha Inicio:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $lote->fecha_inicio->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    💰 Resumen de Costos
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Insumos:</span>
                        <span class="font-medium text-gray-900 dark:text-white">Q{{ number_format($detalleCostos['costos']['insumos'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Alimentación:</span>
                        <span class="font-medium text-gray-900 dark:text-white">Q{{ number_format($detalleCostos['costos']['alimentacion'], 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <span class="font-medium text-gray-900 dark:text-white">Total:</span>
                        <span class="font-bold text-gray-900 dark:text-white">Q{{ number_format($detalleCostos['costos']['total'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    ⚖️ Producción y Rentabilidad
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Producción:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ number_format($detalleCostos['produccion']['total_libras'], 1) }} lb</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Biomasa Actual:</span>
                        <span class="font-medium text-green-600">{{ number_format($detalleCostos['produccion']['biomasa_actual'], 1) }} lb</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <span class="font-medium text-gray-900 dark:text-white">Costo/Libra:</span>
                        <span class="font-bold text-blue-600 text-lg">Q{{ number_format($detalleCostos['indicadores']['costo_por_libra'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicadores Clave -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-green-400 to-green-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center">
                    <span class="text-3xl">💵</span>
                    <div class="ml-4">
                        <p class="text-green-100">Ganancia Realizada</p>
                        <p class="text-2xl font-bold">Q{{ number_format($detalleCostos['indicadores']['ganancia_realizada'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center">
                    <span class="text-3xl">🎯</span>
                    <div class="ml-4">
                        <p class="text-blue-100">Venta Potencial</p>
                        <p class="text-2xl font-bold">Q{{ number_format($detalleCostos['indicadores']['venta_potencial'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center">
                    <span class="text-3xl">📈</span>
                    <div class="ml-4">
                        <p class="text-yellow-100">Margen Estimado</p>
                        <p class="text-2xl font-bold">{{ number_format($detalleCostos['indicadores']['margen_estimado'], 1) }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-400 to-purple-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center">
                    <span class="text-3xl">💲</span>
                    <div class="ml-4">
                        <p class="text-purple-100">Costo por Libra</p>
                        <p class="text-2xl font-bold">Q{{ number_format($detalleCostos['indicadores']['costo_por_libra'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalle de Alimentación -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    🍃 Detalle de Alimentación
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Costos basados en registros existentes de alimentación
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Alimento
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Cantidad (lb)
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Costo Unitario
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Costo Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($alimentaciones as $alimentacion)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $alimentacion->fecha_alimentacion->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $alimentacion->inventarioItem->nombre ?? 'No especificado' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ number_format($alimentacion->cantidad_kg, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if($alimentacion->inventarioItem && $alimentacion->inventarioItem->costo_unitario > 0)
                                    Q{{ number_format($alimentacion->inventarioItem->costo_unitario, 2) }}/{{ $alimentacion->inventarioItem->unidad_base }}
                                @else
                                    <span class="text-gray-400">Sin costo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                @if($alimentacion->inventarioItem && $alimentacion->inventarioItem->costo_unitario > 0)
                                    Q{{ number_format($alimentacion->cantidad_kg * $alimentacion->inventarioItem->costo_unitario, 2) }}
                                @else
                                    <span class="text-gray-400">Q0.00</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                No hay registros de alimentación
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detalle de Insumos -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    🧰 Detalle de Insumos Utilizados
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Costos de insumos utilizados en mantenimientos y protocolos de sanidad
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Insumo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Cantidad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Costo Unitario
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Costo Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($mantenimientos as $mantenimiento)
                            @foreach($mantenimiento->insumos as $insumo)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $mantenimiento->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        Mantenimiento
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $insumo->inventarioItem->nombre ?? 'No especificado' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ number_format($insumo->cantidad, 2) }} {{ $insumo->inventarioItem->unidad_base ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($insumo->inventarioItem && $insumo->inventarioItem->costo_unitario > 0)
                                        Q{{ number_format($insumo->inventarioItem->costo_unitario, 2) }}
                                    @else
                                        <span class="text-gray-400">Sin costo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    @if($insumo->inventarioItem && $insumo->inventarioItem->costo_unitario > 0)
                                        Q{{ number_format($insumo->cantidad * $insumo->inventarioItem->costo_unitario, 2) }}
                                    @else
                                        <span class="text-gray-400">Q0.00</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                No hay registros de insumos utilizados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection