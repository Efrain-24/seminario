{{-- Formulario parcial para crear/editar cosechas --}}
<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Lote</label>
        <select name="lote_id"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                       text-gray-900 dark:text-gray-100 p-2"
            {{ $cosecha ? 'disabled' : '' }} required>
            <option value="">{{ __('Seleccione…') }}</option>
            @foreach ($lotes as $l)
                <option value="{{ $l->id }}" @selected(old('lote_id', optional($cosecha)->lote_id) == $l->id)>
                    {{ $l->nombre ?? ($l->codigo_lote ?? 'Lote #' . $l->id) }}
                    ({{ __('stock') }}: {{ $l->cantidad_actual ?? 0 }})
                </option>
            @endforeach
        </select>
        @if ($cosecha)
            <input type="hidden" name="lote_id" value="{{ $cosecha->lote_id }}">
        @endif
        @error('lote_id')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Fecha</label>
        <input type="date" name="fecha"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                      text-gray-900 dark:text-gray-100 p-2"
            value="{{ old('fecha', optional(optional($cosecha)->fecha)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
            required>
        @error('fecha')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Cantidad cosechada (peces)</label>
        <input type="number" min="1" name="cantidad_cosechada"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                      text-gray-900 dark:text-gray-100 p-2"
            value="{{ old('cantidad_cosechada', optional($cosecha)->cantidad_cosechada) }}" required>
        @error('cantidad_cosechada')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Peso total (kg) (opcional)</label>
        <input type="number" step="0.01" min="0" name="peso_cosechado_kg"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                      text-gray-900 dark:text-gray-100 p-2"
            value="{{ old('peso_cosechado_kg', optional($cosecha)->peso_cosechado_kg) }}">
        @error('peso_cosechado_kg')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Destino</label>
        <select name="destino" id="destino"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                       text-gray-900 dark:text-gray-100 p-2"
            onchange="toggleVentaFields()"
            required>
            <option value="">Seleccione destino...</option>
            @foreach (['venta', 'muestra', 'otro'] as $opt)
                <option value="{{ $opt }}" @selected(old('destino', optional($cosecha)->destino) == $opt)>{{ ucfirst($opt) }}</option>
            @endforeach
        </select>
        @error('destino')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Responsable</label>
        <input type="text" name="responsable"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700
                      text-gray-900 dark:text-gray-100 p-2"
            value="{{ auth()->user()->name ?? 'Sistema' }}"
            readonly>
        @error('responsable')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Observaciones</label>
        <textarea name="observaciones" rows="3"
            class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                         text-gray-900 dark:text-gray-100 p-2">{{ old('observaciones', optional($cosecha)->observaciones) }}</textarea>
        @error('observaciones')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Campos de Venta (se muestran solo si destino es 'venta') -->
    <div id="campos-venta" class="md:col-span-2 space-y-4" style="display: none;">
        <div class="border-t pt-4 mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">💰 Información de Venta</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Cliente -->
                <div>
                    <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Cliente *</label>
                    <input type="text" name="cliente" id="cliente"
                        value="{{ old('cliente', optional($cosecha)->cliente) }}"
                        placeholder="Nombre del cliente"
                        class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                               text-gray-900 dark:text-gray-100 p-2">
                    @error('cliente')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teléfono Cliente (Opcional) -->
                <div>
                    <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Teléfono (opcional)</label>
                    <input type="text" name="telefono_cliente"
                        value="{{ old('telefono_cliente', optional($cosecha)->telefono_cliente) }}"
                        placeholder="8888-8888"
                        class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                               text-gray-900 dark:text-gray-100 p-2">
                    @error('telefono_cliente')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unidad de venta -->
                <div>
                    <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Vender por *</label>
                    <select name="unidad_venta" id="unidad_venta" 
                        onchange="calcularTotales()"
                        class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                               text-gray-900 dark:text-gray-100 p-2">
                        <option value="libra" @selected(old('unidad_venta', 'libra') == 'libra')>Por Libra</option>
                        <option value="pez" @selected(old('unidad_venta') == 'pez')>Por Pez</option>
                    </select>
                    @error('unidad_venta')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Precio -->
                <div>
                    <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">
                        <span id="precio_label">Precio por libra (Q) *</span>
                    </label>
                    <input type="number" name="precio_unitario" id="precio_unitario" step="0.01" min="0"
                        value="{{ old('precio_unitario', optional($cosecha)->precio_kg) }}"
                        placeholder="0.00"
                        oninput="calcularTotales()"
                        class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                               text-gray-900 dark:text-gray-100 p-2">
                    @error('precio_unitario')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total automático -->
                <div class="md:col-span-2">
                    <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">💰 Total de la Venta</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input type="text" id="total_preview" readonly
                                class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700
                                       text-green-600 dark:text-green-400 p-2 font-bold text-lg"
                                placeholder="Q 0.00">
                            <small class="text-gray-500" id="calculo_detalle">Se calculará automáticamente</small>
                        </div>
                        <div>
                            <input type="text" id="total_usd_preview" readonly
                                class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700
                                       text-blue-600 dark:text-blue-400 p-2 font-bold text-lg"
                                placeholder="$ 0.00 USD">
                            <small class="text-gray-500">Equivalente en USD</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tipo de cambio actual para Guatemala (GTQ a USD)
    const tipoCambio = {{ $tipoCambio ?? 7.8 }};

    function toggleVentaFields() {
        const destino = document.getElementById('destino');
        const camposVenta = document.getElementById('campos-venta');
        const cliente = document.getElementById('cliente');
        const precioUnitario = document.getElementById('precio_unitario');
        
        if (!destino || !camposVenta) return;
        
        if (destino.value === 'venta') {
            camposVenta.style.display = 'block';
            // Hacer campos requeridos
            if (cliente) cliente.required = true;
            if (precioUnitario) precioUnitario.required = true;
        } else {
            camposVenta.style.display = 'none';
            // Quitar required
            if (cliente) cliente.required = false;
            if (precioUnitario) precioUnitario.required = false;
        }
    }

    function calcularTotales() {
        const pesoKgField = document.querySelector('input[name="peso_cosechado_kg"]');
        const cantidadPecesField = document.querySelector('input[name="cantidad_cosechada"]');
        const precioUnitarioField = document.getElementById('precio_unitario');
        const unidadVentaField = document.getElementById('unidad_venta');
        const precioLabel = document.getElementById('precio_label');
        const totalPreview = document.getElementById('total_preview');
        const totalUsdPreview = document.getElementById('total_usd_preview');
        const calculoDetalle = document.getElementById('calculo_detalle');
        
        if (!pesoKgField || !cantidadPecesField || !precioUnitarioField) return;
        
        try {
            const pesoKg = parseFloat(pesoKgField.value) || 0;
            const cantidadPeces = parseFloat(cantidadPecesField.value) || 0;
            const precioUnitario = parseFloat(precioUnitarioField.value) || 0;
            const unidadVenta = unidadVentaField ? unidadVentaField.value : 'libra';
            
            // Actualizar etiqueta del precio
            if (precioLabel) {
                if (unidadVenta === 'libra') {
                    precioLabel.textContent = 'Precio por libra (Q) *';
                } else {
                    precioLabel.textContent = 'Precio por pez (Q) *';
                }
            }
            
            let total = 0;
            let detalle = '';
            
            if (precioUnitario > 0) {
                if (unidadVenta === 'libra') {
                    // Convertir kg a libras (1 kg = 2.20462 libras)
                    const pesoLibras = pesoKg * 2.20462;
                    total = pesoLibras * precioUnitario;
                    detalle = `${pesoLibras.toFixed(2)} lbs × Q ${precioUnitario.toFixed(2)}`;
                } else {
                    // Venta por pez
                    total = cantidadPeces * precioUnitario;
                    detalle = `${cantidadPeces} peces × Q ${precioUnitario.toFixed(2)}`;
                }
            }
            
            // Mostrar totales
            if (totalPreview) {
                totalPreview.value = total > 0 ? `Q ${total.toFixed(2)}` : '';
            }
            
            if (totalUsdPreview) {
                const totalUsd = total / tipoCambio;
                totalUsdPreview.value = total > 0 ? `$${totalUsd.toFixed(2)} USD` : '';
            }
            
            if (calculoDetalle) {
                calculoDetalle.textContent = detalle || 'Se calculará automáticamente';
            }
        } catch (error) {
            console.error('Error en cálculos:', error);
        }
    }

    // Ejecutar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        try {
            toggleVentaFields();
            calcularTotales();
            
            // Agregar eventos
            const destino = document.getElementById('destino');
            const pesoField = document.querySelector('input[name="peso_cosechado_kg"]');
            const cantidadField = document.querySelector('input[name="cantidad_cosechada"]');
            const unidadField = document.getElementById('unidad_venta');
            const precioField = document.getElementById('precio_unitario');
            
            if (destino) destino.addEventListener('change', toggleVentaFields);
            if (pesoField) pesoField.addEventListener('input', calcularTotales);
            if (cantidadField) cantidadField.addEventListener('input', calcularTotales);
            if (unidadField) unidadField.addEventListener('change', calcularTotales);
            if (precioField) precioField.addEventListener('input', calcularTotales);
        } catch (error) {
            console.error('Error al inicializar formulario:', error);
        }
    });
</script>
</script>
