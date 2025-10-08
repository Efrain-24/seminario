<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('produccion.inventario.index') }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 hover:text-gray-700 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Inventario
                </a>
                <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                    Movimientos de Inventario
                </h2>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2H5a2 2 0 00-2-2V5a2 2 0 012-2h14"/>
                </svg>
                <span>{{ $movs->total() }} movimientos</span>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-8 max-w-7xl mx-auto px-4">
        @if (session('success'))
            <div class="mb-6 rounded-lg p-4 bg-green-50 border border-green-200 dark:bg-green-900/30 dark:border-green-700">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-green-800 dark:text-green-200">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        {{-- Panel de filtros mejorado --}}
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                    </svg>
                    Filtros de Búsqueda
                </h3>
            </div>
            
            <form method="GET" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-4">
                    <div class="space-y-2">
                        <label for="item_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Artículo</label>
                        <select name="item_id" id="item_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            <option value="">Todos los artículos</option>
                            @foreach ($items as $i)
                                <option value="{{ $i->id }}" {{ request('item_id') == $i->id ? 'selected' : '' }}>
                                    {{ $i->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="bodega_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bodega</label>
                        <select name="bodega_id" id="bodega_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            <option value="">Todas las bodegas</option>
                            @foreach ($bodegas as $b)
                                <option value="{{ $b->id }}" {{ request('bodega_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo</label>
                        <select name="tipo" id="tipo"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            <option value="">Todos los tipos</option>
                            <option value="entrada" {{ request('tipo') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="salida" {{ request('tipo') == 'salida' ? 'selected' : '' }}>Salida</option>
                            <option value="ajuste" {{ request('tipo') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="desde" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha desde</label>
                        <input type="date" name="desde" id="desde" value="{{ request('desde') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="hasta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha hasta</label>
                        <input type="date" name="hasta" id="hasta" value="{{ request('hasta') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </div>
                    
                    <div class="flex items-end space-x-2">
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filtrar
                        </button>
                        @if(request()->hasAny(['item_id', 'bodega_id', 'tipo', 'desde', 'hasta']))
                            <a href="{{ route('produccion.inventario.movimientos.index') }}"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
                
                @if(request()->hasAny(['item_id', 'bodega_id', 'tipo', 'desde', 'hasta']))
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap gap-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Filtros activos:</span>
                            @if(request('item_id'))
                                @php $item = $items->firstWhere('id', request('item_id')) @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                    {{ $item?->nombre ?? 'Artículo' }}
                                </span>
                            @endif
                            @if(request('bodega_id'))
                                @php $bodega = $bodegas->firstWhere('id', request('bodega_id')) @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                    {{ $bodega?->nombre ?? 'Bodega' }}
                                </span>
                            @endif
                            @if(request('tipo'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                    {{ ucfirst(request('tipo')) }}
                                </span>
                            @endif
                            @if(request('desde') || request('hasta'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                    {{ request('desde') ?? '...' }} - {{ request('hasta') ?? '...' }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>

        {{-- Acciones principales --}}
        <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between mb-6">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                @if($movs->hasPages())
                    Mostrando {{ $movs->firstItem() }}-{{ $movs->lastItem() }} de {{ $movs->total() }} movimientos
                @else
                    {{ $movs->total() }} movimientos encontrados
                @endif
            </div>
            
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('produccion.inventario.movimientos.create', 'entrada') }}"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nueva Entrada
                </a>
                <a href="{{ route('produccion.inventario.movimientos.create', 'salida') }}"
                    class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                    Nueva Salida
                </a>
                <a href="{{ route('produccion.inventario.movimientos.create', 'ajuste') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Nuevo Ajuste
                </a>
            </div>
        </div>

        {{-- Tabla de movimientos --}}
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            @if($movs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Fecha
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Artículo
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Bodega
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Tipo
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Cantidad Base
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Cantidad Original
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Descripción
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Usuario
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($movs as $m)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold text-white
                                                    @if($m->item?->tipo === 'alimento') bg-green-500
                                                    @elseif($m->item?->tipo === 'medicamento') bg-red-500
                                                    @elseif($m->item?->tipo === 'equipo') bg-blue-500
                                                    @else bg-gray-500
                                                    @endif">
                                                    @if($m->item?->tipo === 'alimento') AL
                                                    @elseif($m->item?->tipo === 'medicamento') MD
                                                    @elseif($m->item?->tipo === 'equipo') EQ
                                                    @else PR
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $m->item->nombre ?? '—' }}
                                                </div>
                                                @if($m->item)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ ucfirst($m->item->tipo) }} • {{ $m->item->unidad_base }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ $m->bodega->nombre ?? '—' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($m->tipo === 'entrada') bg-emerald-100 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-100
                                            @elseif($m->tipo === 'salida') bg-rose-100 text-rose-800 dark:bg-rose-800 dark:text-rose-100
                                            @else bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100
                                            @endif">
                                            @if($m->tipo === 'entrada') Entrada
                                            @elseif($m->tipo === 'salida') Salida
                                            @else Ajuste
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                                        <div class="text-gray-900 dark:text-gray-100">
                                            {{ number_format(abs($m->cantidad_base), 3) }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $m->item?->unidad_base ?? 'unidad' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">
                                        @if ($m->unidad_origen && $m->cantidad_origen)
                                            <div>{{ number_format(abs($m->cantidad_origen), 3) }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $m->unidad_origen }}</div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        <div class="max-w-xs truncate" title="{{ $m->descripcion }}">
                                            {{ $m->descripcion ?: '—' }}
                                        </div>
                                        @if($m->referencia_type)
                                            <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                📎 Referenciado
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if($m->user_id)
                                            <div class="flex items-center justify-center">
                                                <div class="h-6 w-6 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                        {{ substr(($m->user->name ?? 'Usuario'), 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A9.971 9.971 0 0124 24c4.21 0 7.863 2.613 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0zm12 6a4 4 0 11-8 0 4 4 0 018 0zm-28 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sin movimientos</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if(request()->hasAny(['item_id', 'bodega_id', 'tipo', 'desde', 'hasta']))
                            No se encontraron movimientos que coincidan con los filtros aplicados.
                        @else
                            No hay movimientos de inventario registrados.
                        @endif
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('produccion.inventario.movimientos.create', 'entrada') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Registrar primera entrada
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Paginación --}}
        @if($movs->hasPages())
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Mostrando
                    <span class="font-medium">{{ $movs->firstItem() }}</span>
                    a
                    <span class="font-medium">{{ $movs->lastItem() }}</span>
                    de
                    <span class="font-medium">{{ $movs->total() }}</span>
                    resultados
                </div>
                <div>
                    {{ $movs->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
