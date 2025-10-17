<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reporte de Ganancias por Lote') }}
        </h2>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Seleccione un Lote</h3>

                    <form id="filtroForm" class="mb-6 bg-gray-50 p-4 rounded-lg flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="lote_id" class="block text-sm font-medium text-gray-700 mb-2">Tanque/Lote</label>
                            <select name="lote_id" id="lote_id" class="form-control border border-gray-300 rounded px-3 py-2 w-full" required>
                                <option value="">Seleccionar lote</option>
                                @if(isset($lotes))
                                    @foreach($lotes as $lote)
                                        <option value="{{ $lote->id }}" {{ request('lote_id') == $lote->id ? 'selected' : '' }}>
                                            {{ $lote->codigo_lote }} - {{ $lote->unidadProduccion->nombre ?? 'Sin unidad' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control border border-gray-300 rounded px-3 py-2 w-full" value="{{ request('fecha_inicio') }}">
                        </div>
                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control border border-gray-300 rounded px-3 py-2 w-full" value="{{ request('fecha_fin') }}">
                        </div>
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors" onclick="filtrarReporte()">Filtrar Reporte</button>
                    </form>

                    <script>
                        function filtrarReporte() {
                            const loteId = document.getElementById('lote_id').value;
                            const fechaInicio = document.getElementById('fecha_inicio').value;
                            const fechaFin = document.getElementById('fecha_fin').value;
                            
                            if (!loteId) {
                                alert('Por favor selecciona un lote');
                                return false;
                            }
                            
                            // Redirigir a la ruta de detalles con el lote en la URL
                            let url = "{{ route('reportes.ganancias.detalles', ['lote' => '__LOTE__']) }}".replace('__LOTE__', loteId);
                            if (fechaInicio) url += '?fecha_inicio=' + fechaInicio;
                            if (fechaFin) url += (fechaInicio ? '&' : '?') + 'fecha_fin=' + fechaFin;
                            
                            window.location.href = url;
                        }
                    </script>

                    @if(isset($loteSeleccionado))
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Detalles del Lote</h3>
                        <p>Código: {{ $loteSeleccionado->codigo_lote }}</p>
                        <p>Especie: {{ $loteSeleccionado->especie }}</p>
                        <p>Unidad: {{ $loteSeleccionado->unidadProduccion->nombre ?? 'Sin unidad' }}</p>
                        <p>Cantidad: {{ $loteSeleccionado->cantidad_inicial }}</p>
                        <p>Estado: {{ $loteSeleccionado->estado }}</p>
                        <p>Fecha de Inicio: {{ $loteSeleccionado->fecha_inicio->format('d/m/Y') }}</p>

                        @if($grafica && $desglose)
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-6">Desglose Financiero</h3>
                            <p>Total Ventas: Q{{ number_format($desglose['total_ventas'], 2) }}</p>
                            <p>Total Costos: Q{{ number_format($desglose['total_costos'], 2) }}</p>
                            <p>Ganancia Real: Q{{ number_format($desglose['ganancia_real'], 2) }}</p>
                            <p>Margen de Ganancia: {{ number_format($desglose['margen_ganancia'], 2) }}%</p>

                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-6">Gráfica de Ventas</h3>
                            <canvas id="graficaVentas"></canvas>

                            <script>
                                const ctx = document.getElementById('graficaVentas').getContext('2d');
                                const grafica = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: @json($grafica['labels']),
                                        datasets: [{
                                            label: 'Ventas Totales',
                                            data: @json($grafica['data']),
                                            borderColor: 'rgba(75, 192, 192, 1)',
                                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                position: 'top',
                                            },
                                        },
                                    },
                                });
                            </script>
                        @else
                            <p class="text-gray-500">No hay datos disponibles para el rango de fechas seleccionado.</p>
                        @endif
                    @else
                        <p class="text-gray-500">Seleccione un lote para ver el reporte.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>