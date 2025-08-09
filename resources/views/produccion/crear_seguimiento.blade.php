<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Realizar Seguimiento - {{ $lote->codigo_lote }}
            </h2>
            <a href="{{ route('produccion.seguimiento.lotes') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver a Seguimientos
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Información del Lote -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Información del Lote
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    </div>
                </div>
            </div>

            <!-- Formulario de Seguimiento -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('produccion.lotes.seguimiento.store', $lote->id) }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Fecha de Seguimiento -->
                            <div>
                                <label for="fecha_seguimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha de Seguimiento *
                                </label>
                                <input type="date" name="fecha_seguimiento" id="fecha_seguimiento" 
                                       value="{{ old('fecha_seguimiento', date('Y-m-d')) }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                @error('fecha_seguimiento')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipo de Seguimiento -->
                            <div>
                                <label for="tipo_seguimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tipo de Seguimiento *
                                </label>
                                <select name="tipo_seguimiento" id="tipo_seguimiento" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="rutinario" {{ old('tipo_seguimiento') == 'rutinario' ? 'selected' : '' }}>Rutinario</option>
                                    <option value="muestreo" {{ old('tipo_seguimiento') == 'muestreo' ? 'selected' : '' }}>Muestreo</option>
                                    <option value="mortalidad" {{ old('tipo_seguimiento') == 'mortalidad' ? 'selected' : '' }}>Registro de Mortalidad</option>
                                    <option value="traslado" {{ old('tipo_seguimiento') == 'traslado' ? 'selected' : '' }}>Traslado</option>
                                </select>
                                @error('tipo_seguimiento')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Sección de Población -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Datos de Población</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Cantidad Actual -->
                                <div>
                                    <label for="cantidad_actual" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Cantidad Actual
                                    </label>
                                    <input type="number" name="cantidad_actual" id="cantidad_actual" 
                                           value="{{ old('cantidad_actual') }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                           placeholder="Número de peces contados" min="0">
                                    @error('cantidad_actual')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Mortalidad -->
                                <div>
                                    <label for="mortalidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Mortalidad (registrada hoy)
                                    </label>
                                    <input type="number" name="mortalidad" id="mortalidad" 
                                           value="{{ old('mortalidad', 0) }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                           placeholder="Número de peces muertos" min="0">
                                    @error('mortalidad')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Biometría -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Datos Biométricos</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Peso Promedio -->
                                <div>
                                    <label for="peso_promedio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Peso Promedio (g)
                                    </label>
                                    <input type="number" step="0.01" name="peso_promedio" id="peso_promedio" 
                                           value="{{ old('peso_promedio') }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                           placeholder="0.00" min="0">
                                    @error('peso_promedio')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Talla Promedio -->
                                <div>
                                    <label for="talla_promedio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Talla Promedio (cm)
                                    </label>
                                    <input type="number" step="0.01" name="talla_promedio" id="talla_promedio" 
                                           value="{{ old('talla_promedio') }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                           placeholder="0.00" min="0">
                                    @error('talla_promedio')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Parámetros Ambientales -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Parámetros Ambientales</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Temperatura del Agua -->
                                <div>
                                    <label for="temperatura_agua" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Temperatura del Agua (°C)
                                    </label>
                                    <input type="number" step="0.1" name="temperatura_agua" id="temperatura_agua" 
                                           value="{{ old('temperatura_agua') }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                           placeholder="25.0">
                                    @error('temperatura_agua')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- pH del Agua -->
                                <div>
                                    <label for="ph_agua" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        pH del Agua
                                    </label>
                                    <input type="number" step="0.1" name="ph_agua" id="ph_agua" 
                                           value="{{ old('ph_agua') }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                           placeholder="7.0" min="0" max="14">
                                    @error('ph_agua')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Oxígeno Disuelto -->
                                <div>
                                    <label for="oxigeno_disuelto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Oxígeno Disuelto (mg/L)
                                    </label>
                                    <input type="number" step="0.1" name="oxigeno_disuelto" id="oxigeno_disuelto" 
                                           value="{{ old('oxigeno_disuelto') }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                           placeholder="6.0" min="0">
                                    @error('oxigeno_disuelto')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Observaciones
                            </label>
                            <textarea name="observaciones" id="observaciones" rows="4" 
                                      class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                      placeholder="Describe cualquier observación relevante sobre el estado del lote, comportamiento de los peces, condiciones del estanque, etc.">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('produccion.seguimiento.lotes') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                Registrar Seguimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
