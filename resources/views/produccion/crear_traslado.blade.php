<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Crear Nuevo Traslado
            </h2>
            <a href="{{ route('produccion.traslados') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver a Traslados
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Información del Lote (si se preseleccionó) -->
            @if($lote)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Lote Seleccionado
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Código</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $lote->codigo_lote }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Especie</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $lote->especie }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Cantidad Actual</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($lote->cantidad_actual) }} peces</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Unidad Actual</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                @if($lote->unidadProduccion)
                                    {{ $lote->unidadProduccion->nombre }}
                                @else
                                    Sin asignar
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Formulario de Traslado -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('produccion.traslados.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Lote -->
                            <div class="md:col-span-2">
                                <label for="lote_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Lote a Trasladar *
                                </label>
                                <select name="lote_id" id="lote_id" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                        required onchange="actualizarInfoLote()">
                                    @if(!$lote)
                                        <option value="">Seleccionar lote</option>
                                    @endif
                                    @foreach($lotes as $loteOption)
                                        <option value="{{ $loteOption->id }}" 
                                                data-cantidad="{{ $loteOption->cantidad_actual }}"
                                                data-unidad="{{ $loteOption->unidadProduccion ? $loteOption->unidadProduccion->nombre : 'Sin asignar' }}"
                                                {{ ($lote && $lote->id === $loteOption->id) || old('lote_id') == $loteOption->id ? 'selected' : '' }}>
                                            {{ $loteOption->codigo_lote }} - {{ $loteOption->especie }} 
                                            ({{ number_format($loteOption->cantidad_actual) }} peces)
                                        </option>
                                    @endforeach
                                </select>
                                @error('lote_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                
                                <!-- Info dinámica del lote -->
                                <div id="info-lote" class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded text-sm" style="display: none;">
                                    <p><strong>Cantidad disponible:</strong> <span id="cantidad-disponible"></span> peces</p>
                                    <p><strong>Unidad actual:</strong> <span id="unidad-actual"></span></p>
                                </div>
                            </div>

                            <!-- Fecha de Traslado -->
                            <div>
                                <label for="fecha_traslado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha de Traslado *
                                </label>
                                <input type="date" name="fecha_traslado" id="fecha_traslado" 
                                       value="{{ old('fecha_traslado', date('Y-m-d')) }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                @error('fecha_traslado')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Unidad de Destino -->
                            <div>
                                <label for="unidad_destino_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Unidad de Destino *
                                </label>
                                <select name="unidad_destino_id" id="unidad_destino_id" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Seleccionar unidad de destino</option>
                                    @foreach($unidades as $unidad)
                                        <option value="{{ $unidad->id }}" {{ old('unidad_destino_id') == $unidad->id ? 'selected' : '' }}>
                                            {{ $unidad->codigo }} - {{ $unidad->nombre }} ({{ ucfirst($unidad->tipo) }})
                                            @if($unidad->capacidad_maxima)
                                                - Cap: {{ number_format($unidad->capacidad_maxima) }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('unidad_destino_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Cantidad a Trasladar -->
                            <div>
                                <label for="cantidad_trasladada" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Cantidad a Trasladar *
                                </label>
                                <input type="number" name="cantidad_trasladada" id="cantidad_trasladada" 
                                       value="{{ old('cantidad_trasladada') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       placeholder="Número de peces a trasladar" min="1" required>
                                @error('cantidad_trasladada')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Motivo del Traslado -->
                            <div>
                                <label for="motivo_traslado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Motivo del Traslado *
                                </label>
                                <select name="motivo_traslado" id="motivo_traslado" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Seleccionar motivo</option>
                                    <option value="crecimiento" {{ old('motivo_traslado') == 'crecimiento' ? 'selected' : '' }}>Crecimiento de peces</option>
                                    <option value="sobrepoblacion" {{ old('motivo_traslado') == 'sobrepoblacion' ? 'selected' : '' }}>Sobrepoblación</option>
                                    <option value="mejores_condiciones" {{ old('motivo_traslado') == 'mejores_condiciones' ? 'selected' : '' }}>Mejores condiciones</option>
                                    <option value="mantenimiento" {{ old('motivo_traslado') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento de unidad</option>
                                    <option value="clasificacion" {{ old('motivo_traslado') == 'clasificacion' ? 'selected' : '' }}>Clasificación por tamaño</option>
                                    <option value="otro" {{ old('motivo_traslado') == 'otro' ? 'selected' : '' }}>Otro motivo</option>
                                </select>
                                @error('motivo_traslado')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Sección de Datos Adicionales -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Datos Adicionales (Opcionales)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Peso Promedio durante Traslado -->
                                <div>
                                    <label for="peso_promedio_traslado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Peso Promedio (kg)
                                    </label>
                                    <input type="number" step="0.01" name="peso_promedio_traslado" id="peso_promedio_traslado" 
                                           value="{{ old('peso_promedio_traslado') }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                           placeholder="0.00" min="0">
                                    @error('peso_promedio_traslado')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Hora de Inicio -->
                                <div>
                                    <label for="hora_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Hora de Inicio
                                    </label>
                                    <input type="time" name="hora_inicio" id="hora_inicio" 
                                           value="{{ old('hora_inicio') }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    @error('hora_inicio')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Pérdidas Estimadas -->
                                <div>
                                    <label for="cantidad_perdida" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Pérdidas Estimadas
                                    </label>
                                    <input type="number" name="cantidad_perdida" id="cantidad_perdida" 
                                           value="{{ old('cantidad_perdida', 0) }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                           placeholder="0" min="0">
                                    @error('cantidad_perdida')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Observaciones de Origen -->
                                <div>
                                    <label for="observaciones_origen" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Observaciones de Origen
                                    </label>
                                    <textarea name="observaciones_origen" id="observaciones_origen" rows="3" 
                                              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                              placeholder="Estado de los peces antes del traslado, condiciones de la unidad actual...">{{ old('observaciones_origen') }}</textarea>
                                    @error('observaciones_origen')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Observaciones de Destino -->
                                <div>
                                    <label for="observaciones_destino" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Observaciones de Destino
                                    </label>
                                    <textarea name="observaciones_destino" id="observaciones_destino" rows="3" 
                                              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                              placeholder="Condiciones de la unidad de destino, preparativos realizados...">{{ old('observaciones_destino') }}</textarea>
                                    @error('observaciones_destino')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('produccion.traslados') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                Programar Traslado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function actualizarInfoLote() {
            const select = document.getElementById('lote_id');
            const infoDiv = document.getElementById('info-lote');
            const cantidadSpan = document.getElementById('cantidad-disponible');
            const unidadSpan = document.getElementById('unidad-actual');
            
            if (select.value) {
                const option = select.options[select.selectedIndex];
                const cantidad = option.getAttribute('data-cantidad');
                const unidad = option.getAttribute('data-unidad');
                
                cantidadSpan.textContent = parseInt(cantidad).toLocaleString();
                unidadSpan.textContent = unidad;
                infoDiv.style.display = 'block';
                
                // Actualizar el máximo de cantidad a trasladar
                const cantidadInput = document.getElementById('cantidad_trasladada');
                cantidadInput.max = cantidad;
            } else {
                infoDiv.style.display = 'none';
            }
        }

        // Inicializar si hay un lote preseleccionado
        document.addEventListener('DOMContentLoaded', function() {
            actualizarInfoLote();
        });
    </script>
</x-app-layout>
