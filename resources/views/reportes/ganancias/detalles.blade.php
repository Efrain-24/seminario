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
                                <p class="text-lg font-semibold" style="color: #0ea5e9;">Q{{ number_format($costoTotalProtocolos, 2) }}</p>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-700 dark:text-gray-300">Costo Alimentos</p>
                                <p class="text-lg font-semibold" style="color: #f97316;">Q{{ number_format($costoTotalAlimento, 2) }}</p>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-700 dark:text-gray-300">Costo Insumos</p>
                                <p class="text-lg font-semibold" style="color: #eab308;">Q{{ number_format($costoTotalInsumos, 2) }}</p>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-700 dark:text-gray-300">Mortalidad ({{ $cantidadMortalidad }} peces)</p>
                                <p class="text-lg font-semibold" style="color: #dc2626;">Q{{ number_format($costoMortalidad, 2) }}</p>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-700 dark:text-gray-300 font-semibold">Ingreso por Ventas</p>
                                <p class="text-lg font-semibold" style="color: #2563eb;">Q{{ number_format($totalIngresosVentas, 2) }}</p>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-700 dark:text-gray-300 font-semibold">Ventas Potenciales</p>
                                <p class="text-lg font-semibold" style="color: #ec4899;">Q{{ number_format($ventasPotenciales, 2) }}</p>
                            </div>
                            <div class="flex justify-between items-center pt-3 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                                <p class="text-base font-semibold text-gray-900 dark:text-gray-100">Subtotal</p>
                                <p class="text-2xl font-bold" style="color: {{ $subtotal >= 0 ? '#2563eb' : '#dc2626' }};">Q{{ number_format($subtotal, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Espacio para Gráfica (Derecha) -->
                <div class="bg-white dark:bg-gray-800 border-2 border-white dark:border-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 h-full flex flex-col items-center justify-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Distribución de Costos e Ingresos</h3>
                        <div style="width: 100%; height: 300px;">
                            <canvas id="gananciasChart"></canvas>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('gananciasChart').getContext('2d');
            
            const chartData = {
                labels: [
                    'Costo Protocolo',
                    'Costo Alimentos',
                    'Costo Insumos',
                    'Mortalidad',
                    'Ingreso por Ventas',
                    'Ventas Potenciales'
                ],
                datasets: [{
                    data: [
                        {{ $costoTotalProtocolos }},
                        {{ $costoTotalAlimento }},
                        {{ $costoTotalInsumos }},
                        {{ $costoMortalidad }},
                        {{ $totalIngresosVentas }},
                        {{ $ventasPotenciales }}
                    ],
                    backgroundColor: [
                        '#0ea5e9',  // Celeste - Costo Protocolo
                        '#f97316',  // Naranja - Costo Alimentos
                        '#eab308',  // Amarillo - Costo Insumos
                        '#dc2626',  // Rojo - Mortalidad
                        '#2563eb',  // Azul - Ingreso por Ventas
                        '#ec4899'   // Fucsia - Ventas Potenciales
                    ],
                    borderColor: [
                        '#0ea5e9',
                        '#f97316',
                        '#eab308',
                        '#dc2626',
                        '#2563eb',
                        '#ec4899'
                    ],
                    borderWidth: 2
                }]
            };

            new Chart(ctx, {
                type: 'doughnut',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12
                                },
                                color: '#374151'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Q' + context.parsed.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>