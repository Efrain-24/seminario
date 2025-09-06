<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Cosechas Parciales
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4">

        @if (session('success'))
            <div class="mb-4 rounded p-3 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 sm:items-end sm:justify-between mb-4">
            {{-- Filtros --}}
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Lote</label>
                    <select name="lote_id"
                        class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                        <option value="">Todos</option>
                        @foreach ($lotes as $l)
                            <option value="{{ $l->id }}" @selected(request('lote_id') == $l->id)>{{ $l->codigo_lote }}
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
                @php($qs = request()->only('lote_id', 'desde', 'hasta'))
                <a href="{{ route('produccion.cosechas.create') }}"
                    class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                    Nueva Cosecha
                </a>
                <a href="{{ route('cosechas.trazabilidad.index') }}"
                    class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.26zm8.814-.569a1 1 0 00-1.415-1.414l-.707.707a1 1 0 101.415 1.415l.707-.708zm-7.071 7.072l.707-.707A1 1 0 003.465 9.12l-.708.707a1 1 0 001.415 1.415zm3.2-5.171a1 1 0 00-1.3 1.3l4 10a1 1 0 001.823.075l1.38-2.759 3.018 3.02a1 1 0 001.414-1.415l-3.019-3.02 2.76-1.379a1 1 0 00-.076-1.822l-10-4z" clip-rule="evenodd" />
                    </svg>
                    Trazabilidad
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Lote</th>
                        <th class="px-4 py-2 text-right">Cantidad</th>
                        <th class="px-4 py-2 text-right">Peso (kg)</th>
                        <th class="px-4 py-2 text-left">Destino</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cosechas as $c)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $c->fecha?->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $c->lote->codigo_lote ?? '—' }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($c->cantidad_cosechada) }}</td>
                            <td class="px-4 py-2 text-right">
                                {{ $c->peso_cosechado_kg ? number_format($c->peso_cosechado_kg, 2) : '—' }}
                            </td>
                            <td class="px-4 py-2 capitalize">{{ $c->destino }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('produccion.cosechas.edit', $c) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">Editar</a>
                                <form action="{{ route('produccion.cosechas.destroy', $c) }}" method="POST"
                                    class="inline" onsubmit="return confirm('¿Eliminar y revertir stock?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                Sin registros
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $cosechas->links() }}</div>
    </div>
</x-app-layout>
