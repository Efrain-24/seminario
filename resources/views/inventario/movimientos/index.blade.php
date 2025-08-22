<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Movimientos de inventario</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4">
        @if (session('success'))
            <div class="mb-4 rounded p-3 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                {{ session('success') }}</div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 sm:items-end sm:justify-between mb-4">
            {{-- Filtros --}}
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Ítem</label>
                    <select name="item_id"
                        class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                        <option value="">Todos</option>
                        @foreach ($items as $i)
                            <option value="{{ $i->id }}" @selected(request('item_id') == $i->id)>{{ $i->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Bodega</label>
                    <select name="bodega_id"
                        class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                        <option value="">Todas</option>
                        @foreach ($bodegas as $b)
                            <option value="{{ $b->id }}" @selected(request('bodega_id') == $b->id)>{{ $b->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Tipo</label>
                    <select name="tipo"
                        class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                        <option value="">Todos</option>
                        @foreach (['entrada', 'salida', 'ajuste'] as $t)
                            <option value="{{ $t }}" @selected(request('tipo') == $t)>{{ ucfirst($t) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Desde</label>
                    <input type="date" name="desde" value="{{ request('desde') }}"
                        class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}"
                        class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                </div>
                <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Filtrar</button>
            </form>

            {{-- Acciones --}}
            <div class="flex gap-2">
                <a href="{{ route('produccion.inventario.movimientos.create', 'entrada') }}"
                    class="px-4 py-2 rounded bg-emerald-600 hover:bg-emerald-700 text-white">+ Entrada</a>
                <a href="{{ route('produccion.inventario.movimientos.create', 'salida') }}"
                    class="px-4 py-2 rounded bg-rose-600 hover:bg-rose-700 text-white">− Salida</a>
                <a href="{{ route('produccion.inventario.movimientos.create', 'ajuste') }}"
                    class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">⟲ Ajuste</a>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden border border-gray-200 dark:border-gray-700">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-3 py-2">Fecha</th>
                        <th class="px-3 py-2 text-left">Ítem</th>
                        <th class="px-3 py-2 text-left">Bodega</th>
                        <th class="px-3 py-2">Tipo</th>
                        <th class="px-3 py-2 text-right">Cant. base</th>
                        <th class="px-3 py-2 text-right">Origen</th>
                        <th class="px-3 py-2 text-left">Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movs as $m)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-3 py-2">{{ \Carbon\Carbon::parse($m->fecha)->format('Y-m-d') }}</td>
                            <td class="px-3 py-2">{{ $m->item->nombre ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $m->bodega->nombre ?? '—' }}</td>
                            <td class="px-3 py-2 text-center capitalize">{{ $m->tipo }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($m->cantidad_base, 3) }}</td>
                            <td class="px-3 py-2 text-right">
                                @if ($m->unidad_origen)
                                    {{ number_format($m->cantidad_origen, 3) }} {{ $m->unidad_origen }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-3 py-2">{{ $m->descripcion }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin
                                movimientos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $movs->links() }}</div>
    </div>
</x-app-layout>
