<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Alimentación') }}
                <span class="text-base font-normal text-gray-600 dark:text-gray-400">
                    - {{ $alimentacion->fecha_alimentacion->format('d/m/Y H:i') }}
                </span>
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('alimentacion.show', $alimentacion) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Ver
                </a>
                <a href="{{ route('alimentacion.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones flotantes -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('alimentacion.update', $alimentacion) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

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
                                            class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @foreach($lotes as $lote)
                                            <option value="{{ $lote->id }}" {{ old('lote_id', $alimentacion->lote_id) == $lote->id ? 'selected' : '' }}>
                                                {{ $lote->codigo_lote }} - {{ $lote->unidadProduccion->nombre }} ({{ $lote->especie }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lote_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tipo de Alimento -->
                                <div>
                                    <label for="tipo_alimento_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Tipo de Alimento <span class="text-red-500">*</span>
                                    </label>
                                    <select name="tipo_alimento_id" id="tipo_alimento_id" required
                                            class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @foreach($tiposAlimento as $tipo)
                                            <option value="{{ $tipo->id }}" 
                                                    data-costo="{{ $tipo->costo_por_kg }}" 
                                                    data-categoria="{{ $tipo->categoria }}"
                                                    {{ old('tipo_alimento_id', $alimentacion->tipo_alimento_id) == $tipo->id ? 'selected' : '' }}>
                                                {{ $tipo->nombre_completo }} - {{ ucfirst($tipo->categoria) }}
                                                @if($tipo->costo_por_kg) (Q{{ $tipo->costo_por_kg }}/lbs) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tipo_alimento_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Fecha de Alimentación -->
                                <div>
                                    <label for="fecha_alimentacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Fecha de Alimentación <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="fecha_alimentacion" id="fecha_alimentacion" 
                                           value="{{ old('fecha_alimentacion', $alimentacion->fecha_alimentacion->format('Y-m-d')) }}" 
                                           max="{{ now()->format('Y-m-d') }}" required
                                           class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                                           value="{{ old('hora_alimentacion', $alimentacion->hora_alimentacion->format('H:i')) }}" required
                                           class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('hora_alimentacion')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
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
                                           value="{{ old('cantidad_kg', $alimentacion->cantidad_kg) }}" required
                                           class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Ej: 5.5">
                                    @error('cantidad_kg')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Libras reales de alimento que se echaron al estanque</p>
                                    <!-- Costo estimado -->
                                    <div id="costo_estimado" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Costo estimado: <span id="costo_valor">Q{{ number_format($alimentacion->costo_total ?? 0, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Método de Alimentación -->
                                <div>
                                    <label for="metodo_alimentacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Método <span class="text-red-500">*</span>
                                    </label>
                                    <select name="metodo_alimentacion" id="metodo_alimentacion" required
                                            class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @foreach(App\Models\Alimentacion::getMetodosAlimentacion() as $key => $label)
                                            <option value="{{ $key }}" {{ old('metodo_alimentacion', $alimentacion->metodo_alimentacion) == $key ? 'selected' : '' }}>
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
                                            class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">No especificado</option>
                                        @foreach(App\Models\Alimentacion::getEstadosPeces() as $key => $label)
                                            <option value="{{ $key }}" {{ old('estado_peces', $alimentacion->estado_peces) == $key ? 'selected' : '' }}>
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
                                           value="{{ old('porcentaje_consumo', $alimentacion->porcentaje_consumo) }}"
                                           class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                                          class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Observaciones sobre el comportamiento de los peces, condiciones ambientales, incidencias, etc.">{{ old('observaciones', $alimentacion->observaciones) }}</textarea>
                                @error('observaciones')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex justify-end space-x-4 pt-6">
                            <a href="{{ route('alimentacion.show', $alimentacion) }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Actualizar Alimentación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoAlimentoSelect = document.getElementById('tipo_alimento_id');
            const cantidadInput = document.getElementById('cantidad_kg');
            const costoEstimadoDiv = document.getElementById('costo_estimado');
            const costoValorSpan = document.getElementById('costo_valor');

            function calcularCosto() {
                const selectedOption = tipoAlimentoSelect.options[tipoAlimentoSelect.selectedIndex];
                const costoPorKg = parseFloat(selectedOption.getAttribute('data-costo')) || 0;
                const cantidad = parseFloat(cantidadInput.value) || 0;

                if (costoPorKg > 0 && cantidad > 0) {
                    const costoTotal = costoPorKg * cantidad;
                    costoValorSpan.textContent = 'Q' + costoTotal.toFixed(2);
                    costoEstimadoDiv.style.display = 'block';
                } else {
                    costoEstimadoDiv.style.display = 'block';
                    costoValorSpan.textContent = 'Q0.00';
                }
            }

            tipoAlimentoSelect.addEventListener('change', calcularCosto);
            cantidadInput.addEventListener('input', calcularCosto);

            // Calcular al cargar la página
            calcularCosto();
        });
    </script>
</x-app-layout>
