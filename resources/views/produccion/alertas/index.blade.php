<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Alertas por anomalías — Bajo peso
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 space-y-6">

        {{-- Filtros/Parámetros --}}
        <form method="GET"
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Lote</label>
                <select name="lote_id"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                    <option value="">Todos</option>
                    @foreach ($lotes as $l)
                        <option value="{{ $l->id }}" @selected($loteId == $l->id)>{{ $l->codigo_lote }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">FCR</label>
                <input type="number" step="0.01" name="fcr" value="{{ $fcr }}"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
            </div>
            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Tolerancia (%)</label>
                <input type="number" step="1" min="5" max="90" name="tol"
                    value="{{ $tol }}"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
            </div>
            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Mín. días entre biometrías</label>
                <input type="number" min="1" name="min_dias" value="{{ $minDias }}"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
            </div>
            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Mín. alimento periodo (kg)</label>
                <input type="number" min="0" step="0.1" name="min_feed_kg" value="{{ $minFeedKg }}"
                    class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
            </div>
            <div class="sm:col-span-2 md:col-span-3 lg:col-span-6 flex justify-end">
                <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Aplicar</button>
            </div>
        </form>

        {{-- Tabla de alertas --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Lote</th>
                        <th class="px-4 py-2 text-left">Periodo</th>
                        <th class="px-4 py-2 text-right">Días</th>
                        <th class="px-4 py-2 text-right">Alimento (kg)</th>
                        <th class="px-4 py-2 text-right">Pobl. prom</th>
                        <th class="px-4 py-2 text-right">Peso ini / fin (g)</th>
                        <th class="px-4 py-2 text-right">Gan. esp. (g)</th>
                        <th class="px-4 py-2 text-right">Gan. obs. (g)</th>
                        <th class="px-4 py-2 text-right">Déficit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alertas as $a)
                        @php
                            $sev = $a['deficit_pct']; // %
                            $badge = $sev >= 40 ? 'bg-red-600' : ($sev >= 25 ? 'bg-orange-500' : 'bg-yellow-500');
                        @endphp
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $a['codigo_lote'] }}</td>
                            <td class="px-4 py-2">{{ $a['desde'] }} → {{ $a['hasta'] }}</td>
                            <td class="px-4 py-2 text-right">{{ $a['dias'] }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($a['alimento_kg'], 2) }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($a['pobl_prom']) }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($a['peso_inicial_g'], 2) }} /
                                {{ number_format($a['peso_final_g'], 2) }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($a['ganancia_esperada_g'], 2) }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($a['ganancia_observada_g'], 2) }}</td>
                            <td class="px-4 py-2 text-right">
                                <span class="text-white px-2 py-1 rounded {{ $badge }}">
                                    -{{ number_format($a['deficit_pct'], 2) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                No se detectaron anomalías con los parámetros actuales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-500 dark:text-gray-400">
            Regla: si la ganancia observada &lt; ganancia esperada × (1 − tolerancia). FCR={{ $fcr }},
            tolerancia={{ $tol }}%.
        </p>
    </div>
</x-app-layout>
