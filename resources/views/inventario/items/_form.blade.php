@php($it = $item ?? null)

{{-- Información básica --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="space-y-2">
        <label for="nombre" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Nombre del artículo <span class="text-red-500">*</span>
        </label>
        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $it->nombre ?? '') }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
            placeholder="Ej: Alimento Concentrado Premium" required>
        @error('nombre')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="sku" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
            </svg>
            SKU / Código
            <span class="ml-2 text-xs text-green-600 dark:text-green-400">(Generado automáticamente)</span>
        </label>
        <input type="text" name="sku" id="sku" value="{{ old('sku', $it->sku ?? '') }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-100"
            placeholder="Se generará automáticamente" readonly>
        @error('sku')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Código único generado automáticamente basado en el tipo y nombre del artículo
        </p>
    </div>
</div>

{{-- Categorización --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="space-y-2">
        <label for="tipo" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Categoría <span class="text-red-500">*</span>
        </label>
        <select name="tipo" id="tipo" onchange="updateTipoInfo()"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
            required>
            <option value="">Seleccionar categoría...</option>
            <option value="alimento" {{ old('tipo', $it->tipo ?? '') == 'alimento' ? 'selected' : '' }}>
                Alimento
            </option>
            <option value="insumo" {{ old('tipo', $it->tipo ?? '') == 'insumo' ? 'selected' : '' }}>
                Insumo / Suministro
            </option>
        </select>
        @error('tipo')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
        
        {{-- Info de la categoría seleccionada --}}
        <div id="tipo-info" class="hidden mt-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div id="tipo-description" class="text-sm text-gray-600 dark:text-gray-300"></div>
        </div>
    </div>
    
    <div class="space-y-2">
        <label for="unidad_base" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
            </svg>
            Unidad de medida <span class="text-red-500">*</span>
        </label>
        <select name="unidad_base" id="unidad_base" onchange="updateUnidadInfo()"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
            required>
            <option value="">Seleccionar unidad...</option>
            <option value="kg" {{ old('unidad_base', $it->unidad_base ?? '') == 'kg' ? 'selected' : '' }}>
                Kilogramos (kg)
            </option>
            <option value="lb" {{ old('unidad_base', $it->unidad_base ?? '') == 'lb' ? 'selected' : '' }}>
                Libras (lb)
            </option>
            <option value="unidad" {{ old('unidad_base', $it->unidad_base ?? '') == 'unidad' ? 'selected' : '' }}>
                Unidades (piezas)
            </option>
            <option value="litro" {{ old('unidad_base', $it->unidad_base ?? '') == 'litro' ? 'selected' : '' }}>
                Litros (l)
            </option>
        </select>
        @error('unidad_base')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
        
        {{-- Info de la unidad seleccionada --}}
        <div id="unidad-info" class="hidden mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
            <div id="unidad-description" class="text-sm text-blue-700 dark:text-blue-300"></div>
        </div>
    </div>
</div>

{{-- Control de stock --}}
<div class="space-y-2">
    <label for="stock_minimo" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Stock mínimo
    </label>
    <div class="flex items-center space-x-4">
        <input type="number" step="0.001" min="0" name="stock_minimo" id="stock_minimo"
            value="{{ old('stock_minimo', $it->stock_minimo ?? 0) }}"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
            placeholder="0.000">
        <span id="unidad-display" class="text-sm text-gray-500 dark:text-gray-400 min-w-0">unidades</span>
    </div>
    @error('stock_minimo')
        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
    <p class="text-xs text-gray-500 dark:text-gray-400">
        Cantidad mínima antes de generar alertas de stock bajo
    </p>
</div>

{{-- Información de costos --}}
<div class="border-t border-gray-200 dark:border-gray-600 pt-6 mt-6">
    <h3 class="flex items-center text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
        </svg>
        Información de Costos
    </h3>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label for="costo_unitario" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Costo unitario
            </label>
            <div class="flex items-center space-x-2">
                <select name="moneda" id="moneda"
                    class="px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    <option value="GTQ" {{ old('moneda', $it->moneda ?? 'GTQ') == 'GTQ' ? 'selected' : '' }}>GTQ</option>
                    <option value="USD" {{ old('moneda', $it->moneda ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ old('moneda', $it->moneda ?? '') == 'EUR' ? 'selected' : '' }}>EUR</option>
                </select>
                <input type="number" step="0.01" min="0" name="costo_unitario" id="costo_unitario"
                    value="{{ old('costo_unitario', $it->costo_unitario ?? '') }}"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                    placeholder="0.00">
                <span id="costo-unidad-display" class="text-sm text-gray-500 dark:text-gray-400 min-w-0">/ kg</span>
            </div>
            @error('costo_unitario')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Costo por unidad base (kg, litro, etc.)
            </p>
        </div>
        
        <div class="space-y-2">
            <label for="fecha_ultimo_costo" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Fecha del costo
            </label>
            <input type="date" name="fecha_ultimo_costo" id="fecha_ultimo_costo"
                value="{{ old('fecha_ultimo_costo', $it->fecha_ultimo_costo ?? '') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
            @error('fecha_ultimo_costo')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Fecha de actualización del costo
            </p>
        </div>
    </div>
    
    {{-- Rangos de costo (solo lectura para mostrar histórico) --}}
    @if($it && ($it->costo_minimo || $it->costo_maximo))
    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rango histórico de costos</h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600 dark:text-gray-400">Costo mínimo:</span>
                <span class="font-medium text-green-600 dark:text-green-400">
                    {{ $it->moneda }} {{ number_format($it->costo_minimo, 2) }}
                </span>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Costo máximo:</span>
                <span class="font-medium text-red-600 dark:text-red-400">
                    {{ $it->moneda }} {{ number_format($it->costo_maximo, 2) }}
                </span>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Descripción --}}
<div class="space-y-2">
    <label for="descripcion" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Descripción
    </label>
    <textarea name="descripcion" id="descripcion" rows="3"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
        placeholder="Descripción detallada del artículo, características técnicas, uso recomendado, etc.">{{ old('descripcion', $it->descripcion ?? '') }}</textarea>
    @error('descripcion')
        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

<script>
function updateTipoInfo() {
    const select = document.getElementById('tipo');
    const infoDiv = document.getElementById('tipo-info');
    const descDiv = document.getElementById('tipo-description');
    
    if (select.value) {
        let description = '';
        if (select.value === 'alimento') {
            description = '<strong>Alimentos:</strong> Concentrados, pellets, suplementos nutricionales para peces.';
        } else if (select.value === 'insumo') {
            description = '<strong>Insumos:</strong> Equipos, herramientas, productos químicos, materiales de construcción.';
        }
        
        descDiv.innerHTML = description;
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
}

function updateUnidadInfo() {
    const select = document.getElementById('unidad_base');
    const infoDiv = document.getElementById('unidad-info');
    const descDiv = document.getElementById('unidad-description');
    const displaySpan = document.getElementById('unidad-display');
    const costoUnidadDisplay = document.getElementById('costo-unidad-display');
    
    if (select.value) {
        let description = '';
        let displayText = select.value;
        
        if (select.value === 'kg') {
            description = 'Sistema admite conversión automática entre kg y lb';
            displayText = 'kg';
        } else if (select.value === 'lb') {
            description = 'Sistema admite conversión automática entre lb y kg';
            displayText = 'lb';
        } else if (select.value === 'unidad') {
            description = 'Para productos que se cuentan por piezas (equipos, herramientas, etc.)';
            displayText = 'unidades';
        } else if (select.value === 'litro') {
            description = '🧪 Para productos líquidos (desinfectantes, aditivos, combustibles)';
            displayText = 'litros';
        }
        
        descDiv.textContent = description;
        displaySpan.textContent = displayText;
        
        // Actualizar también el display de la unidad en los costos
        if (costoUnidadDisplay) {
            costoUnidadDisplay.textContent = '/ ' + displayText;
        }
        
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
        displaySpan.textContent = 'unidades';
        if (costoUnidadDisplay) {
            costoUnidadDisplay.textContent = '/ unidades';
        }
    }
}

function generateSKU() {
    const nombreInput = document.getElementById('nombre');
    const tipoSelect = document.getElementById('tipo');
    const skuInput = document.getElementById('sku');
    
    if (!nombreInput.value.trim() || !tipoSelect.value) {
        skuInput.value = '';
        return;
    }
    
    // Prefijo según el tipo
    let prefijo = '';
    switch(tipoSelect.value) {
        case 'alimento':
            prefijo = 'ALM';
            break;
        case 'insumo':
            prefijo = 'INS';
            break;
        case 'medicamento':
            prefijo = 'MED';
            break;
        case 'equipo':
            prefijo = 'EQP';
            break;
        default:
            prefijo = 'ITM';
    }
    
    // Tomar las primeras palabras del nombre y crear código
    const palabras = nombreInput.value.trim().split(' ');
    let sufijo = '';
    
    if (palabras.length === 1) {
        // Una sola palabra, tomar primeras 4 letras
        sufijo = palabras[0].substring(0, 4).toUpperCase();
    } else if (palabras.length === 2) {
        // Dos palabras, tomar primeras 2 letras de cada una
        sufijo = palabras[0].substring(0, 2).toUpperCase() + palabras[1].substring(0, 2).toUpperCase();
    } else {
        // Más de dos palabras, tomar primera letra de las primeras 4 palabras
        sufijo = palabras.slice(0, 4).map(p => p.charAt(0).toUpperCase()).join('');
    }
    
    // Generar número aleatorio de 3 dígitos
    const numero = Math.floor(Math.random() * 900) + 100;
    
    // Generar SKU final
    const sku = `${prefijo}-${sufijo}-${numero}`;
    skuInput.value = sku;
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateTipoInfo();
    updateUnidadInfo();
    
    // Generar SKU cuando cambie el nombre o tipo
    const nombreInput = document.getElementById('nombre');
    const tipoSelect = document.getElementById('tipo');
    const unidadSelect = document.getElementById('unidad_base');
    
    if (nombreInput) {
        nombreInput.addEventListener('input', generateSKU);
    }
    
    if (tipoSelect) {
        tipoSelect.addEventListener('change', function() {
            updateTipoInfo();
            generateSKU();
        });
    }
    
    if (unidadSelect) {
        unidadSelect.addEventListener('change', updateUnidadInfo);
    }
});
</script>
