<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                Editar Registro de Limpieza
                @if($limpieza->protocoloSanidad)
                    <span class="text-sm font-normal text-gray-600 dark:text-gray-400">
                        - {{ $limpieza->protocoloSanidad->nombre }}
                    </span>
                @endif
            </h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                ID: {{ $limpieza->id }} | Estado: 
                <span class="font-medium {{ $limpieza->estado === 'completado' ? 'text-green-600' : ($limpieza->estado === 'en_progreso' ? 'text-yellow-600' : 'text-gray-600') }}">
                    {{ ucfirst(str_replace('_', ' ', $limpieza->estado)) }}
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-8 max-w-6xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
            <form action="{{ route('limpieza.update', $limpieza) }}" method="POST" class="space-y-6" id="form-limpieza">
                @csrf
                @method('PUT')
                
                <!-- Información básica en grid de 2 columnas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Fecha</label>
                        <input type="date" name="fecha" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3" value="{{ $limpieza->fecha }}" required>
                    </div>
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Estado</label>
                        <select name="estado" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3" required>
                            <option value="no_ejecutado" @if($limpieza->estado == 'no_ejecutado') selected @endif>No Ejecutado</option>
                            <option value="en_progreso" @if($limpieza->estado == 'en_progreso') selected @endif>En Progreso</option>
                            <option value="completado" @if($limpieza->estado == 'completado') selected @endif>Completado</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Área</label>
                    <select name="area" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3" required>
                        <option value="">Seleccione un área...</option>
                        
                        <!-- Unidades de Producción -->
                        @if($unidades->count() > 0)
                            <optgroup label="Unidades de Producción">
                                @foreach($unidades as $unidad)
                                    @php $valorUnidad = "Unidad: " . $unidad->codigo; @endphp
                                    <option value="{{ $valorUnidad }}" {{ $limpieza->area == $valorUnidad ? 'selected' : '' }}>
                                        {{ $unidad->codigo }} - {{ ucfirst(str_replace('_', ' ', $unidad->tipo)) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                        
                        <!-- Bodegas -->
                        @if($bodegas->count() > 0)
                            <optgroup label="Bodegas">
                                @foreach($bodegas as $bodega)
                                    @php $valorBodega = "Bodega: " . $bodega->nombre; @endphp
                                    <option value="{{ $valorBodega }}" {{ $limpieza->area == $valorBodega ? 'selected' : '' }}>
                                        {{ $bodega->nombre }} - {{ $bodega->ubicacion }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                        
                        <!-- Opción para áreas personalizadas (compatibilidad con datos existentes) -->
                        @if($limpieza->area && !str_starts_with($limpieza->area, 'Unidad:') && !str_starts_with($limpieza->area, 'Bodega:'))
                            <optgroup label="Otras Áreas">
                                <option value="{{ $limpieza->area }}" selected>{{ $limpieza->area }} (Área personalizada)</option>
                            </optgroup>
                        @endif
                    </select>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="responsable" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Responsable</label>
                        <select name="responsable" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3" required>
                            <option value="">Seleccione...</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->name }}" @if($limpieza->responsable == $usuario->name) selected @endif>{{ $usuario->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="protocolo_sanidad_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Protocolo de Sanidad</label>
                        <select name="protocolo_sanidad_id" id="protocolo_sanidad_id" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3" required onchange="cargarActividades()">
                            @foreach($protocolos as $protocolo)
                                <option value="{{ $protocolo->id }}" @if($limpieza->protocolo_sanidad_id == $protocolo->id) selected @endif>{{ $protocolo->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Checklist de Actividades -->
                <div id="actividades-section" class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <label class="block text-lg font-medium text-gray-700 dark:text-gray-200 mb-4">Checklist de Actividades</label>
                    <div id="actividades-container" class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 p-4">
                        @if($limpieza->actividades_ejecutadas && count($limpieza->actividades_ejecutadas) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600">Actividad</th>
                                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600 w-24">Completada</th>
                                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600 w-1/3">Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($limpieza->actividades_ejecutadas as $index => $actividad)
                                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                    <label for="actividad_{{ $index }}" class="cursor-pointer">
                                                        {{ $actividad['descripcion'] ?? '' }}
                                                    </label>
                                                    <input type="hidden" 
                                                           name="actividades_ejecutadas[{{ $index }}][descripcion]" 
                                                           value="{{ $actividad['descripcion'] ?? '' }}">
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <input type="checkbox" 
                                                           name="actividades_ejecutadas[{{ $index }}][completada]" 
                                                           value="1"
                                                           id="actividad_{{ $index }}"
                                                           {{ isset($actividad['completada']) && $actividad['completada'] ? 'checked' : '' }}
                                                           class="rounded border-gray-300 dark:border-gray-600 text-green-600 focus:ring-green-500">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <textarea name="actividades_ejecutadas[{{ $index }}][observaciones]" 
                                                              class="w-full text-xs rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 p-2" 
                                                              rows="2" 
                                                              placeholder="Observaciones específicas (opcional)">{{ $actividad['observaciones'] ?? '' }}</textarea>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No hay actividades registradas para esta limpieza.</p>
                        @endif
                    </div>
                </div>

                <!-- Sección de Insumos (se muestra automáticamente si el protocolo tiene insumos) -->
                @if($insumosProtocolo->isNotEmpty())
                    <div id="insumos-section" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            Insumos Requeridos
                            <span class="text-xs bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full">
                                {{ $insumosProtocolo->count() }} insumos
                            </span>
                        </h3>
                        
                        <div class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-yellow-400 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-200 font-medium">
                                        Al completar esta limpieza, registre las cantidades reales de insumos utilizados
                                    </p>
                                    <p class="text-xs text-yellow-600 dark:text-yellow-300 mt-1">
                                        Las cantidades se descontarán automáticamente del inventario
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Insumo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad Planificada</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock Disponible</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad Real Utilizada</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Costo Estimado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($insumosProtocolo as $index => $insumo)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                <div class="flex items-center gap-2">
                                                    <strong>{{ $insumo['nombre'] }}</strong>
                                                    @if($insumo['es_obligatorio'])
                                                        <span class="text-xs bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 px-1 py-0.5 rounded">Obligatorio</span>
                                                    @endif
                                                </div>
                                                <small class="text-gray-500">{{ $insumo['unidad'] }}</small>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                {{ number_format($insumo['cantidad_planificada'], 2) }} {{ $insumo['unidad'] }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span class="{{ $insumo['stock_disponible'] >= $insumo['cantidad_planificada'] ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ number_format($insumo['stock_disponible'], 2) }} {{ $insumo['unidad'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <input type="hidden" name="insumos_reales[{{ $index }}][inventario_item_id]" value="{{ $insumo['id'] }}">
                                                <input type="hidden" name="insumos_reales[{{ $index }}][cantidad_planificada]" value="{{ $insumo['cantidad_planificada'] }}">
                                                
                                                <input type="number" 
                                                       name="insumos_reales[{{ $index }}][cantidad_real]" 
                                                       value="{{ $insumo['cantidad_planificada'] }}"
                                                       step="0.001" 
                                                       min="0" 
                                                       max="{{ $insumo['stock_disponible'] }}"
                                                       class="w-28 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2 text-sm"
                                                       onchange="calcularCosto({{ $index }}, {{ $insumo['costo_unitario'] }})"
                                                       {{ $limpieza->estado === 'completado' ? 'disabled' : '' }}>
                                                <span class="text-xs text-gray-500 ml-1">{{ $insumo['unidad'] }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                <span id="costo-{{ $index }}">Q{{ number_format($insumo['cantidad_planificada'] * $insumo['costo_unitario'], 2) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <td colspan="4" class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 text-right">
                                            Costo Total Estimado:
                                        </td>
                                        <td class="px-6 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">
                                            <span id="costo-total">Q{{ number_format($insumosProtocolo->sum(fn($i) => $i['cantidad_planificada'] * $i['costo_unitario']), 2) }}</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Observaciones</label>
                    <textarea name="observaciones" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3" rows="4" placeholder="Observaciones generales sobre la limpieza">{{ $limpieza->observaciones }}</textarea>
                </div>

                <div class="flex flex-wrap gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="px-6 py-3 rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium transition-colors">Actualizar Limpieza</button>
                    <a href="{{ route('limpieza.index') }}" class="px-6 py-3 rounded-lg bg-gray-500 hover:bg-gray-600 text-white font-medium transition-colors">Cancelar</a>
                    <a href="{{ route('protocolo-sanidad.index') }}" class="px-6 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Ver Protocolos
                    </a>
                </div>

                <script>
                    function calcularCosto(index, costoUnitario) {
                        const cantidadInput = document.querySelector(`input[name="insumos_reales[${index}][cantidad_real]"]`);
                        const cantidad = parseFloat(cantidadInput.value) || 0;
                        const costo = cantidad * costoUnitario;
                        
                        document.getElementById(`costo-${index}`).textContent = `Q${costo.toFixed(2)}`;
                        
                        // Recalcular costo total
                        calcularCostoTotal();
                    }
                    
                    function calcularCostoTotal() {
                        let total = 0;
                        @if($insumosProtocolo->isNotEmpty())
                            @foreach($insumosProtocolo as $index => $insumo)
                                const cantidad{{ $index }} = parseFloat(document.querySelector(`input[name="insumos_reales[{{ $index }}][cantidad_real]"]`)?.value) || 0;
                                total += cantidad{{ $index }} * {{ $insumo['costo_unitario'] }};
                            @endforeach
                        @endif
                        
                        const costoTotalElement = document.getElementById('costo-total');
                        if (costoTotalElement) {
                            costoTotalElement.textContent = `Q${total.toFixed(2)}`;
                        }
                    }

                    async function cargarActividades() {
                        const protocoloId = document.getElementById('protocolo_sanidad_id').value;
                        const actividadesContainer = document.getElementById('actividades-container');
                        const insumosSection = document.getElementById('insumos-section');

                        if (!protocoloId) {
                            actividadesContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm">Seleccione un protocolo para ver las actividades.</p>';
                            if (insumosSection) {
                                insumosSection.style.display = 'none';
                            }
                            return;
                        }

                        try {
                            const response = await fetch(`/limpieza/protocolo/${protocoloId}/actividades`);
                            const data = await response.json();

                            if (data.actividades && data.actividades.length > 0) {
                                if (data.actividades.length === 1 && typeof data.actividades[0] === 'string') {
                                    let raw = data.actividades[0];
                                    let prelim = raw.split(/[\r\n;|]+/).filter(a=>a.trim()!== '');
                                    if (prelim.length === 1) {
                                        const numSplit = raw.split(/\s*\d+\s*[).:-]\s+/).filter(a=>a.trim()!=='');
                                        if (numSplit.length > 1) prelim = numSplit;
                                    }
                                    if (prelim.length === 1 && prelim[0].includes(',')) {
                                        const comaParts = prelim[0].split(',').map(p=>p.trim()).filter(p=>p!=='');
                                        if (comaParts.length > 1) prelim = comaParts;
                                    }
                                    data.actividades = prelim.map(a=>a.trim()).filter(a=>a.length>0);
                                }
                                actividadesContainer.innerHTML = `
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600">Actividad</th>
                                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600 w-24">Completada</th>
                                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600 w-1/3">Observaciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                `;
                                
                                const tbody = actividadesContainer.querySelector('tbody');
                                data.actividades.forEach((actividad, index) => {
                                    const row = document.createElement('tr');
                                    row.className = 'border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700';
                                    row.innerHTML = `
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            <label for="actividad_${index}" class="cursor-pointer">
                                                ${actividad}
                                            </label>
                                            <input type="hidden" 
                                                   name="actividades_ejecutadas[${index}][descripcion]" 
                                                   value="${actividad}">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="checkbox" 
                                                   name="actividades_ejecutadas[${index}][completada]" 
                                                   value="1"
                                                   id="actividad_${index}"
                                                   class="rounded border-gray-300 dark:border-gray-600 text-green-600 focus:ring-green-500">
                                        </td>
                                        <td class="px-4 py-3">
                                            <textarea name="actividades_ejecutadas[${index}][observaciones]" 
                                                      class="w-full text-xs rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 p-2" 
                                                      rows="2" 
                                                      placeholder="Observaciones específicas (opcional)"></textarea>
                                        </td>
                                    `;
                                    tbody.appendChild(row);
                                });
                            } else {
                                actividadesContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm">Este protocolo no tiene actividades definidas.</p>';
                            }

                            // Cargar insumos del protocolo usando la función específica
                            cargarInsumosProtocolo(protocoloId);
                        } catch (error) {
                            console.error('Error al cargar actividades:', error);
                            actividadesContainer.innerHTML = '<p class="text-red-500 text-sm">Error al cargar las actividades del protocolo.</p>';
                        }
                    }

                    // Validar formulario antes de enviar
                    document.getElementById('form-limpieza').addEventListener('submit', function(e) {
                        const estadoSelect = document.querySelector('select[name="estado"]');
                        const insumosInputs = document.querySelectorAll('input[name*="cantidad_real"]');
                        
                        // Solo validar insumos si se está completando la limpieza y hay insumos
                        if (estadoSelect.value === 'completado' && insumosInputs.length > 0) {
                            let valid = true;
                            
                            insumosInputs.forEach(input => {
                                const cantidad = parseFloat(input.value) || 0;
                                const max = parseFloat(input.getAttribute('max')) || 0;
                                const tr = input.closest('tr');
                                const strongElement = tr ? tr.querySelector('strong') : null;
                                const insumoNombre = strongElement ? strongElement.textContent : 'Insumo';
                                
                                if (cantidad > max && max > 0) {
                                    alert(`La cantidad para ${insumoNombre} (${cantidad}) excede el stock disponible (${max})`);
                                    valid = false;
                                    input.focus();
                                    return false;
                                }
                            });
                            
                            if (!valid) {
                                e.preventDefault();
                                return false;
                            }
                        }
                        
                        // Permitir el envío del formulario
                        return true;
                    });

                    // Función para calcular el costo de un insumo individual
                    function calcularCostoInsumo(input, costoUnitario) {
                        const cantidad = parseFloat(input.value) || 0;
                        const costoTotal = (cantidad * costoUnitario).toFixed(2);
                        
                        // Encontrar el span del costo en la misma fila
                        const row = input.closest('tr');
                        const costoSpan = row.querySelector('.costo-insumo');
                        if (costoSpan) {
                            costoSpan.textContent = `Q${costoTotal}`;
                        }
                        
                        // Recalcular el costo total
                        calcularCostoTotalInsumos();
                    }

                    // Función para calcular el costo total de todos los insumos
                    function calcularCostoTotalInsumos() {
                        let total = 0;
                        document.querySelectorAll('.costo-insumo').forEach(span => {
                            const valor = span.textContent.replace('Q', '');
                            total += parseFloat(valor) || 0;
                        });
                        
                        const totalSpan = document.getElementById('costo-total-insumos');
                        if (totalSpan) {
                            totalSpan.textContent = `Q${total.toFixed(2)}`;
                        }
                    }

                    // Cargar insumos al inicio si ya hay un protocolo seleccionado
                    document.addEventListener('DOMContentLoaded', function() {
                        const protocoloSelect = document.getElementById('protocolo_sanidad_id');
                        if (protocoloSelect && protocoloSelect.value) {
                            cargarInsumosProtocolo(protocoloSelect.value);
                        }
                    });

                    // Función específica para cargar solo los insumos
                    async function cargarInsumosProtocolo(protocoloId) {
                        const insumosSection = document.getElementById('insumos-section');
                        
                        if (!protocoloId) {
                            if (insumosSection) {
                                insumosSection.style.display = 'none';
                            }
                            return;
                        }

                        try {
                            const insumosResponse = await fetch(`/limpieza/protocolo/${protocoloId}/insumos`);
                            const insumosData = await insumosResponse.json();
                            
                            if (insumosData.insumos && insumosData.insumos.length > 0) {
                                // Mostrar sección de insumos
                                if (insumosSection) {
                                    insumosSection.style.display = 'block';
                                    
                                    // Buscar el contenedor de la tabla de insumos
                                    const insumosTableContainer = insumosSection.querySelector('.overflow-x-auto');
                                    if (insumosTableContainer) {
                                        let insumosHtml = `
                                            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                                                <thead class="bg-gray-50 dark:bg-gray-700">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600">Insumo</th>
                                                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600 w-32">Planificado</th>
                                                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600 w-32">Real</th>
                                                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600 w-32">Stock</th>
                                                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600 w-32">Costo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                        `;
                                        
                                        let costoTotal = 0;
                                        insumosData.insumos.forEach((insumo, index) => {
                                            const costoInsumo = (insumo.cantidad_planificada * insumo.costo_unitario).toFixed(2);
                                            costoTotal += parseFloat(costoInsumo);
                                            
                                            insumosHtml += `
                                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                        <strong>${insumo.nombre}</strong>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">${insumo.unidad}</div>
                                                        <input type="hidden" name="insumos[${index}][inventario_item_id]" value="${insumo.id}">
                                                        <input type="hidden" name="insumos[${index}][cantidad_planificada]" value="${insumo.cantidad_planificada}">
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
                                                        ${insumo.cantidad_planificada}
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <input type="number" 
                                                               name="insumos[${index}][cantidad_real]" 
                                                               value="${insumo.cantidad_planificada}"
                                                               step="0.001" 
                                                               min="0" 
                                                               max="${insumo.stock_disponible}"
                                                               class="w-20 text-center rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 p-1 text-sm"
                                                               onchange="calcularCostoInsumo(this, ${insumo.costo_unitario})">
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-sm">
                                                        <span class="${insumo.stock_disponible <= 0 ? 'text-red-600 dark:text-red-400 font-bold' : 'text-gray-900 dark:text-gray-100'}">
                                                            ${insumo.stock_disponible}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
                                                        <span class="costo-insumo">Q${costoInsumo}</span>
                                                    </td>
                                                </tr>
                                            `;
                                        });
                                        
                                        // Fila de total
                                        insumosHtml += `
                                                    <tr class="bg-gray-50 dark:bg-gray-700 font-bold">
                                                        <td colspan="4" class="px-4 py-3 text-right text-sm text-gray-900 dark:text-gray-100 border-t border-gray-300 dark:border-gray-600">
                                                            Costo Total de Insumos:
                                                        </td>
                                                        <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100 border-t border-gray-300 dark:border-gray-600">
                                                            <span id="costo-total-insumos">Q${costoTotal.toFixed(2)}</span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        `;
                                        
                                        insumosTableContainer.innerHTML = insumosHtml;
                                    }
                                }
                            } else {
                                // Ocultar sección de insumos si no hay
                                if (insumosSection) {
                                    insumosSection.style.display = 'none';
                                }
                            }
                        } catch (error) {
                            console.error('Error al cargar insumos:', error);
                            if (insumosSection) {
                                insumosSection.innerHTML = `
                                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                        <p class="text-sm text-red-700 dark:text-red-200">
                                            Error al cargar los insumos del protocolo.
                                        </p>
                                    </div>
                                `;
                            }
                        }
                    }
                </script>
            </form>
        </div>
    </div>
</x-app-layout>
