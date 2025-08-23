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
                <a href="{{ route('produccion.inventario.items.create') }}"
                    class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white shadow-sm">
                    Agregar Producto
                </a>
                <a href="{{ route('produccion.inventario.bodegas.index') }}"
                    class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                    🏢 Bodegas
                </a>
                <a href="{{ route('produccion.inventario.movimientos.index') }}"
                    class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Movimientos
                </a>
                <a href="{{ route('produccion.inventario.alertas.index') }}"
                    class="px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                    🚨 Alertas
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
                            {{ $it->nombre }} — total: {{ number_format($it->stockTotal(), 2) }}
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
                        <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" 
                            onclick="window.location.href='{{ route('produccion.inventario.items.show', $it->id) }}'">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-bold text-white
                                            @if($it->tipo === 'alimento') bg-green-500
                                            @else bg-blue-500
                                            @endif">
                                            @if($it->tipo === 'alimento') AL @else PR @endif
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $it->nombre }}</div>
                                        @if($it->sku)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $it->sku }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($it->tipo === 'alimento') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                    @else bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                    @endif">
                                    {{ ucfirst($it->tipo) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-900 dark:text-gray-100">{{ $it->unidad_base }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ number_format($it->stockTotal(), 2) }}
                                </div>
                                @if($it->stockTotal() < $it->stock_minimo && $it->stock_minimo > 0)
                                    <div class="text-xs text-red-600 dark:text-red-400">¡Bajo mínimo!</div>
                                @endif
                            </td>
                            @foreach ($bodegas as $b)
                                <td class="px-4 py-3 text-right text-sm text-gray-900 dark:text-gray-100">
                                    {{ number_format(optional($it->existencias->firstWhere('bodega_id', $b->id))->stock_actual ?? 0, 2) }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + $bodegas->count() }}"
                                class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Sin productos en inventario</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Comienza agregando productos para gestionar tu inventario</p>
                                    <a href="{{ route('produccion.inventario.items.create') }}" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm text-sm font-medium">
                                        Agregar primer producto
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
