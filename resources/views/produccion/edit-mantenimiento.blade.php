<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Mantenimiento') }}
                <span class="text-base font-normal text-gray-600 dark:text-gray-400">- {{ $mantenimiento->unidadProduccion->nombre }}</span>
            </h2>
            <a href="{{ route('produccion.mantenimientos.show', $mantenimiento) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <!-- Notificaciones flotantes -->
    @if ($errors->any())
        <x-notification type="error" message="¡Ups! Hay algunos errores en el formulario. Revisa los campos marcados en rojo." />
    @endif
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('produccion.mantenimientos.update', $mantenimiento) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Unidad de Producción -->
                        <div>
                            <label for="unidad_produccion_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Unidad de Producción *
                            </label>
                            <select name="unidad_produccion_id" id="unidad_produccion_id" required 
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar unidad...</option>
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}" 
                                            {{ (old('unidad_produccion_id', $mantenimiento->unidad_produccion_id) == $u->id) ? 'selected' : '' }}>
                                        {{ $u->nombre }} ({{ $u->codigo }}) - {{ ucfirst($u->tipo) }}
                                        @if($u->estado !== 'activo')
                                            - {{ ucfirst($u->estado) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo de Mantenimiento -->
                        <div>
                            <label for="tipo_mantenimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tipo de Mantenimiento *
                            </label>
                            <select name="tipo_mantenimiento" id="tipo_mantenimiento" required 
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar tipo...</option>
                                <option value="preventivo" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'preventivo' ? 'selected' : '' }}>
                                    Preventivo - Mantenimiento programado regular
                                </option>
                                <option value="correctivo" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'correctivo' ? 'selected' : '' }}>
                                    Correctivo - Reparación de problemas específicos
                                </option>
                                <option value="limpieza" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'limpieza' ? 'selected' : '' }}>
                                    Limpieza - Limpieza profunda y desinfección
                                </option>
                                <option value="reparacion" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'reparacion' ? 'selected' : '' }}>
                                    Reparación - Reparaciones mayores de infraestructura
                                </option>
                                <option value="inspeccion" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'inspeccion' ? 'selected' : '' }}>
                                    Inspección - Inspección y evaluación general
                                </option>
                                <option value="desinfeccion" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'desinfeccion' ? 'selected' : '' }}>
                                    Desinfección - Desinfección especializada
                                </option>
                            </select>
                        </div>

                        <!-- Prioridad -->
                        <div>
                            <label for="prioridad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Prioridad *
                            </label>
                            <select name="prioridad" id="prioridad" required 
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar prioridad...</option>
                                <option value="baja" {{ old('prioridad', $mantenimiento->prioridad) === 'baja' ? 'selected' : '' }}>
                                    Baja - No urgente, puede programarse con flexibilidad
                                </option>
                                <option value="media" {{ old('prioridad', $mantenimiento->prioridad) === 'media' ? 'selected' : '' }}>
                                    Media - Importante, programar en las próximas semanas
                                </option>
                                <option value="alta" {{ old('prioridad', $mantenimiento->prioridad) === 'alta' ? 'selected' : '' }}>
                                    Alta - Urgente, requiere atención pronta
                                </option>
                                <option value="critica" {{ old('prioridad', $mantenimiento->prioridad) === 'critica' ? 'selected' : '' }}>
                                    Crítica - Emergencia, atención inmediata
                                </option>
                            </select>
                        </div>

                        <!-- Usuario Responsable -->
                        <div>
                            <label for="usuario_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Usuario Responsable *
                            </label>
                            <select name="usuario_id" id="usuario_id" required 
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar responsable...</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('usuario_id', $mantenimiento->usuario_id) == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Selecciona quien será el responsable de ejecutar este mantenimiento
                            </p>
                            @error('usuario_id')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha Programada -->
                        <div>
                            <label for="fecha_mantenimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha Programada *
                            </label>
                            <input type="date" name="fecha_mantenimiento" id="fecha_mantenimiento" required
                                   value="{{ old('fecha_mantenimiento', $mantenimiento->fecha_mantenimiento->format('Y-m-d')) }}"
                                   min="{{ now()->format('Y-m-d') }}"
                                   class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion_trabajo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Descripción del Mantenimiento *
                            </label>
                            <textarea name="descripcion_trabajo" id="descripcion_trabajo" rows="4" required
                                      placeholder="Describe detalladamente el trabajo de mantenimiento a realizar..."
                                      class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('descripcion_trabajo', $mantenimiento->descripcion_trabajo) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Incluye detalles sobre el trabajo a realizar, materiales necesarios, etc.
                            </p>
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label for="observaciones_antes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Observaciones Previas
                            </label>
                            <textarea name="observaciones_antes" id="observaciones_antes" rows="3"
                                      placeholder="Observaciones, notas especiales o requerimientos específicos..."
                                      class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('observaciones_antes', $mantenimiento->observaciones_antes) }}</textarea>
                        </div>

                        <!-- Opciones especiales -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_vaciado" value="1" 
                                           {{ old('requiere_vaciado', $mantenimiento->requiere_vaciado) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Requiere vaciado de la unidad
                                    </span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_traslado_peces" value="1" 
                                           {{ old('requiere_traslado_peces', $mantenimiento->requiere_traslado_peces) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Requiere traslado de peces
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <a href="{{ route('produccion.mantenimientos.show', $mantenimiento) }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                Actualizar Mantenimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Importante: Edición de Mantenimiento
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>Solo se pueden editar mantenimientos que estén en estado <strong>Programado</strong>. 
                            Una vez que un mantenimiento ha sido iniciado o completado, ya no puede modificarse.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
