<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Nuevo Protocolo de Sanidad</h2>
    </x-slot>
    <div class="py-8 max-w-4xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form action="{{ route('protocolo-sanidad.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Información Básica -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Nombre del Protocolo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre" 
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Ej. Desinfección de Estanques" required>
                    </div>
                    <div>
                        <label for="fecha_implementacion" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Fecha de Implementación <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="fecha_implementacion" name="fecha_implementacion" 
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="responsable" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Responsable <span class="text-red-500">*</span>
                        </label>
                        <select id="responsable" name="responsable" 
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Seleccionar responsable...</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->name }}">{{ $usuario->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Descripción
                        </label>
                        <textarea id="descripcion" name="descripcion" rows="3"
                                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                  placeholder="Descripción opcional del protocolo..."></textarea>
                    </div>
                </div>

                <!-- Actividades del Protocolo -->
                <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Actividades del Protocolo</h3>
                    <div id="actividades-container" class="space-y-3">
                        <div class="flex gap-2 actividad-item">
                            <input type="text" name="actividades[]" 
                                   placeholder="Ej. Revisar nivel de pH del agua" 
                                   class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" onclick="removeActividad(this)" 
                                    class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                                ✕
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="addActividad()" 
                            class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        + Agregar Actividad
                    </button>
                </div>

                <!-- Insumos Necesarios (Opcional) -->
                <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Insumos Necesarios</h3>
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="requiere-insumos" onchange="toggleInsumos()" 
                                   class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                Este protocolo requiere insumos
                            </span>
                        </label>
                    </div>
                    
                    <div id="insumos-section" class="hidden space-y-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Información sobre insumos</h4>
                                    <div class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Solo insumos del inventario (no alimentos)</li>
                                            <li>Se descontarán automáticamente al ejecutar</li>
                                            <li>Se verifica stock disponible antes de ejecutar</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="insumos-container" class="space-y-4">
                            <!-- Template del primer insumo -->
                            <div class="insumo-item bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-md font-medium text-gray-800 dark:text-gray-200">Insumo #1</h4>
                                    <button type="button" onclick="removeInsumo(this)" 
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        ✕ Eliminar
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                            Insumo <span class="text-red-500">*</span>
                                        </label>
                                        <select name="insumos[0][inventario_item_id]" 
                                                class="insumo-select w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                onchange="updateInsumoInfo(this)">
                                            <option value="">Seleccionar insumo...</option>
                                            @foreach($insumos as $insumo)
                                                <option value="{{ $insumo->id }}" 
                                                        data-unidad="{{ $insumo->unidad_base }}" 
                                                        data-stock="{{ $insumo->stockTotal() }}">
                                                    {{ $insumo->nombre }} 
                                                    @if($insumo->sku)({{ $insumo->sku }})@endif
                                                    - {{ number_format($insumo->stockTotal(), 2) }} {{ $insumo->unidad_base }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                            Cantidad <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex">
                                            <input type="number" name="insumos[0][cantidad_necesaria]" 
                                                   step="0.001" min="0.001" placeholder="0.000"
                                                   class="cantidad-input flex-1 rounded-l-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   onchange="validarStock(this)">
                                            <span class="unidad-display px-3 py-3 bg-gray-100 dark:bg-gray-600 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-lg text-sm text-gray-700 dark:text-gray-300">
                                                unidad
                                            </span>
                                        </div>
                                        <div class="stock-warning mt-1 hidden text-sm text-red-600 dark:text-red-400">
                                            ⚠️ Stock insuficiente
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                            Tipo
                                        </label>
                                        <select name="insumos[0][es_obligatorio]" 
                                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="1">Obligatorio</option>
                                            <option value="0">Opcional</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                            Notas
                                        </label>
                                        <input type="text" name="insumos[0][notas]" 
                                               placeholder="Instrucciones especiales..." 
                                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" onclick="addInsumo()" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                            + Agregar Otro Insumo
                        </button>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                    <div class="flex gap-4">
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Protocolo
                        </button>
                        <a href="{{ route('protocolo-sanidad.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Cancelar
                        </a>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let insumoIndex = 1;
            const insumosDisponibles = {!! json_encode($insumos->map(function($insumo) {
                return [
                    'id' => $insumo->id,
                    'nombre' => $insumo->nombre,
                    'sku' => $insumo->sku,
                    'unidad_base' => $insumo->unidad_base,
                    'stock' => $insumo->stockTotal()
                ];
            })) !!};

            // Funciones para Actividades
            window.addActividad = function() {
                const container = document.getElementById('actividades-container');
                const div = document.createElement('div');
                div.className = 'flex gap-2 actividad-item';
                div.innerHTML = `
                    <input type="text" name="actividades[]" 
                           placeholder="Ej. Revisar nivel de pH del agua" 
                           class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="button" onclick="removeActividad(this)" 
                            class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                        ✕
                    </button>
                `;
                container.appendChild(div);
            }

            window.removeActividad = function(button) {
                const container = document.getElementById('actividades-container');
                if (container.children.length > 1) {
                    button.closest('.actividad-item').remove();
                }
            }

            // Funciones para Insumos
            window.toggleInsumos = function() {
                const checkbox = document.getElementById('requiere-insumos');
                const section = document.getElementById('insumos-section');
                section.classList.toggle('hidden', !checkbox.checked);
            }

            window.addInsumo = function() {
                const container = document.getElementById('insumos-container');
                const div = document.createElement('div');
                div.className = 'insumo-item bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg p-4';
                
                let opcionesInsumos = '<option value="">Seleccionar insumo...</option>';
                insumosDisponibles.forEach(function(insumo) {
                    const sku = insumo.sku ? `(${insumo.sku})` : '';
                    opcionesInsumos += `
                        <option value="${insumo.id}" data-unidad="${insumo.unidad_base}" data-stock="${insumo.stock}">
                            ${insumo.nombre} ${sku} - ${parseFloat(insumo.stock).toFixed(2)} ${insumo.unidad_base}
                        </option>`;
                });
                
                div.innerHTML = `
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-medium text-gray-800 dark:text-gray-200">Insumo #${insumoIndex + 1}</h4>
                        <button type="button" onclick="removeInsumo(this)" 
                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                            ✕ Eliminar
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Insumo <span class="text-red-500">*</span>
                            </label>
                            <select name="insumos[${insumoIndex}][inventario_item_id]" 
                                    class="insumo-select w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    onchange="updateInsumoInfo(this)">
                                ${opcionesInsumos}
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Cantidad <span class="text-red-500">*</span>
                            </label>
                            <div class="flex">
                                <input type="number" name="insumos[${insumoIndex}][cantidad_necesaria]" 
                                       step="0.001" min="0.001" placeholder="0.000"
                                       class="cantidad-input flex-1 rounded-l-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       onchange="validarStock(this)">
                                <span class="unidad-display px-3 py-3 bg-gray-100 dark:bg-gray-600 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-lg text-sm text-gray-700 dark:text-gray-300">
                                    unidad
                                </span>
                            </div>
                            <div class="stock-warning mt-1 hidden text-sm text-red-600 dark:text-red-400">
                                ⚠️ Stock insuficiente
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Tipo</label>
                            <select name="insumos[${insumoIndex}][es_obligatorio]" 
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="1">Obligatorio</option>
                                <option value="0">Opcional</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Notas</label>
                            <input type="text" name="insumos[${insumoIndex}][notas]" 
                                   placeholder="Instrucciones especiales..." 
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                `;
                
                container.appendChild(div);
                insumoIndex++;
            }

            window.removeInsumo = function(button) {
                const container = document.getElementById('insumos-container');
                if (container.children.length > 1) {
                    button.closest('.insumo-item').remove();
                }
            }

            window.updateInsumoInfo = function(select) {
                const selectedOption = select.options[select.selectedIndex];
                const unidad = selectedOption.dataset.unidad || 'unidad';
                const insumoItem = select.closest('.insumo-item');
                const unidadDisplay = insumoItem.querySelector('.unidad-display');
                
                if (unidadDisplay) {
                    unidadDisplay.textContent = unidad;
                }
            }

            window.validarStock = function(input) {
                const insumoItem = input.closest('.insumo-item');
                const select = insumoItem.querySelector('.insumo-select');
                const warningDiv = insumoItem.querySelector('.stock-warning');
                
                if (!select.value || !input.value) {
                    warningDiv.classList.add('hidden');
                    return;
                }
                
                const selectedOption = select.options[select.selectedIndex];
                const stockDisponible = parseFloat(selectedOption.dataset.stock) || 0;
                const cantidadNecesaria = parseFloat(input.value) || 0;
                
                warningDiv.classList.toggle('hidden', cantidadNecesaria <= stockDisponible);
            }
        });
    </script>
</x-app-layout>
