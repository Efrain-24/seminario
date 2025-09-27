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
                        <th class="px-4 py-2 text-left">Venta</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cosechas as $c)
                        <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <td class="px-4 py-2">{{ $c->fecha?->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $c->lote->codigo_lote ?? '—' }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($c->cantidad_cosechada) }}</td>
                            <td class="px-4 py-2 text-right">
                                {{ $c->peso_cosechado_kg ? number_format($c->peso_cosechado_kg, 2) : '—' }}
                            </td>
                            <td class="px-4 py-2">
                                <span class="capitalize px-2 py-1 text-xs rounded-full {{ $c->destino === 'venta' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $c->destino }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                @if($c->destino === 'venta')
                                    <div class="space-y-1">
                                        @if($c->estado_venta === 'completada')
                                            <div class="text-xs">
                                                <span class="font-mono text-green-600">{{ $c->codigo_venta }}</span>
                                                <br>
                                                <span class="text-gray-600">{{ $c->cliente }}</span>
                                                <br>
                                                <span class="font-medium">C$ {{ number_format($c->total_venta, 2) }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">
                                                Pendiente
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <div class="flex justify-end space-x-1">
                                    @if($c->destino === 'venta')
                                        @if($c->estado_venta === 'completada')
                                            <!-- Botones de ticket -->
                                            <a href="{{ route('produccion.cosechas.ticket.ver', $c) }}" 
                                               target="_blank"
                                               class="text-purple-600 hover:text-purple-800 dark:text-purple-400" 
                                               title="Ver Ticket"
                                               onclick="event.stopPropagation()">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('produccion.cosechas.ticket.descargar', $c) }}" 
                                               class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400" 
                                               title="Descargar Ticket"
                                               onclick="event.stopPropagation()">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <!-- Botón para completar venta -->
                                            <a href="{{ route('produccion.cosechas.completar-venta', $c) }}" 
                                               class="text-green-600 hover:text-green-800 dark:text-green-400" 
                                               title="Completar Venta"
                                               onclick="event.stopPropagation()">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    @endif
                                    
                                    <a href="{{ route('produccion.cosechas.show', $c) }}" 
                                       class="text-gray-600 hover:text-gray-800 dark:text-gray-400" 
                                       title="Ver Detalles"
                                       onclick="event.stopPropagation()">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    <a href="{{ route('produccion.cosechas.edit', $c) }}" 
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400" 
                                       title="Editar"
                                       onclick="event.stopPropagation()">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    
                                    <form action="{{ route('produccion.cosechas.destroy', $c) }}" method="POST"
                                          class="inline" onsubmit="return confirm('¿Eliminar y revertir stock?')"
                                          onclick="event.stopPropagation()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
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
