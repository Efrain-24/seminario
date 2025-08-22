<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Alertas de Inventario — Insumos bajos o vencidos
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 space-y-6">
        @if (session('success'))
            <div class="mb-4 rounded p-3 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                {{ session('success') }}</div>
        @endif

        {{-- Filtros --}}
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Tipo</label>
                <select name="tipo"
                    class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                    <option value="">Todos</option>
                    @foreach (['alimento' => 'Alimento', 'insumo' => 'Insumo'] as $v => $txt)
                        <option value="{{ $v }}" @selected($tipo === $v)>{{ $txt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Bodega</label>
                <select name="bodega_id"
                    class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                    <option value="">Todas</option>
                    @foreach ($bodegas as $b)
                        <option value="{{ $b->id }}" @selected($bodegaId == $b->id)>{{ $b->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Por vencer en (días)</label>
                <input type="number" name="dias" min="1" value="{{ $dias }}"
                    class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2 w-28">
            </div>
            <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Aplicar</button>
        </form>

        {{-- Bloque: Stock bajo --}}
        <div
            class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden border border-gray-200 dark:border-gray-700">
            <div
                class="px-4 py-3 font-semibold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 border-b dark:border-gray-700">
                Ítems con stock bajo
            </div>
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Ítem</th>
                        <th class="px-4 py-2 text-left">Tipo</th>
                        <th class="px-4 py-2 text-left">Unidad</th>
                        <th class="px-4 py-2 text-right">Stock total</th>
                        <th class="px-4 py-2 text-right">Mínimo</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bajos as $it)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $it->nombre }}</td>
                            <td class="px-4 py-2 capitalize">{{ $it->tipo }}</td>
                            <td class="px-4 py-2">{{ $it->unidad_base }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($it->stockTotal(), 3) }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($it->stock_minimo, 3) }}</td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('produccion.inventario.movimientos.create', 'entrada') }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">Registrar entrada</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin
                                alertas de bajo stock</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Bloque: Lotes vencidos --}}
        <div
            class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden border border-gray-200 dark:border-gray-700">
            <div
                class="px-4 py-3 font-semibold text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 border-b dark:border-gray-700">
                Lotes vencidos (con stock)
            </div>
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Ítem</th>
                        <th class="px-4 py-2">Lote</th>
                        <th class="px-4 py-2">Bodega</th>
                        <th class="px-4 py-2">Venció</th>
                        <th class="px-4 py-2 text-right">Stock lote</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vencidos as $l)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $l->item->nombre }}</td>
                            <td class="px-4 py-2 text-center">{{ $l->lote ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ $l->bodega->nombre }}</td>
                            <td class="px-4 py-2 text-center">
                                {{ \Carbon\Carbon::parse($l->fecha_vencimiento)->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($l->stock_lote, 3) }}</td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('produccion.inventario.movimientos.create', 'ajuste') }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">Ajustar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin lotes
                                vencidos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Bloque: Lotes por vencer --}}
        <div
            class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden border border-gray-200 dark:border-gray-700">
            <div
                class="px-4 py-3 font-semibold text-orange-700 dark:text-orange-300 bg-orange-50 dark:bg-orange-900/30 border-b dark:border-gray-700">
                Lotes por vencer en {{ $dias }} días
            </div>
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Ítem</th>
                        <th class="px-4 py-2">Lote</th>
                        <th class="px-4 py-2">Bodega</th>
                        <th class="px-4 py-2">Vence</th>
                        <th class="px-4 py-2 text-right">Stock lote</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($porVencer as $l)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $l->item->nombre }}</td>
                            <td class="px-4 py-2 text-center">{{ $l->lote ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ $l->bodega->nombre }}</td>
                            <td class="px-4 py-2 text-center">
                                {{ \Carbon\Carbon::parse($l->fecha_vencimiento)->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($l->stock_lote, 3) }}</td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('produccion.inventario.movimientos.create', 'salida') }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">Usar primero</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin lotes
                                por vencer</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
