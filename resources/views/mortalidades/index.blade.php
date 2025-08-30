<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Registro de Mortalidad</h2>
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
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Tanque/Unidad</label>
                    <select name="unidad_produccion_id"
                        class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                        <option value="">Todos</option>
                        @foreach ($unidades as $u)
                            <option value="{{ $u->id }}" @selected(request('unidad_produccion_id') == $u->id)>{{ $u->nombre ?? $u->codigo }} ({{ $u->tipo }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Lote</label>
                    <select name="lote_id"
                        class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2">
                        <option value="">Todos</option>
                        @foreach ($lotes as $l)
                            <option value="{{ $l->id }}" @selected(request('lote_id') == $l->id)>{{ $l->codigo_lote }}</option>
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
                <a href="{{ route('produccion.mortalidades.charts', $qs) }}"
                    class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                    Ver gráficos
                </a>


                <script>
                    document.getElementById('logUnidadSelect').addEventListener('change', function() {
                        var unidadId = this.value;
                        if (unidadId) {
                            window.location.href = '/produccion/unidades/' + unidadId + '/mortalidad-log';
                        }
                    });
                </script>

                <a href="{{ route('produccion.mortalidades.create') }}"
                    class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                    Nuevo registro
                </a>
            </div>
            <script>
                function verLogMortalidad() {
                    var loteId = document.querySelector('select[name="lote_id"]').value;
                    if (!loteId) {
                        alert('Selecciona un lote para ver su log de mortalidad.');
                        return;
                    }
                    window.location.href = '/produccion/lotes/' + loteId + '/mortalidad-log';
                }
            </script>
        </div>



        <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Tanque/Unidad</th>
                        <th class="px-4 py-2 text-left">Lote</th>
                        <th class="px-4 py-2 text-right">Cantidad</th>
                        <th class="px-4 py-2 text-left">Causa</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mortalidades as $m)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $m->fecha?->format('Y-m-d') }}</td>
                            <td class="px-4 py-2">{{ $m->unidadProduccion->nombre ?? $m->unidadProduccion->codigo ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $m->lote->codigo_lote ?? '—' }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($m->cantidad) }}</td>
                            <td class="px-4 py-2">{{ $m->causa ?? '—' }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('produccion.mortalidades.edit', $m) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">Editar</a>
                                <form action="{{ route('produccion.mortalidades.destroy', $m) }}" method="POST"
                                    class="inline" onsubmit="return confirm('¿Eliminar y devolver stock?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin
                                registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <div class="mt-4">{{ $mortalidades->links() }}</div>
    </div>
</x-app-layout>
