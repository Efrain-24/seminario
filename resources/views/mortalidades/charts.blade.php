<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Gráficos de Mortalidad
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 space-y-6">

        {{-- Filtros --}}
        <form method="GET"
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 grid sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Lote</label>
                <select name="lote_id"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                    @foreach ($lotes as $l)
                        <option value="{{ $l->id }}" @selected($l->id == $loteId)>{{ $l->codigo_lote }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
            </div>

            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
            </div>

            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Agrupar</label>
                <select name="group"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                    <option value="day" @selected($group === 'day')>Día</option>
                    <option value="week" @selected($group === 'week')>Semana</option>
                    <option value="month" @selected($group === 'month')>Mes</option>
                </select>
            </div>

            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Población base (peces)</label>
                <input type="number" name="stock_base" min="1" value="{{ $stockBase }}"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
            </div>

            <div class="sm:col-span-2 md:col-span-4 lg:col-span-5 flex justify-end">
                <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Aplicar</button>
            </div>
        </form>

        {{-- Gráfico 1: Muertes --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <h3 class="text-base font-semibold mb-3 text-gray-900 dark:text-gray-100">Muertes por período</h3>
            <canvas id="chartMuertes" height="120"></canvas>
        </div>

        {{-- Gráfico 2: Tasa (%) --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Tasa de mortalidad (%)</h3>
                <div class="text-xs text-gray-500 dark:text-gray-400">Fórmula: muertes / población_base × 100</div>
            </div>
            <canvas id="chartTasa" height="120"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($labels);
        const muertes = @json($muertes);
        const tasas = @json($tasas);

        // Detecta modo oscuro para tonos del grid/ejes
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';
        const tickColor = isDark ? '#e5e7eb' : '#374151';

        // Muertes por período
        new Chart(document.getElementById('chartMuertes'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Muertes',
                    data: muertes,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        ticks: {
                            color: tickColor
                        },
                        grid: {
                            color: gridColor
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: tickColor
                        },
                        grid: {
                            color: gridColor
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: tickColor
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });

        // Tasa (%)
        new Chart(document.getElementById('chartTasa'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Tasa %',
                    data: tasas,
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        ticks: {
                            color: tickColor
                        },
                        grid: {
                            color: gridColor
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: tickColor
                        },
                        grid: {
                            color: gridColor
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: tickColor
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.parsed.y}%`
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
