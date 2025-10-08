<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Crear Nueva Unidad de Producción') }}
            </h2>
            <a href="{{ route('unidades.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver a Unidades
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
                    <form method="POST" action="{{ route('unidades.store') }}" class="space-y-6">
                        @csrf

                        <!-- Información sobre código automático -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    <strong>Código automático:</strong> El código de la unidad se generará automáticamente basado en el tipo seleccionado. 
                                    Formato: <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">TIPO+NÚMERO</code>
                                    (Ej: TQ001, ES002, JL003, SE004)
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo de Unidad (ahora primero) -->
                            <div class="md:col-span-2">
                                <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tipo de Unidad * <span class="text-xs text-gray-500">(determina el código de la unidad)</span>
                                </label>
                                <select name="tipo" id="tipo" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Seleccionar tipo de unidad</option>
                                    <option value="tanque" {{ old('tipo') == 'tanque' ? 'selected' : '' }}>Tanque</option>
                                    <option value="estanque" {{ old('tipo') == 'estanque' ? 'selected' : '' }}>Estanque</option>
                                    <option value="jaula" {{ old('tipo') == 'jaula' ? 'selected' : '' }}>Jaula</option>
                                    <option value="sistema_especializado" {{ old('tipo') == 'sistema_especializado' ? 'selected' : '' }}>Sistema Especializado</option>
                                </select>
                                @error('tipo')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                
                                <!-- Mensaje informativo del código que se generará -->
                                <p class="mt-2 text-xs text-green-600 dark:text-green-400">
                                      <span id="codigo-ejemplo">Seleccione un tipo para ver el código que se generará</span>
                                </p>
                            </div>

                            <!-- Nombre -->
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nombre *
                                </label>
                                <input type="text" name="nombre" id="nombre" 
                                       value="{{ old('nombre') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       placeholder="Ej: Tanque Principal 1" required>
                                @error('nombre')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Capacidad Máxima -->
                            <div>
                                <label for="capacidad_maxima" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Capacidad Máxima (L)
                                </label>
                                <input type="number" step="0.01" name="capacidad_maxima" id="capacidad_maxima" 
                                       value="{{ old('capacidad_maxima') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       placeholder="0.00" min="0">
                                @error('capacidad_maxima')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Área -->
                            <div>
                                <label for="area" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Área (m²)
                                </label>
                                <input type="number" step="0.01" name="area" id="area" 
                                       value="{{ old('area') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       placeholder="0.00" min="0">
                                @error('area')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Profundidad -->
                            <div>
                                <label for="profundidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Profundidad (m)
                                </label>
                                <input type="number" step="0.01" name="profundidad" id="profundidad" 
                                       value="{{ old('profundidad') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       placeholder="0.00" min="0">
                                @error('profundidad')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fecha de Construcción -->
                            <div class="md:col-span-2">
                                <label for="fecha_construccion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha de Construcción
                                </label>
                                <input type="date" name="fecha_construccion" id="fecha_construccion" 
                                       value="{{ old('fecha_construccion') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('fecha_construccion')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Descripción
                                </label>
                                <textarea name="descripcion" id="descripcion" rows="3" 
                                          class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                          placeholder="Información adicional sobre la unidad...">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('unidades.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Crear Unidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo');
            const codigoEjemplo = document.getElementById('codigo-ejemplo');

            // Mapeo de tipos a información de códigos
            const tipoInfo = {
                'tanque': 'Formato: TQ001, TQ002, TQ003...',
                'estanque': 'Formato: ES001, ES002, ES003...',
                'jaula': 'Formato: JL001, JL002, JL003...',
                'sistema_especializado': 'Formato: SE001, SE002, SE003...'
            };

            // Función para mostrar preview del código
            function showCodePreview() {
                const tipo = tipoSelect.value;
                
                if (!tipo) {
                    codigoEjemplo.textContent = "Seleccione un tipo para ver el código que se generará";
                    return;
                }

                // Llamar al servidor para obtener el siguiente código que se generaría
                fetch(`/unidades/generate-code/${tipo}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.codigo) {
                        codigoEjemplo.innerHTML = `Se generará el código: <strong class="font-mono">${data.codigo}</strong>`;
                    }
                })
                .catch(error => {
                    console.error('Error al obtener preview del código:', error);
                    if (tipo && tipoInfo[tipo]) {
                        codigoEjemplo.textContent = tipoInfo[tipo];
                    }
                });
            }

            // Mostrar preview cuando cambie el tipo
            tipoSelect.addEventListener('change', function() {
                showCodePreview();
            });

            // Mostrar preview inicial si hay un tipo seleccionado
            if (tipoSelect.value) {
                showCodePreview();
            }
        });
    </script>
</x-app-layout>