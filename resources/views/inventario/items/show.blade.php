<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('produccion.inventario.index') }}" 
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                        {{ $item->nombre }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ ucfirst($item->tipo) }} • {{ $item->unidad_base }}
                        @if($item->sku) • SKU: {{ $item->sku }} @endif
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $item->tipo === 'alimento' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' }}">
                    {{ ucfirst($item->tipo) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 space-y-6">
        @if (session('success'))
            <div class="rounded-lg p-4 bg-green-50 border border-green-200 dark:bg-green-900/30 dark:border-green-700">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-green-800 dark:text-green-200">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Panel principal con acciones --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Acciones rápidas --}}
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Acciones Rápidas
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ route('produccion.inventario.movimientos.create', 'entrada') }}?item_id={{ $item->id }}"
                                class="flex flex-col items-center p-4 border-2 border-emerald-200 rounded-lg hover:border-emerald-300 hover:bg-emerald-50 dark:border-emerald-700 dark:hover:bg-emerald-900/20 transition-colors group">
                                <div class="flex items-center justify-center w-12 h-12 bg-emerald-100 rounded-full group-hover:bg-emerald-200 dark:bg-emerald-800 dark:group-hover:bg-emerald-700 mb-3">
                                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Nueva Entrada</h4>
                                <p class="text-xs text-center text-gray-500 dark:text-gray-400">Registrar ingreso de stock</p>
                            </a>

                            <a href="{{ route('produccion.inventario.movimientos.create', 'salida') }}?item_id={{ $item->id }}"
                                class="flex flex-col items-center p-4 border-2 border-rose-200 rounded-lg hover:border-rose-300 hover:bg-rose-50 dark:border-rose-700 dark:hover:bg-rose-900/20 transition-colors group">
                                <div class="flex items-center justify-center w-12 h-12 bg-rose-100 rounded-full group-hover:bg-rose-200 dark:bg-rose-800 dark:group-hover:bg-rose-700 mb-3">
                                    <svg class="w-6 h-6 text-rose-600 dark:text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Nueva Salida</h4>
                                <p class="text-xs text-center text-gray-500 dark:text-gray-400">Registrar consumo de stock</p>
                            </a>

                            <a href="{{ route('produccion.inventario.movimientos.create', 'ajuste') }}?item_id={{ $item->id }}"
                                class="flex flex-col items-center p-4 border-2 border-indigo-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 dark:border-indigo-700 dark:hover:bg-indigo-900/20 transition-colors group">
                                <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-full group-hover:bg-indigo-200 dark:bg-indigo-800 dark:group-hover:bg-indigo-700 mb-3">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Ajustar Stock</h4>
                                <p class="text-xs text-center text-gray-500 dark:text-gray-400">Corregir inventario físico</p>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Stock por bodega --}}
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Existencias por Bodega
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Bodega
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Stock Actual
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                        Estado
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @foreach($stockPorBodega as $sb)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $sb->bodega->nombre }}
                                                    </div>
                                                    @if($sb->bodega->ubicacion)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $sb->bodega->ubicacion }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ number_format($sb->stock, 2) }} {{ $item->unidad_base }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($sb->stock > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                    Disponible
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                                    Sin stock
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                                    <td class="px-6 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        Total General
                                    </td>
                                    <td class="px-6 py-3 text-right text-sm text-gray-900 dark:text-gray-100">
                                        {{ number_format($item->stockTotal(), 2) }} {{ $item->unidad_base }}
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        @if($item->stockTotal() < $item->stock_minimo && $item->stock_minimo > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                ¡Bajo mínimo!
                                            </span>
                                        @elseif($item->stockTotal() > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                Disponible
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                Sin stock
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Últimos movimientos --}}
                @if($movimientos->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Últimos Movimientos
                                </h3>
                                <a href="{{ route('produccion.inventario.movimientos.index') }}?item_id={{ $item->id }}" 
                                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Ver todos →
                                </a>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Fecha
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Tipo
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Bodega
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Cantidad
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Descripción
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach($movimientos as $mov)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($mov->tipo === 'entrada') bg-emerald-100 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-100
                                                    @elseif($mov->tipo === 'salida') bg-rose-100 text-rose-800 dark:bg-rose-800 dark:text-rose-100
                                                    @else bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100
                                                    @endif">
                                                    @if($mov->tipo === 'entrada') Entrada
                                                    @elseif($mov->tipo === 'salida') Salida
                                                    @else Ajuste
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $mov->bodega->nombre ?? '—' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ number_format(abs($mov->cantidad_base), 3) }} {{ $item->unidad_base }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                <div class="max-w-xs truncate" title="{{ $mov->descripcion }}">
                                                    {{ $mov->descripcion ?: '—' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Panel lateral con información --}}
            <div class="space-y-6">
                {{-- Información del producto --}}
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Información</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $item->nombre }}</dd>
                        </div>
                        
                        @if($item->sku)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SKU</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $item->sku }}</dd>
                            </div>
                        @endif
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Categoría</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($item->tipo === 'alimento') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                    @else bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                    @endif">
                                    {{ ucfirst($item->tipo) }}
                                </span>
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unidad de medida</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $item->unidad_base }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock mínimo</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ number_format($item->stock_minimo, 2) }} {{ $item->unidad_base }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock actual</dt>
                            <dd class="mt-1">
                                <span class="text-lg font-semibold
                                    @if($item->stockTotal() < $item->stock_minimo && $item->stock_minimo > 0) text-red-600 dark:text-red-400
                                    @elseif($item->stockTotal() > 0) text-green-600 dark:text-green-400
                                    @else text-gray-500 dark:text-gray-400
                                    @endif">
                                    {{ number_format($item->stockTotal(), 2) }} {{ $item->unidad_base }}
                                </span>
                            </dd>
                        </div>
                        
                        @if($item->descripcion)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Descripción</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $item->descripcion }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Acciones del producto --}}
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Acciones</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <a href="{{ route('produccion.inventario.items.edit', $item) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar Producto
                        </a>
                        
                        <a href="{{ route('produccion.inventario.movimientos.index') }}?item_id={{ $item->id }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Ver Historial Completo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
