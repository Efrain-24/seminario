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
                    @if($tipo === 'entrada')
                        Nueva Entrada de Inventario
                    @elseif($tipo === 'salida')
                        Nueva Salida de Inventario
                    @else
                        Ajuste de Inventario
                    @endif
                </h2>
            </div>
            <div class="flex items-center space-x-2 text-sm">
                <span class="px-3 py-1 rounded-full text-white
                    @if($tipo === 'entrada') bg-emerald-500
                    @elseif($tipo === 'salida') bg-rose-500
                    @else bg-indigo-500
                    @endif">
                    {{ ucfirst($tipo) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4">
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

        @if ($errors->any())
            <div class="mb-6 rounded-lg p-4 bg-red-50 border border-red-200 dark:bg-red-900/30 dark:border-red-700">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-red-800 dark:text-red-200 font-medium">Errores en el formulario:</span>
                </div>
                <ul class="list-disc list-inside text-red-700 dark:text-red-300 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Información contextual --}}
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4 dark:bg-blue-900/20 dark:border-blue-700">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium text-blue-800 dark:text-blue-200">
                    @if($tipo === 'entrada')
                        Registrar productos que ingresan al inventario
                    @elseif($tipo === 'salida')
                        Registrar productos que salen del inventario
                    @else
                        Corregir cantidades existentes en bodega
                    @endif
                </span>
            </div>
            <p class="text-sm text-blue-700 dark:text-blue-300">
                @if($tipo === 'entrada')
                    Las entradas aumentan el stock disponible. Puedes especificar lote y fecha de vencimiento para mejor trazabilidad.
                @elseif($tipo === 'salida')
                    Las salidas reducen el stock disponible. El sistema verificará que haya suficiente stock antes de procesar.
                @else
                    Los ajustes establecen una cantidad exacta. El sistema calculará automáticamente la diferencia y registrará el movimiento.
                @endif
            </p>
        </div>

        {{-- Formulario --}}
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Detalles del {{ ucfirst($tipo) }}
                </h3>
            </div>

            <form method="POST" action="{{ route('produccion.inventario.movimientos.store') }}" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="tipo" value="{{ $tipo }}">

                {{-- Información básica --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Selección de Artículo --}}
                    <div class="space-y-2">
                        <label for="item_id" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Artículo <span class="text-red-500">*</span>
                        </label>
                        <select name="item_id" id="item_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                            required onchange="updateItemInfo()">
                            <option value="">Seleccionar artículo...</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" 
                                    data-tipo="{{ $item->tipo }}"
                                    data-unidad="{{ $item->unidad_base }}"
                                    data-stock-min="{{ $item->stock_minimo }}"
                                    data-stock-total="{{ $item->stockTotal() }}"
                                    {{ (old('item_id') == $item->id || request('item_id') == $item->id || (isset($selectedItemId) && $selectedItemId == $item->id)) ? 'selected' : '' }}>
                                    {{ $item->nombre }} ({{ ucfirst($item->tipo) }} - {{ $item->unidad_base }})
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        
                        {{-- Info del artículo seleccionado --}}
                        <div id="item-info" class="hidden mt-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-300">
                                <span id="item-tipo" class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium"></span>
                                <span>Unidad: <strong id="item-unidad"></strong></span>
                                <span>Stock mínimo: <strong id="item-stock-min"></strong></span>
                                <span>Stock total: <strong id="item-stock-total" class="text-green-600 dark:text-green-400"></strong></span>
                            </div>
                        </div>
                    </div>

                    {{-- Selección de Bodega --}}
                    <div class="space-y-2">
                        <label for="bodega_id" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Bodega <span class="text-red-500">*</span>
                        </label>
                        <select name="bodega_id" id="bodega_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                            required>
                            <option value="">Seleccionar bodega...</option>
                            @foreach($bodegas as $bodega)
                                <option value="{{ $bodega->id }}" {{ old('bodega_id') == $bodega->id ? 'selected' : '' }}>
                                    {{ $bodega->nombre }}
                                    @if($bodega->ubicacion)
                                        - {{ $bodega->ubicacion }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('bodega_id')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Fecha --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="fecha" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Fecha del movimiento <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="fecha" id="fecha"
                            value="{{ old('fecha', date('Y-m-d')) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                            required>
                        @error('fecha')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Cantidades --}}
                @if ($tipo !== 'ajuste')
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="cantidad" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                Cantidad <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.0001" min="0.0001" name="cantidad" id="cantidad"
                                value="{{ old('cantidad') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                placeholder="0.00" required>
                            @error('cantidad')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label for="unidad" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                                Unidad <span class="text-red-500">*</span>
                            </label>
                            <select name="unidad" id="unidad"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                required>
                                <option value="kg" {{ old('unidad') == 'kg' ? 'selected' : '' }}>Kilogramos (kg)</option>
                                <option value="lb" {{ old('unidad') == 'lb' ? 'selected' : '' }}>Libras (lb)</option>
                                <option value="unidad" {{ old('unidad') == 'unidad' ? 'selected' : '' }}>Unidades</option>
                                <option value="litro" {{ old('unidad') == 'litro' ? 'selected' : '' }}>Litros</option>
                            </select>
                            @error('unidad')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="nuevo_stock" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Nuevo stock (unidad base) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.0001" min="0" name="nuevo_stock" id="nuevo_stock"
                                value="{{ old('nuevo_stock') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                placeholder="0.00" required>
                            @error('nuevo_stock')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Se calculará automáticamente la diferencia con el stock actual
                            </p>
                        </div>
                        
                        {{-- Mostrar diferencia calculada --}}
                        <div id="ajuste-info" class="hidden space-y-2">
                            <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Diferencia calculada
                            </label>
                            <div class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <span id="diferencia-calculada" class="text-sm font-medium">--</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Información de lote (solo para entradas) --}}
                @if ($tipo === 'entrada')
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Información de Lote (Opcional)
                        </h4>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="lote" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Número de lote
                                </label>
                                <input type="text" name="lote" id="lote"
                                    value="{{ old('lote') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                    placeholder="Ej: LT001-2025">
                                @error('lote')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="space-y-2">
                                <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha de vencimiento
                                </label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                                    value="{{ old('fecha_vencimiento') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                @error('fecha_vencimiento')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                <strong>Recomendación:</strong> Especificar lote y fecha de vencimiento ayuda con la trazabilidad y el control FEFO (First Expired, First Out) en las salidas.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Descripción --}}
                <div class="space-y-2">
                    <label for="descripcion" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Descripción / Observaciones
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                        placeholder="@if($tipo === 'entrada')Compra a proveedor XYZ, factura #123...@elseif($tipo === 'salida')Consumo para lote de producción #456...@elseAjuste por inventario físico...@endif">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('produccion.inventario.movimientos.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cancelar
                    </a>
                    
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2 rounded-lg shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2
                        @if($tipo === 'entrada') bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500
                        @elseif($tipo === 'salida') bg-rose-600 hover:bg-rose-700 focus:ring-rose-500
                        @else bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500
                        @endif">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        @if($tipo === 'entrada')
                            Registrar Entrada
                        @elseif($tipo === 'salida')
                            Registrar Salida
                        @else
                            Aplicar Ajuste
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript para interactividad --}}
    <script>
        // Datos de existencias (se cargarían dinámicamente en producción)
        let existenciasData = {};

        function updateItemInfo() {
            const select = document.getElementById('item_id');
            const option = select.options[select.selectedIndex];
            const infoDiv = document.getElementById('item-info');
            
            if (option.value) {
                document.getElementById('item-tipo').textContent = option.dataset.tipo.toUpperCase();
                document.getElementById('item-unidad').textContent = option.dataset.unidad;
                document.getElementById('item-stock-min').textContent = option.dataset.stockMin || '0';
                document.getElementById('item-stock-total').textContent = (parseFloat(option.dataset.stockTotal) || 0).toFixed(2) + ' ' + option.dataset.unidad;
                infoDiv.classList.remove('hidden');
                
                // Actualizar unidad por defecto
                const unidadSelect = document.getElementById('unidad');
                if (unidadSelect) {
                    unidadSelect.value = option.dataset.unidad;
                }
            } else {
                infoDiv.classList.add('hidden');
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const itemSelect = document.getElementById('item_id');
            const bodegaSelect = document.getElementById('bodega_id');
            const nuevoStockInput = document.getElementById('nuevo_stock');
            
            itemSelect.addEventListener('change', updateItemInfo);
            
            // Si hay un item preseleccionado desde la URL, actualizar info
            if (itemSelect.value) {
                updateItemInfo();
            }
            
            // Inicializar si hay valores seleccionados
            if (itemSelect.value) updateItemInfo();
        });
        });
    </script>
</x-app-layout>
