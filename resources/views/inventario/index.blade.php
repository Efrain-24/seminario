<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Inventario — Alimento e Insumos
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 space-y-6">
        @if (session('success'))
            <div class="mb-4 rounded p-3 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        {{-- Acciones --}}
        <div class="flex flex-col sm:flex-row gap-3 sm:items-end sm:justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-300"></div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('produccion.inventario.items.index') }}"
                    class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Gestionar ítems
                </a>
                <a href="{{ route('produccion.inventario.bodegas.index') }}"
                    class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Bodegas
                </a>
                <a href="{{ route('produccion.inventario.movimientos.index') }}"
                    class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Movimientos
                </a>
                <a href="{{ route('produccion.inventario.movimientos.create', 'entrada') }}"
                    class="px-3 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white">
                    + Entrada
                </a>
                <a href="{{ route('produccion.inventario.movimientos.create', 'salida') }}"
                    class="px-3 py-2 rounded bg-rose-600 hover:bg-rose-700 text-white">
                    − Salida
                </a>
                <a href="{{ route('produccion.inventario.movimientos.create', 'ajuste') }}"
                    class="px-3 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">
                    ⟲ Ajuste
                </a>
            </div>
        </div>

        {{-- Alertas de stock bajo --}}
        @if ($low->count())
            <div
                class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-xl p-4">
                <div class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">Stock bajo</div>
                <ul class="list-disc ms-6 text-sm text-yellow-800 dark:text-yellow-200">
                    @foreach ($low as $it)
                        <li>
                            {{ $it->nombre }} — total: {{ number_format($it->stockTotal(), 3) }}
                            {{ $it->unidad_base }}
                            (mín: {{ $it->stock_minimo }})
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Tabla de existencias por bodega --}}
        <div
            class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden border border-gray-200 dark:border-gray-700">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Ítem</th>
                        <th class="px-4 py-2 text-left">Tipo</th>
                        <th class="px-4 py-2 text-left">Unidad</th>
                        <th class="px-4 py-2 text-right">Stock total</th>
                        @foreach ($bodegas as $b)
                            <th class="px-4 py-2 text-right">{{ $b->nombre }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $it)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $it->nombre }}</td>
                            <td class="px-4 py-2 capitalize">{{ $it->tipo }}</td>
                            <td class="px-4 py-2">{{ $it->unidad_base }}</td>
                            <td class="px-4 py-2 text-right font-semibold">{{ number_format($it->stockTotal(), 3) }}
                            </td>
                            @foreach ($bodegas as $b)
                                <td class="px-4 py-2 text-right">
                                    {{ number_format(optional($it->existencias->firstWhere('bodega_id', $b->id))->stock_actual ?? 0, 3) }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + $bodegas->count() }}"
                                class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                Sin ítems.
                                <a href="{{ route('produccion.inventario.items.create') }}" class="underline">Crear
                                    ítem</a>
                                o
                                <a href="{{ route('produccion.inventario.movimientos.create', 'entrada') }}"
                                    class="underline">registrar entrada</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
