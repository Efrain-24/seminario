<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Control de Producción — Biomasa Estimada
        </h2>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-8 max-w-6xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Lote</th>
                        <th class="px-4 py-2 text-right">Cantidad actual</th>
                        <th class="px-4 py-2 text-right">Peso prom. (g)</th>
                        <th class="px-4 py-2 text-right">Biomasa (kg)</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" 
                            onclick="window.location.href='{{ route('produccion.control.show', $r['lote']) }}'">
                            <td class="px-4 py-2">{{ $r['lote']->codigo_lote }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($r['lote']->cantidad_actual) }}</td>
                            <td class="px-4 py-2 text-right">
                                {{ $r['peso_promedio_g'] ? number_format($r['peso_promedio_g'], 1) : '—' }}g</td>
                            <td class="px-4 py-2 text-right">
                                {{ $r['biomasa_kg'] ? number_format($r['biomasa_kg'], 2) : '—' }}</td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('produccion.control.show', $r['lote']) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">Ver/Predecir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                No hay lotes
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
