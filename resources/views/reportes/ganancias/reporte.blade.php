<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reporte de Ganancias por Lote') }}
        </h2>
    </x-slot>

    <!-- Cargar Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                        <div class="flex-1 min-w-xs">
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
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors" onclick="filtrarReporte()">Filtrar Reporte</button>
                    </form>

                    <script>
                        function filtrarReporte() {
                            const loteId = document.getElementById('lote_id').value;
                            
                            if (!loteId) {
                                alert('Por favor selecciona un lote');
                                return false;
                            }
                            
                            // Realizar petición AJAX para obtener los detalles
                            let url = "{{ route('reportes.ganancias.detalles', ['lote' => '__LOTE__']) }}".replace('__LOTE__', loteId);
                            
                            fetch(url, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Error en la respuesta del servidor');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Datos recibidos:', data);
                                    
                                    // Inyectar el HTML
                                    const resultadosContainer = document.getElementById('resultadosReporte');
                                    resultadosContainer.innerHTML = data.html;
                                    
                                    // Esperar a que el DOM se actualice
                                    setTimeout(() => {
                                        // Obtener el canvas
                                        const canvas = document.getElementById('gananciasChart');
                                        
                                        if (!canvas) {
                                            console.error('Canvas no encontrado');
                                            return;
                                        }
                                        
                                        if (!window.Chart) {
                                            console.error('Chart.js no está cargado');
                                            return;
                                        }
                                        
                                        // Destruir gráfica anterior si existe
                                        Chart.helpers.each(Chart.instances, function(instance) {
                                            if (instance.canvas === canvas) {
                                                instance.destroy();
                                            }
                                        });
                                        
                                        // Crear nueva gráfica con los datos JSON
                                        const ctx = canvas.getContext('2d');
                                        const chartData = {
                                            labels: [
                                                'Costo Protocolo',
                                                'Costo Alimentos',
                                                'Costo Insumos',
                                                'Costo Compra Pez',
                                                'Mortalidad',
                                                'Ingreso por Ventas',
                                                'Ventas Potenciales'
                                            ],
                                            datasets: [{
                                                data: [
                                                    data.data.protocolo,
                                                    data.data.alimentos,
                                                    data.data.insumos,
                                                    data.data.pez,
                                                    data.data.mortalidad,
                                                    data.data.ventas,
                                                    data.data.potenciales
                                                ],
                                                backgroundColor: [
                                                    '#0ea5e9',  // Celeste - Costo Protocolo
                                                    '#f97316',  // Naranja - Costo Alimentos
                                                    '#eab308',  // Amarillo - Costo Insumos
                                                    '#8b5cf6',  // Púrpura - Costo Compra Pez
                                                    '#dc2626',  // Rojo - Mortalidad
                                                    '#2563eb',  // Azul - Ingreso por Ventas
                                                    '#ec4899'   // Fucsia - Ventas Potenciales
                                                ],
                                                borderColor: [
                                                    '#0ea5e9',
                                                    '#f97316',
                                                    '#eab308',
                                                    '#8b5cf6',
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
                                        
                                        console.log('Gráfica creada exitosamente con los datos:', data.data);
                                    }, 100);
                                    
                                    // Scroll suave hacia los resultados
                                    setTimeout(() => {
                                        resultadosContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                    }, 150);
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Error al cargar el reporte: ' + error.message);
                                });
                        }
                    </script>

                    <!-- Contenedor para resultados dinámicos -->
                    <div id="resultadosReporte" class="mt-8"></div>

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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <!-- Ingresos -->
                                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                    <h4 class="text-md font-medium text-green-800 dark:text-green-200 mb-2">💰 Ingresos</h4>
                                    <p class="text-green-700 dark:text-green-300">Total Ventas: <strong>Q{{ number_format($desglose['total_ventas'], 2) }}</strong></p>
                                </div>
                                
                                <!-- Costos -->
                                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                                    <h4 class="text-md font-medium text-red-800 dark:text-red-200 mb-2">💸 Costos</h4>
                                    <div class="text-red-700 dark:text-red-300 space-y-1">
                                        @if($desglose['precio_compra_lote'] > 0)
                                            <p>• Compra de lote: Q{{ number_format($desglose['precio_compra_lote'], 2) }}</p>
                                        @endif
                                        <p>• Alimentación: Q{{ number_format($desglose['total_alimentacion'], 2) }}</p>
                                        <p>• Mantenimientos: Q{{ number_format($desglose['total_mantenimientos'], 2) }}</p>
                                        <p>• Limpiezas: Q{{ number_format($desglose['total_limpiezas'], 2) }}</p>
                                        <p>• Protocolos de sanidad: Q{{ number_format($desglose['total_protocolos'], 2) }}</p>
                                        <p>• Insumos de mantenimientos: Q{{ number_format($desglose['costo_insumos_mantenimientos'], 2) }}</p>
                                        <hr class="border-red-300 my-2">
                                        <p class="font-semibold">Total Costos: Q{{ number_format($desglose['total_costos'], 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ganancia -->
                            <div class="mt-4 p-4 {{ $desglose['ganancia_real'] >= 0 ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20' }} rounded-lg">
                                <h4 class="text-md font-medium {{ $desglose['ganancia_real'] >= 0 ? 'text-blue-800 dark:text-blue-200' : 'text-yellow-800 dark:text-yellow-200' }} mb-2">
                                    {{ $desglose['ganancia_real'] >= 0 ? '📈 Ganancia' : '📉 Pérdida' }}
                                </h4>
                                <p class="{{ $desglose['ganancia_real'] >= 0 ? 'text-blue-700 dark:text-blue-300' : 'text-yellow-700 dark:text-yellow-300' }}">
                                    Resultado: <strong>Q{{ number_format($desglose['ganancia_real'], 2) }}</strong>
                                </p>
                                <p class="{{ $desglose['ganancia_real'] >= 0 ? 'text-blue-700 dark:text-blue-300' : 'text-yellow-700 dark:text-yellow-300' }}">
                                    Margen: <strong>{{ number_format($desglose['margen_ganancia'], 2) }}%</strong>
                                </p>
                            </div>

                            <!-- Detalle de Protocolos Ejecutados -->
                            @if(isset($protocoloDetalle) && $protocoloDetalle->isNotEmpty())
                                <div class="mt-6">
                                    <h4 class="text-md font-medium text-gray-800 dark:text-gray-200 mb-3">🧪 Protocolos de Sanidad Ejecutados</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Protocolo</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Responsable</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Insumos Utilizados</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Costo</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                                @foreach($protocoloDetalle as $protocolo)
                                                    <tr>
                                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $protocolo['fecha'] }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $protocolo['protocolo'] }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $protocolo['responsable'] }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $protocolo['insumos'] ?: 'Sin insumos' }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">Q{{ number_format($protocolo['costo'], 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <td colspan="4" class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 text-right">Total Protocolos:</td>
                                                    <td class="px-4 py-2 text-sm font-bold text-gray-900 dark:text-gray-100">Q{{ number_format($desglose['total_protocolos'], 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            @endif

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