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
                    <p class="text-gray-700 dark:text-gray-300">Costo Compra Pez ({{ number_format($loteSeleccionado->cantidad_inicial) }} peces)</p>
                    <p class="text-lg font-semibold" style="color: #8b5cf6;">Q{{ number_format($costoCompraPez, 2) }}</p>
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
