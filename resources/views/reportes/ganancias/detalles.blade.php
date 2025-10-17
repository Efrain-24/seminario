<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalles del Reporte de Ganancias') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Información del Lote -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Información del Lote</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Código</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $loteSeleccionado->codigo_lote }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Unidad de Producción</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $loteSeleccionado->unidadProduccion->nombre ?? 'Sin unidad' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Cantidad Inicial</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $loteSeleccionado->cantidad_inicial }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Precio Unitario</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">Q{{ number_format($loteSeleccionado->precio_unitario_pez ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consumo de Alimento -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Consumo de Alimento</h3>
                    @if(count($alimentacionDetalle) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300">Fecha</th>
                                        <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300">Producto</th>
                                        <th class="px-4 py-2 text-right text-gray-700 dark:text-gray-300">Cantidad (kg)</th>
                                        <th class="px-4 py-2 text-right text-gray-700 dark:text-gray-300">Precio Compra</th>
                                        <th class="px-4 py-2 text-right text-gray-700 dark:text-gray-300">Costo Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($alimentacionDetalle as $alimento)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $alimento['fecha'] }}</td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $alimento['producto'] }}</td>
                                            <td class="px-4 py-2 text-right text-gray-900 dark:text-gray-100">{{ number_format($alimento['cantidad_kg'], 2) }}</td>
                                            <td class="px-4 py-2 text-right text-gray-900 dark:text-gray-100">Q{{ number_format($alimento['precio_compra'], 2) }}</td>
                                            <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-gray-100">Q{{ number_format($alimento['costo_total'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-blue-50 dark:bg-blue-900/20 font-semibold">
                                    <tr>
                                        <td colspan="4" class="px-4 py-2 text-right text-gray-900 dark:text-gray-100">Total Alimento:</td>
                                        <td class="px-4 py-2 text-right text-blue-600 dark:text-blue-400">Q{{ number_format($costoTotalAlimento, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400">No hay registros de alimentación para este lote.</p>
                    @endif
                </div>
            </div>

            <!-- Costos de Protocolos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Costos de Protocolos</h3>
                    @if(count($protocoloDetalle) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300">Protocolo</th>
                                        <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300">Fecha</th>
                                        <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300">Descripción</th>
                                        <th class="px-4 py-2 text-right text-gray-700 dark:text-gray-300">Costo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($protocoloDetalle as $protocolo)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $protocolo['nombre'] }}</td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $protocolo['fecha'] }}</td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $protocolo['descripcion'] }}</td>
                                            <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-gray-100">Q{{ number_format($protocolo['costo'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-green-50 dark:bg-green-900/20 font-semibold">
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-gray-900 dark:text-gray-100">Total Protocolos:</td>
                                        <td class="px-4 py-2 text-right text-green-600 dark:text-green-400">Q{{ number_format($costoTotalProtocolos, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400">No hay protocolos registrados para este lote.</p>
                    @endif
                </div>
            </div>

            <!-- Insumos Utilizados -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Insumos Utilizados</h3>
                    @if(count($insumoDetalle) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300">Insumo</th>
                                        <th class="px-4 py-2 text-right text-gray-700 dark:text-gray-300">Cantidad</th>
                                        <th class="px-4 py-2 text-right text-gray-700 dark:text-gray-300">Precio Compra</th>
                                        <th class="px-4 py-2 text-right text-gray-700 dark:text-gray-300">Costo Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($insumoDetalle as $insumo)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $insumo['nombre'] }}</td>
                                            <td class="px-4 py-2 text-right text-gray-900 dark:text-gray-100">{{ number_format($insumo['cantidad'], 2) }}</td>
                                            <td class="px-4 py-2 text-right text-gray-900 dark:text-gray-100">Q{{ number_format($insumo['precio_compra'], 2) }}</td>
                                            <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-gray-100">Q{{ number_format($insumo['costo_total'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-purple-50 dark:bg-purple-900/20 font-semibold">
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-gray-900 dark:text-gray-100">Total Insumos:</td>
                                        <td class="px-4 py-2 text-right text-purple-600 dark:text-purple-400">Q{{ number_format($costoTotalInsumos, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400">No hay insumos registrados para este lote.</p>
                    @endif
                </div>
            </div>

            <!-- Precio de Compra del Pez -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Costo de Adquisición del Lote</h3>
                    <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg">
                        <p class="text-gray-700 dark:text-gray-300 mb-2">
                            <span class="font-medium">Cantidad:</span> {{ number_format($loteSeleccionado->cantidad_inicial, 0) }} peces
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 mb-2">
                            <span class="font-medium">Precio Unitario:</span> Q{{ number_format($loteSeleccionado->precio_unitario_pez ?? 0, 2) }}
                        </p>
                        <p class="text-lg font-semibold text-amber-600 dark:text-amber-400">
                            Costo Total de Compra: Q{{ number_format($precioCompraPez, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Resumen Total -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Resumen de Costos Totales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Costo de Alimento</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">Q{{ number_format($costoTotalAlimento, 2) }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Costo de Protocolos</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">Q{{ number_format($costoTotalProtocolos, 2) }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Costo de Insumos</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">Q{{ number_format($costoTotalInsumos, 2) }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Costo de Compra del Lote</p>
                            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">Q{{ number_format($precioCompraPez, 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-300 dark:border-gray-600">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">Costo Total del Lote:</p>
                            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">Q{{ number_format($totalCostos, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón volver -->
            <div class="flex justify-start">
                <a href="{{ route('reportes.ganancias') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver al Reporte
                </a>
            </div>
        </div>
    </div>
</x-app-layout>