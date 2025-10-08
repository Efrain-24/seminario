<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Tipo de Alimento') }}
            </h2>
            <a href="{{ route('tipos-alimento.show', $tipoAlimento) }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver
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
                <div class="p-6">
                    <form action="{{ route('tipos-alimento.update', $tipoAlimento) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Información Básica -->
                            <div class="col-span-2 border-b pb-6 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Información Básica</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Nombre *
                                        </label>
                                        <input type="text" 
                                               name="nombre" 
                                               id="nombre" 
                                               value="{{ old('nombre', $tipoAlimento->nombre) }}"
                                               required
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nombre') border-red-500 @enderror">
                                        @error('nombre')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="marca" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Marca
                                        </label>
                                        <input type="text" 
                                               name="marca" 
                                               id="marca" 
                                               value="{{ old('marca', $tipoAlimento->marca) }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('marca') border-red-500 @enderror">
                                        @error('marca')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Categoría *
                                        </label>
                                        <select name="categoria" 
                                                id="categoria" 
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('categoria') border-red-500 @enderror">
                                            <option value="">Seleccionar categoría</option>
                                            @foreach($categorias as $key => $nombre)
                                                <option value="{{ $key }}" {{ old('categoria', $tipoAlimento->categoria) == $key ? 'selected' : '' }}>
                                                    {{ $nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('categoria')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="activo" class="flex items-center">
                                            <input type="checkbox" 
                                                   name="activo" 
                                                   id="activo" 
                                                   value="1"
                                                   {{ old('activo', $tipoAlimento->activo) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Activo</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Composición Nutricional -->
                            <div class="col-span-2 border-b pb-6 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Composición Nutricional (%)</h3>
                                
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                    <div>
                                        <label for="proteina" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Proteína
                                        </label>
                                        <input type="number" 
                                               name="proteina" 
                                               id="proteina" 
                                               value="{{ old('proteina', $tipoAlimento->proteina) }}"
                                               min="0" 
                                               max="100" 
                                               step="0.01"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('proteina') border-red-500 @enderror">
                                        @error('proteina')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="grasa" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Grasa
                                        </label>
                                        <input type="number" 
                                               name="grasa" 
                                               id="grasa" 
                                               value="{{ old('grasa', $tipoAlimento->grasa) }}"
                                               min="0" 
                                               max="100" 
                                               step="0.01"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('grasa') border-red-500 @enderror">
                                        @error('grasa')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="fibra" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Fibra
                                        </label>
                                        <input type="number" 
                                               name="fibra" 
                                               id="fibra" 
                                               value="{{ old('fibra', $tipoAlimento->fibra) }}"
                                               min="0" 
                                               max="100" 
                                               step="0.01"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('fibra') border-red-500 @enderror">
                                        @error('fibra')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="humedad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Humedad
                                        </label>
                                        <input type="number" 
                                               name="humedad" 
                                               id="humedad" 
                                               value="{{ old('humedad', $tipoAlimento->humedad) }}"
                                               min="0" 
                                               max="100" 
                                               step="0.01"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('humedad') border-red-500 @enderror">
                                        @error('humedad')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="ceniza" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Ceniza
                                        </label>
                                        <input type="number" 
                                               name="ceniza" 
                                               id="ceniza" 
                                               value="{{ old('ceniza', $tipoAlimento->ceniza) }}"
                                               min="0" 
                                               max="100" 
                                               step="0.01"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('ceniza') border-red-500 @enderror">
                                        @error('ceniza')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Presentación y Costos -->
                            <div class="col-span-2 border-b pb-6 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Presentación y Costos</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="presentacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Presentación
                                        </label>
                                        <select name="presentacion" 
                                                id="presentacion"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('presentacion') border-red-500 @enderror">
                                            <option value="">Seleccionar presentación</option>
                                            @foreach($presentaciones as $key => $nombre)
                                                <option value="{{ $key }}" {{ old('presentacion', $tipoAlimento->presentacion) == $key ? 'selected' : '' }}>
                                                    {{ $nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('presentacion')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="peso_presentacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Peso por Presentación (kg)
                                        </label>
                                        <input type="number" 
                                               name="peso_presentacion" 
                                               id="peso_presentacion" 
                                               value="{{ old('peso_presentacion', $tipoAlimento->peso_presentacion) }}"
                                               min="0.01" 
                                               step="0.01"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('peso_presentacion') border-red-500 @enderror">
                                        @error('peso_presentacion')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="costo_por_kg" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Costo por Kg ($)
                                        </label>
                                        <input type="number" 
                                               name="costo_por_kg" 
                                               id="costo_por_kg" 
                                               value="{{ old('costo_por_kg', $tipoAlimento->costo_por_kg) }}"
                                               min="0.01" 
                                               step="0.01"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('costo_por_kg') border-red-500 @enderror">
                                        @error('costo_por_kg')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="col-span-2">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Descripción
                                </label>
                                <textarea name="descripcion" 
                                          id="descripcion" 
                                          rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $tipoAlimento->descripcion) }}</textarea>
                                @error('descripcion')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('tipos-alimento.show', $tipoAlimento) }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Actualizar Tipo de Alimento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
