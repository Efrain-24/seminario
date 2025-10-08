<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Nueva Alimentación') }}
            </h2>
            <a href="{{ route('alimentacion.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <!-- Notificaciones flotantes -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('alimentacion.store') }}" class="space-y-6">
                        @csrf

                        <!-- Información Básica -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Información Básica</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Lote -->
                                <div>
                                    <label for="lote_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Lote <span class="text-red-500">*</span>
                                    </label>
                                    <select name="lote_id" id="lote_id" required
                                            class="block w-full dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm {{ $errors->has('lote_id') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500' }}">
                                        <option value="">Selecciona un lote</option>
                                        @foreach($lotes as $lote)
                                            <option value="{{ $lote->id }}" {{ old('lote_id') == $lote->id ? 'selected' : '' }}>
                                                {{ $lote->codigo_lote }} - {{ $lote->unidadProduccion->nombre }} ({{ $lote->especie }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lote_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <!-- Bodega -->
                                <div>
                                    <label for="bodega_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Bodega <span class="text-red-500">*</span>
                                    </label>
                                    <select name="bodega_id" id="bodega_id" required onchange="actualizarAlimentosPorBodega()"
                                            class="block w-full dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm {{ $errors->has('bodega_id') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500' }}">
                                        <option value="">Selecciona una bodega</option>
                                        @foreach($bodegas as $bodega)
                                            <option value="{{ $bodega->id }}" {{ old('bodega_id') == $bodega->id ? 'selected' : '' }}>
                                                {{ $bodega->nombre }} - {{ $bodega->ubicacion }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bodega_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Alimento del Inventario -->
                                <div>
                                    <label for="inventario_item_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Alimento del Inventario <span class="text-red-500">*</span>
                                    </label>
                                    <select name="inventario_item_id" id="inventario_item_id" required
                                            class="block w-full dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm {{ $errors->has('inventario_item_id') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500' }}">
                                        <option value="">Primero selecciona una bodega</option>
                                    </select>
                                    @error('inventario_item_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <!-- Fecha de Alimentación -->
                                <div>
                                    <label for="fecha_alimentacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Fecha de Alimentación <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="fecha_alimentacion" id="fecha_alimentacion" 
                                           value="{{ old('fecha_alimentacion', now()->format('Y-m-d')) }}" 
                                           max="{{ now()->format('Y-m-d') }}" required
                                           class="block w-full dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm {{ $errors->has('fecha_alimentacion') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500' }}">
                                    @error('fecha_alimentacion')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Hora de Alimentación -->
                                <div>
                                    <label for="hora_alimentacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Hora de Alimentación <span class="text-red-500">*</span>
                                    </label>
                                    <input type="time" name="hora_alimentacion" id="hora_alimentacion" 
                                           value="{{ old('hora_alimentacion', now()->format('H:i')) }}" required
                                           class="block w-full dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm {{ $errors->has('hora_alimentacion') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500' }}">
                                    @error('hora_alimentacion')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="hora_am_pm"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Cantidad y Método -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Cantidad y Método</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Cantidad -->
                                <div>
                                    <label for="cantidad_kg" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Cantidad de Alimento Suministrado (libras) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="cantidad_kg" id="cantidad_kg" step="0.01" min="0.01" 
                                           value="{{ old('cantidad_kg') }}" required
                                           class="block w-full dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm {{ $errors->has('cantidad_kg') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500' }}"
                                           placeholder="Ej: 5.5">
                                    @error('cantidad_kg')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Libras reales de alimento que se echaron al estanque</p>
                                    <!-- Costo estimado -->
                                    <div id="costo_estimado" class="text-sm text-gray-600 dark:text-gray-400 mt-1" style="display: none;">
                                        Costo estimado: <span id="costo_valor">Q0.00</span>
                                    </div>
                                </div>

                                <!-- Método de Alimentación -->
                                <div>
                                    <label for="metodo_alimentacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Método <span class="text-red-500">*</span>
                                    </label>
                                    <select name="metodo_alimentacion" id="metodo_alimentacion" required
                                            class="block w-full dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm {{ $errors->has('metodo_alimentacion') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500' }}">
                                        @foreach(App\Models\Alimentacion::getMetodosAlimentacion() as $key => $label)
                                            <option value="{{ $key }}" {{ old('metodo_alimentacion', 'manual') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('metodo_alimentacion')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones de los Peces -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Observaciones de los Peces</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Estado de los Peces -->
                                <div>
                                    <label for="estado_peces" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Estado de los Peces
                                    </label>
                                    <select name="estado_peces" id="estado_peces"
                                            class="block w-full dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm {{ $errors->has('estado_peces') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500' }}">
                                        <option value="">No especificado</option>
                                        @foreach(App\Models\Alimentacion::getEstadosPeces() as $key => $label)
                                            <option value="{{ $key }}" {{ old('estado_peces') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('estado_peces')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Porcentaje de Consumo -->
                                <div>
                                    <label for="porcentaje_consumo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Porcentaje de Consumo (%)
                                    </label>
                                    <input type="number" name="porcentaje_consumo" id="porcentaje_consumo" min="0" max="100" 
                                           value="{{ old('porcentaje_consumo') }}"
                                           class="block w-full dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm {{ $errors->has('porcentaje_consumo') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500' }}">
                                    @error('porcentaje_consumo')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Estimación de cuánto alimento consumieron los peces</p>
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones Generales -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Observaciones Generales</h3>
                            
                            <div>
                                <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Observaciones
                                </label>
                                <textarea name="observaciones" id="observaciones" rows="4" 
                                          class="block w-full dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm {{ $errors->has('observaciones') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500' }}"
                                          placeholder="Observaciones sobre el comportamiento de los peces, condiciones ambientales, incidencias, etc.">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex justify-end space-x-4 pt-6">
                            <a href="{{ route('alimentacion.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Guardar Alimentación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Datos de existencias por bodega del controlador
        const existenciasPorBodega = @json($existenciasPorBodega);
        
        // Función para actualizar tipos de alimento cuando se selecciona una bodega
        function actualizarAlimentosPorBodega() {
            const bodegaSelect = document.getElementById('bodega_id');
            const alimentoSelect = document.getElementById('inventario_item_id');
            const bodegaId = bodegaSelect.value;
            
            // Limpiar opciones actuales
            alimentoSelect.innerHTML = '<option value="">Selecciona un alimento del inventario</option>';
            
            if (bodegaId && existenciasPorBodega[bodegaId]) {
                // Agregar opciones para los alimentos disponibles en esta bodega
                existenciasPorBodega[bodegaId].forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item.inventario_item_id;
                    option.dataset.stockMinimo = item.stock_minimo;
                    option.dataset.unidad = item.unidad;
                    option.dataset.costoUnitario = item.costo_unitario;
                    option.dataset.moneda = item.moneda;
                    option.dataset.tieneCosto = item.tiene_costo;
                    
                    // Mostrar nombre con información del inventario
                    let texto = `${item.nombre_completo}`;
                    if (item.sku) {
                        texto += ` (${item.sku})`;
                    }
                    if (item.descripcion) {
                        texto += ` - ${item.descripcion}`;
                    }
                    texto += ` (${item.cantidad_disponible} ${item.unidad} disponible)`;
                    
                    // Mostrar costo si está disponible
                    if (item.tiene_costo && item.costo_unitario > 0) {
                        texto += ` - ${item.moneda} ${item.costo_unitario}/${item.unidad}`;
                    }
                    
                    // Alerta si está por debajo del stock mínimo
                    if (item.cantidad_disponible <= item.stock_minimo) {
                        texto += ' ⚠️ Stock bajo';
                    }
                    
                    option.textContent = texto;
                    alimentoSelect.appendChild(option);
                });
            }
            
            // Limpiar campos dependientes cuando cambia la bodega
            document.getElementById('cantidad_kg').value = '';
            const costoEstimadoDiv = document.getElementById('costo_estimado');
            if (costoEstimadoDiv) {
                costoEstimadoDiv.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const inventarioItemSelect = document.getElementById('inventario_item_id');
            const cantidadInput = document.getElementById('cantidad_kg');
            const costoEstimadoDiv = document.getElementById('costo_estimado');
            const costoValorSpan = document.getElementById('costo_valor');

            function validarCantidad() {
                const selectedOption = inventarioItemSelect.options[inventarioItemSelect.selectedIndex];
                const stockMinimo = parseFloat(selectedOption.getAttribute('data-stock-minimo')) || 0;
                const unidad = selectedOption.getAttribute('data-unidad') || 'kg';
                const cantidad = parseFloat(cantidadInput.value) || 0;

                // Obtener cantidad disponible del texto de la opción
                const textoOpcion = selectedOption.textContent;
                const match = textoOpcion.match(/\((\d+(?:\.\d+)?)\s+\w+\s+disponible\)/);
                const cantidadDisponible = match ? parseFloat(match[1]) : 0;

                // Validar que no exceda el stock disponible
                if (cantidad > cantidadDisponible) {
                    alert(`La cantidad solicitada (${cantidad} ${unidad}) excede el stock disponible (${cantidadDisponible} ${unidad})`);
                    cantidadInput.value = cantidadDisponible;
                    return false;
                }

                // Advertir si quedará por debajo del stock mínimo
                const stockRestante = cantidadDisponible - cantidad;
                if (stockRestante < stockMinimo) {
                    const confirmar = confirm(`Advertencia: Después de esta alimentación quedarán ${stockRestante} ${unidad}, por debajo del stock mínimo de ${stockMinimo} ${unidad}. ¿Desea continuar?`);
                    if (!confirmar) {
                        cantidadInput.value = '';
                        return false;
                    }
                }

                return true;
            }

            // Función para calcular costo estimado
            function calcularCostoEstimado() {
                const selectedOption = inventarioItemSelect.options[inventarioItemSelect.selectedIndex];
                const cantidad = parseFloat(cantidadInput.value) || 0;
                const costoEstimadoDiv = document.getElementById('costo_estimado');
                const costoValorSpan = document.getElementById('costo_valor');
                
                if (selectedOption.value && cantidad > 0) {
                    const costoUnitario = parseFloat(selectedOption.getAttribute('data-costo-unitario')) || 0;
                    const moneda = selectedOption.getAttribute('data-moneda') || 'GTQ';
                    const tieneCosto = selectedOption.getAttribute('data-tiene-costo') === 'true';
                    
                    if (tieneCosto && costoUnitario > 0) {
                        const costoTotal = costoUnitario * cantidad;
                        costoValorSpan.textContent = `${moneda} ${costoTotal.toFixed(2)}`;
                        costoEstimadoDiv.style.display = 'block';
                    } else {
                        costoValorSpan.textContent = '';
                        costoEstimadoDiv.style.display = 'none';
                    }
                } else {
                    costoEstimadoDiv.style.display = 'none';
                }
            }

            // Función para mostrar información del item seleccionado
            function mostrarInfoItem() {
                const selectedOption = inventarioItemSelect.options[inventarioItemSelect.selectedIndex];
                if (selectedOption.value) {
                    const unidad = selectedOption.getAttribute('data-unidad') || 'kg';
                    const stockMinimo = selectedOption.getAttribute('data-stock-minimo') || 0;
                    const costoUnitario = selectedOption.getAttribute('data-costo-unitario') || 0;
                    console.log(`Item seleccionado - Unidad: ${unidad}, Stock mínimo: ${stockMinimo}, Costo: ${costoUnitario}`);
                    calcularCostoEstimado();
                }
            }

            inventarioItemSelect.addEventListener('change', mostrarInfoItem);
            cantidadInput.addEventListener('input', function() {
                validarCantidad();
                calcularCostoEstimado();
            });

            // Mostrar AM/PM en la hora
            const horaInput = document.getElementById('hora_alimentacion');
            const horaAmPm = document.getElementById('hora_am_pm');
            function mostrarAmPm() {
                if (horaInput.value) {
                    let [h, m] = horaInput.value.split(':');
                    h = parseInt(h);
                    let ampm = h >= 12 ? 'PM' : 'AM';
                    let hora12 = h % 12 || 12;
                    horaAmPm.textContent = `Hora seleccionada: ${hora12}:${m} ${ampm}`;
                } else {
                    horaAmPm.textContent = '';
                }
            }
            horaInput.addEventListener('input', mostrarAmPm);
            mostrarAmPm();
        });
    </script>
</x-app-layout>
