<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Unidad de Producción') }}
                <span class="text-base font-normal text-gray-600 dark:text-gray-400">- {{ $unidad->nombre }}</span>
            </h2>
            <a href="{{ route('produccion.unidades.show', $unidad) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver a Detalles
            </a>
        </div>
    </x-slot>

    <!-- Notificaciones flotantes -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('produccion.unidades.update', $unidad) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Información sobre código -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    <strong>Código actual:</strong> {{ $unidad->codigo }}. 
                                    El código de las unidades no puede ser modificado para mantener la integridad de los registros.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo de Unidad -->
                            <div class="md:col-span-2">
                                <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tipo de Unidad * <span class="text-xs text-gray-500">(el código actual se mantendrá)</span>
                                </label>
                                <select name="tipo" id="tipo" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Seleccionar tipo de unidad</option>
                                    <option value="tanque" {{ old('tipo', $unidad->tipo) == 'tanque' ? 'selected' : '' }}>Tanque</option>
                                    <option value="estanque" {{ old('tipo', $unidad->tipo) == 'estanque' ? 'selected' : '' }}>Estanque</option>
                                    <option value="jaula" {{ old('tipo', $unidad->tipo) == 'jaula' ? 'selected' : '' }}>Jaula</option>
                                    <option value="sistema_especializado" {{ old('tipo', $unidad->tipo) == 'sistema_especializado' ? 'selected' : '' }}>Sistema Especializado</option>
                                </select>
                                @error('tipo')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nombre -->
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nombre *
                                </label>
                                <input type="text" name="nombre" id="nombre" 
                                       value="{{ old('nombre', $unidad->nombre) }}"
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
                                       value="{{ old('capacidad_maxima', $unidad->capacidad_maxima) }}"
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
                                       value="{{ old('area', $unidad->area) }}"
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
                                       value="{{ old('profundidad', $unidad->profundidad) }}"
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
                                       value="{{ old('fecha_construccion', $unidad->fecha_construccion?->format('Y-m-d')) }}"
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
                                          placeholder="Información adicional sobre la unidad...">{{ old('descripcion', $unidad->descripcion) }}</textarea>
                                @error('descripcion')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                @can('eliminar_unidades')
                                    <button type="button" onclick="openInhabilitarModal()" 
                                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Inhabilitar Unidad
                                    </button>
                                @endcan
                            </div>
                            <div class="flex space-x-4">
                                <a href="{{ route('produccion.unidades.show', $unidad) }}" 
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancelar
                                </a>
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Actualizar Unidad
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación para Inhabilitar -->
    <div id="inhabilitarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 text-center mb-4">
                    ¿Confirmar Inhabilitación?
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 text-center mb-6">
                    ¿Estás seguro de que quieres inhabilitar la unidad <strong>{{ $unidad->nombre }}</strong>?<br>
                    <span class="text-red-600 dark:text-red-400 font-medium">La unidad quedará como inactiva y no podrá usarse para nuevos lotes.</span>
                </p>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3 mb-4">
                    <p class="text-xs text-yellow-800 dark:text-yellow-200">
                        <strong>Nota:</strong> Solo se pueden inhabilitar unidades que no tengan lotes activos ni mantenimientos pendientes.
                    </p>
                </div>
                <form method="POST" action="{{ route('produccion.unidades.inhabilitar', $unidad) }}" class="flex justify-center space-x-4">
                    @csrf
                    @method('PATCH')
                    <button type="button" onclick="closeInhabilitarModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">
                        Inhabilitar Unidad
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openInhabilitarModal() {
            document.getElementById('inhabilitarModal').classList.remove('hidden');
        }
        
        function closeInhabilitarModal() {
            document.getElementById('inhabilitarModal').classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('inhabilitarModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeInhabilitarModal();
            }
        });
    </script>
</x-app-layout>
