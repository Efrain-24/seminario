<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Crear Nuevo Lote') }}
            </h2>
            <a href="{{ route('produccion.lotes') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver a Lotes
            </a>
        </div>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('produccion.lotes.store') }}" class="space-y-6" onsubmit="prepararFormulario()">
                        @csrf

                        <!-- Input hidden para la especie final -->
                        <input type="hidden" name="especie" id="especie_final" value="{{ old('especie') }}">

                        <!-- Información sobre código automático -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    <strong>Código automático:</strong> El código del lote se generará automáticamente basado en la especie seleccionada. 
                                    Formato: <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">ESPECIE-AÑO-CORRELATIVO</code>
                                    (Ej: TIL-2025-001, TRU-2025-002)
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Especie (ahora primero) -->
                            <div class="md:col-span-2">
                                <label for="especie" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Especie * <span class="text-xs text-gray-500">(determina el código del lote)</span>
                                </label>
                                <select id="especie_select" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                        required onchange="actualizarCodigoPreview()">
                                    <option value="">Seleccionar especie</option>
                                    <option value="Tilapia Nilótica" {{ old('especie') == 'Tilapia Nilótica' ? 'selected' : '' }}>Tilapia Nilótica</option>
                                    <option value="Trucha Arcoíris" {{ old('especie') == 'Trucha Arcoíris' ? 'selected' : '' }}>Trucha Arcoíris</option>
                                    <option value="Carpa" {{ old('especie') == 'Carpa' ? 'selected' : '' }}>Carpa</option>
                                    <option value="Salmón" {{ old('especie') == 'Salmón' ? 'selected' : '' }}>Salmón</option>
                                    <option value="Bagre" {{ old('especie') == 'Bagre' ? 'selected' : '' }}>Bagre</option>
                                    <option value="Cachama" {{ old('especie') == 'Cachama' ? 'selected' : '' }}>Cachama</option>
                                    <option value="Bocachico" {{ old('especie') == 'Bocachico' ? 'selected' : '' }}>Bocachico</option>
                                    <option value="Yamú" {{ old('especie') == 'Yamú' ? 'selected' : '' }}>Yamú</option>
                                    <option value="Otra" {{ old('especie') == 'Otra' ? 'selected' : '' }}>Otra especie</option>
                                </select>
                                @error('especie')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                
                                <!-- Vista previa del código -->
                                <div id="codigo-preview" class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded text-sm text-gray-600 dark:text-gray-400" style="display: none;">
                                    <strong>Código que se generará:</strong> <span id="codigo-texto" class="font-mono text-blue-600 dark:text-blue-400"></span>
                                </div>
                            </div>

                            <!-- Campo para especie personalizada -->
                            <div id="especie-otra" class="md:col-span-2" style="display: none;">
                                <label for="especie_personalizada" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Especificar otra especie
                                </label>
                                <input type="text" id="especie_personalizada" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       placeholder="Escribir nombre de la especie" onchange="actualizarEspeciePersonalizada()">
                            </div>

                            <!-- Cantidad Inicial -->
                            <div>
                                <label for="cantidad_inicial" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Cantidad Inicial *
                                </label>
                                <input type="number" name="cantidad_inicial" id="cantidad_inicial" 
                                       value="{{ old('cantidad_inicial') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       placeholder="Número de peces" min="1" required>
                                @error('cantidad_inicial')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fecha de Inicio -->
                            <div>
                                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha de Inicio *
                                </label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" 
                                       value="{{ old('fecha_inicio', date('Y-m-d')) }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                @error('fecha_inicio')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fecha de Siembra -->
                            <div>
                                <label for="fecha_siembra" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha de Siembra
                                </label>
                                <input type="date" name="fecha_siembra" id="fecha_siembra" 
                                       value="{{ old('fecha_siembra') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Fecha cuando se sembraron los peces en el lote (necesaria para reportes de ganancia)</p>
                                @error('fecha_siembra')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Peso Promedio Inicial -->
                            <div>
                                <label for="peso_promedio_inicial_gramos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Peso Promedio Inicial (gramos)
                                </label>
                                <input type="number" step="0.1" name="peso_promedio_inicial_gramos" id="peso_promedio_inicial_gramos" 
                                       value="{{ old('peso_promedio_inicial_gramos') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       placeholder="11.0" min="11" max="990">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Rango permitido: 11 a 990 gramos. Será convertido automáticamente a kg para almacenamiento.</p>
                                @error('peso_promedio_inicial_gramos')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Talla Promedio Inicial -->
                            <div>
                                <label for="talla_promedio_inicial" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Talla Promedio Inicial (cm)
                                </label>
                                <input type="number" step="0.01" name="talla_promedio_inicial" id="talla_promedio_inicial" 
                                       value="{{ old('talla_promedio_inicial') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       placeholder="0.00" min="0">
                                @error('talla_promedio_inicial')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Precio por Libra -->
                            <!-- Precio por Libra -->
                            <div>
                                <label for="precio_libra" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Precio por Libra (Q)
                                </label>
                                <input type="number" step="0.01" name="precio_libra" id="precio_libra" 
                                       value="{{ old('precio_libra') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-100"
                                       placeholder="Ej: 15.00"
                                       min="0.01">
                                @error('precio_libra')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>                            <!-- Precio de Alevín -->
                            <div>
                                <label for="precio_unitario_pez" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Precio de Alevín (Q)
                                </label>
                                <input type="number" step="0.01" name="precio_unitario_pez" id="precio_unitario_pez" 
                                       value="{{ old('precio_unitario_pez') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       placeholder="5.00" min="0">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Costo de compra por cada alevín en el lote (necesario para reportes de ganancia)</p>
                                @error('precio_unitario_pez')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Unidad de Producción -->
                            <div class="md:col-span-2">
                                <label for="unidad_produccion_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Unidad de Producción
                                </label>
                                <select name="unidad_produccion_id" id="unidad_produccion_id" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Seleccionar unidad (opcional)</option>
                                    @foreach($unidades as $unidad)
                                        <option value="{{ $unidad->id }}" {{ old('unidad_produccion_id') == $unidad->id ? 'selected' : '' }}>
                                            {{ $unidad->codigo }} - {{ $unidad->nombre }} ({{ ucfirst($unidad->tipo) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('unidad_produccion_id')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Observaciones -->
                            <div class="md:col-span-2">
                                <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Observaciones
                                </label>
                                <textarea name="observaciones" id="observaciones" rows="3" 
                                          class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                          placeholder="Información adicional sobre el lote...">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('produccion.lotes') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                Crear Lote
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function prepararFormulario() {
            const especieSelect = document.getElementById('especie_select');
            const especiePersonalizada = document.getElementById('especie_personalizada');
            const especieFinal = document.getElementById('especie_final');
            
            if (especieSelect.value === 'Otra' && especiePersonalizada.value.trim()) {
                especieFinal.value = especiePersonalizada.value.trim();
            } else {
                especieFinal.value = especieSelect.value;
            }
        }

        function actualizarCodigoPreview() {
            const especieSelect = document.getElementById('especie_select');
            const codigoPreview = document.getElementById('codigo-preview');
            const codigoTexto = document.getElementById('codigo-texto');
            const especieOtra = document.getElementById('especie-otra');
            const especieFinal = document.getElementById('especie_final');
            
            if (especieSelect.value === 'Otra') {
                especieOtra.style.display = 'block';
                codigoPreview.style.display = 'none';
                especieFinal.value = '';
            } else if (especieSelect.value) {
                especieOtra.style.display = 'none';
                especieFinal.value = especieSelect.value;
                const acronimo = generarAcronimo(especieSelect.value);
                const año = new Date().getFullYear();
                const codigoEjemplo = acronimo + '-' + año + '-XXX';
                codigoTexto.textContent = codigoEjemplo;
                codigoPreview.style.display = 'block';
            } else {
                especieOtra.style.display = 'none';
                codigoPreview.style.display = 'none';
                especieFinal.value = '';
            }
        }

        function actualizarEspeciePersonalizada() {
            const especiePersonalizada = document.getElementById('especie_personalizada');
            const codigoPreview = document.getElementById('codigo-preview');
            const codigoTexto = document.getElementById('codigo-texto');
            const especieFinal = document.getElementById('especie_final');
            
            if (especiePersonalizada.value.trim()) {
                especieFinal.value = especiePersonalizada.value.trim();
                
                const acronimo = generarAcronimo(especiePersonalizada.value);
                const año = new Date().getFullYear();
                const codigoEjemplo = acronimo + '-' + año + '-XXX';
                codigoTexto.textContent = codigoEjemplo;
                codigoPreview.style.display = 'block';
            } else {
                codigoPreview.style.display = 'none';
                especieFinal.value = '';
            }
        }

        function generarAcronimo(especie) {
            especie = especie.toLowerCase().trim();
            
            const mapeoEspecies = {
                'tilapia': 'TIL',
                'tilapia nilótica': 'TIL',
                'tilapia nilotica': 'TIL',
                'trucha': 'TRU',
                'trucha arcoíris': 'TRU',
                'trucha arcoiris': 'TRU',
                'carpa': 'CAR',
                'salmón': 'SAL',
                'salmon': 'SAL',
                'bagre': 'BAG',
                'cachama': 'CAC',
                'bocachico': 'BOC',
                'yamú': 'YAM',
                'yamu': 'YAM'
            };
            
            // Buscar coincidencia exacta
            if (mapeoEspecies[especie]) {
                return mapeoEspecies[especie];
            }
            
            // Buscar coincidencia parcial
            for (const [nombreEspecie, acronimo] of Object.entries(mapeoEspecies)) {
                if (especie.includes(nombreEspecie)) {
                    return acronimo;
                }
            }
            
            // Generar acrónimo de las primeras letras
            const palabras = especie.split(' ');
            let acronimo = '';
            
            for (const palabra of palabras) {
                if (palabra.length > 0) {
                    acronimo += palabra.charAt(0).toUpperCase();
                }
            }
            
            // Asegurar que tenga al menos 3 caracteres
            if (acronimo.length < 3) {
                acronimo = especie.replace(/\s/g, '').substring(0, 3).toUpperCase();
            }
            
            return acronimo.substring(0, 3);
        }

        // Actualizar al cargar la página si hay un valor seleccionado
        document.addEventListener('DOMContentLoaded', function() {
            actualizarCodigoPreview();
        });
    </script>
</x-app-layout>
