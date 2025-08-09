<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Crear Nueva Unidad de Producción') }}
            </h2>
            <a href="{{ route('produccion.unidades') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver a Unidades
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('produccion.unidades.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Código -->
                            <div>
                                <label for="codigo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Código *
                                </label>
                                <input type="text" name="codigo" id="codigo" 
                                       value="{{ old('codigo') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       placeholder="Ej: TQ-001, EST-001" required>
                                @error('codigo')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
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

                            <!-- Tipo -->
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tipo *
                                </label>
                                <select name="tipo" id="tipo" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="tanque" {{ old('tipo') == 'tanque' ? 'selected' : '' }}>Tanque</option>
                                    <option value="estanque" {{ old('tipo') == 'estanque' ? 'selected' : '' }}>Estanque</option>
                                </select>
                                @error('tipo')
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
                            <a href="{{ route('produccion.unidades') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                Crear Unidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
