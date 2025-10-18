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

            <!-- Resumen Total con Gráfica -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Desglose Detallado de Costos (Izquierda) -->
                <div class="bg-white dark:bg-gray-800 border-2 border-white dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Desglose Detallado</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-700 dark:text-gray-300">Costo Protocolo</p>
                                <p class="text-lg font-semibold text-green-600 dark:text-green-400">Q{{ number_format($costoTotalProtocolos, 2) }}</p>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-700 dark:text-gray-300">Costo Alimentos</p>
                                <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">Q{{ number_format($costoTotalAlimento, 2) }}</p>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-700 dark:text-gray-300">Costo Insumos</p>
                                <p class="text-lg font-semibold text-purple-600 dark:text-purple-400">Q{{ number_format($costoTotalInsumos, 2) }}</p>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-700 dark:text-gray-300">Mortalidad ({{ $cantidadMortalidad }} peces)</p>
                                <p class="text-lg font-semibold text-red-600 dark:text-red-400">Q{{ number_format($costoMortalidad, 2) }}</p>
                            </div>
                            <div class="flex justify-between items-center pt-3 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                                <p class="text-base font-semibold text-gray-900 dark:text-gray-100">Subtotal</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">Q{{ number_format($costoTotalProtocolos + $costoTotalAlimento + $costoTotalInsumos + $costoMortalidad, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Espacio para Gráfica (Derecha) -->
                <div class="bg-white dark:bg-gray-800 border-2 border-white dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 h-full flex items-center justify-center">
                        <p class="text-gray-400 dark:text-gray-500">Gráfica</p>
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