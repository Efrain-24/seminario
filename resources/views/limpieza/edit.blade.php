<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Editar Registro de Limpieza</h2>
    </x-slot>
    <div class="py-8 max-w-2xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form action="{{ route('limpieza.update', $limpieza) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha</label>
                    <input type="date" name="fecha" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" value="{{ $limpieza->fecha }}" required>
                </div>
                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Área</label>
                    <select name="area" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
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
                <div>
                    <label for="responsable" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Responsable</label>
                    <select name="responsable" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                        <option value="">Seleccione...</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->name }}" @if($limpieza->responsable == $usuario->name) selected @endif>{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="protocolo_sanidad_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Protocolo de Sanidad</label>
                    <select name="protocolo_sanidad_id" id="protocolo_sanidad_id" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required onchange="cargarActividades()">
                        @foreach($protocolos as $protocolo)
                            <option value="{{ $protocolo->id }}" @if($limpieza->protocolo_sanidad_id == $protocolo->id) selected @endif>{{ $protocolo->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Estado</label>
                    <select name="estado" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                        <option value="no_ejecutado" @if($limpieza->estado == 'no_ejecutado') selected @endif>No Ejecutado</option>
                        <option value="en_progreso" @if($limpieza->estado == 'en_progreso') selected @endif>En Progreso</option>
                        <option value="completado" @if($limpieza->estado == 'completado') selected @endif>Completado</option>
                    </select>
                </div>

                <!-- Checklist de Actividades -->
                <div id="actividades-section">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Checklist de Actividades</label>
                    <div id="actividades-container" class="border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 p-4">
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

                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Observaciones</label>
                    <textarea name="observaciones" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" rows="3" placeholder="Observaciones generales sobre la limpieza">{{ $limpieza->observaciones }}</textarea>
                </div>

                <div class="flex gap-2 mt-4">
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white">Actualizar</button>
                    <a href="{{ route('limpieza.index') }}" class="px-4 py-2 rounded bg-gray-500 hover:bg-gray-600 text-white">Cancelar</a>
                    <a href="{{ route('protocolo-sanidad.index') }}" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Protocolos
                    </a>
                </div>

                <script>
                    async function cargarActividades() {
                        const protocoloId = document.getElementById('protocolo_sanidad_id').value;
                        const actividadesContainer = document.getElementById('actividades-container');

                        if (!protocoloId) {
                            return;
                        }

                        // Solo recarga si el protocolo cambió
                        if (protocoloId == {{ $limpieza->protocolo_sanidad_id }}) {
                            return;
                        }

                        try {
                            const response = await fetch(`/limpieza/protocolo/${protocoloId}/actividades`);
                            const data = await response.json();

                            if (data.actividades && data.actividades.length > 0) {
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
                        } catch (error) {
                            console.error('Error al cargar actividades:', error);
                            actividadesContainer.innerHTML = '<p class="text-red-500 text-sm">Error al cargar las actividades del protocolo.</p>';
                        }
                    }
                </script>
            </form>
        </div>
    </div>
</x-app-layout>
