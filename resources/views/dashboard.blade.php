<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Dashboard Principal
        </h2>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tarjetas de Estadísticas Generales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Lotes Activos -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Lotes Activos
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $estadisticasGenerales['lotes_activos'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Unidades Activas -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h4a1 1 0 011 1v5m-6 0h6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Unidades Activas
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $estadisticasGenerales['unidades_activas'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Biomasa Total -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l-3-3m3 3l3-3"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Biomasa Total
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ number_format($estadisticasGenerales['biomasa_total'], 1) }} kg
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Alertas Activas -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-red-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Alertas Activas
                                </dt>
                                <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $estadisticasGenerales['alertas_activas'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indicadores de Producción -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Estadísticas de Producción -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Indicadores de Producción
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Peso Promedio Actual</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($estadisticasProduccion['peso_promedio'], 2) }} kg
                            </div>
                        </div>
                        <div class="border-l-4 border-green-500 pl-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Crecimiento Promedio</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($estadisticasProduccion['crecimiento_promedio'], 3) }} kg/día
                            </div>
                        </div>
                        <div class="border-l-4 border-red-500 pl-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Mortalidad del Mes</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($estadisticasProduccion['mortalidad_mes']) }} individuos
                            </div>
                        </div>
                        <div class="border-l-4 border-yellow-500 pl-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Eficiencia Alimentaria</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($estadisticasProduccion['eficiencia_alimentaria'], 1) }}:1
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Especies en Producción -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Especies en Producción
                    </h3>
                    <div class="space-y-3">
                        @foreach($estadisticasProduccion['especies_produccion'] as $especie)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ ucfirst($especie->especie) }}
                                </span>
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300 rounded-full">
                                    {{ $especie->cantidad }} lotes
                                </span>
                            </div>
                        @endforeach
                        @if($estadisticasProduccion['especies_produccion']->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                                No hay especies en producción activa
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Gráfico de Mortalidad -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Mortalidad por Mes (Últimos 6 meses)
                    </h3>
                    <div class="h-64">
                        <canvas id="mortalidadChart"></canvas>
                    </div>
                </div>

                <!-- Gráfico de Biomasa por Especie -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Biomasa por Especie
                    </h3>
                    <div class="h-64">
                        <canvas id="biomasaChart"></canvas>
                    </div>
                </div>

                <!-- Gráfico de Ocupación de Unidades -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Ocupación por Tipo de Unidad
                    </h3>
                    <div class="h-64">
                        <canvas id="ocupacionChart"></canvas>
                    </div>
                </div>

                <!-- Gráfico de Crecimiento -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Peso Promedio (Últimas 4 semanas)
                    </h3>
                    <div class="h-64">
                        <canvas id="crecimientoChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Alertas y Actividad Reciente -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Alertas Críticas -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        Alertas Críticas
                    </h3>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @forelse($alertasNotificaciones['alertas_criticas'] as $alerta)
                            <div class="border-l-4 {{ $alerta->tipo_alerta == 'enfermedad' ? 'border-red-500 bg-red-50 dark:bg-red-900/10' : 'border-orange-500 bg-orange-50 dark:bg-orange-900/10' }} p-3 rounded-r">
                                <div class="flex items-start">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $alerta->getNivelAlerta() }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Lote: {{ $alerta->lote->codigo_lote }}
                                            @if($alerta->tipo_alerta == 'enfermedad')
                                                - {{ $alerta->nombre_enfermedad }}
                                                ({{ $alerta->porcentaje_afectados }}% afectados)
                                            @elseif($alerta->tipo_alerta == 'bajo peso')
                                                - Desviación: {{ $alerta->porcentaje_desviacion }}%
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $alerta->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">
                                No hay alertas críticas activas
                            </p>
                        @endforelse
                    </div>
                </div>

                <!-- Actividad Reciente -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Actividad Reciente
                    </h3>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @forelse($actividadReciente as $actividad)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-2 h-2 rounded-full mt-2 bg-{{ $actividad['color'] }}-500"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $actividad['descripcion'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($actividad['fecha'])->diffForHumans() }}
                                        - {{ $actividad['usuario'] }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">
                                No hay actividad reciente
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>


        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Configuración de Chart.js para tema oscuro
        Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#374151';
        Chart.defaults.borderColor = document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB';

        // Datos de los gráficos desde PHP
        const mortalidadData = @json($datosGraficos['mortalidad_por_mes']);
        const biomasaData = @json($datosGraficos['biomasa_por_especie']);
        const ocupacionData = @json($datosGraficos['ocupacion_unidades']);
        const crecimientoData = @json($datosGraficos['crecimiento_por_semana']);

        // Gráfico de Mortalidad
        const mortalidadCtx = document.getElementById('mortalidadChart').getContext('2d');
        new Chart(mortalidadCtx, {
            type: 'line',
            data: {
                labels: mortalidadData.map(item => item.mes),
                datasets: [{
                    label: 'Mortalidad',
                    data: mortalidadData.map(item => item.cantidad),
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Biomasa
        const biomasaCtx = document.getElementById('biomasaChart').getContext('2d');
        new Chart(biomasaCtx, {
            type: 'doughnut',
            data: {
                labels: biomasaData.map(item => item.especie),
                datasets: [{
                    data: biomasaData.map(item => item.biomasa),
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6',
                        '#06B6D4'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico de Ocupación
        const ocupacionCtx = document.getElementById('ocupacionChart').getContext('2d');
        new Chart(ocupacionCtx, {
            type: 'bar',
            data: {
                labels: ocupacionData.map(item => item.tipo),
                datasets: [{
                    label: 'Ocupación (%)',
                    data: ocupacionData.map(item => item.ocupacion),
                    backgroundColor: '#10B981',
                    borderColor: '#059669',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Gráfico de Crecimiento
        const crecimientoCtx = document.getElementById('crecimientoChart').getContext('2d');
        new Chart(crecimientoCtx, {
            type: 'line',
            data: {
                labels: crecimientoData.map(item => item.semana),
                datasets: [{
                    label: 'Peso Promedio (kg)',
                    data: crecimientoData.map(item => item.peso_promedio),
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Actualizar gráficos si cambia el tema
        window.addEventListener('storage', function(e) {
            if (e.key === 'theme') {
                location.reload();
            }
        });
    </script>
    @endpush
</x-app-layout>
