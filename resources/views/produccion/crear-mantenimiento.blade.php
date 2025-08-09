<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Programar Mantenimiento') }}
                @if($unidad)
                    <span class="text-base font-normal text-gray-600 dark:text-gray-400">- {{ $unidad->nombre }}</span>
                @endif
            </h2>
            <a href="{{ $unidad ? route('produccion.mantenimientos', $unidad) : route('produccion.mantenimientos') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensajes de error -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>¡Ups! Algo salió mal.</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('produccion.mantenimientos.store') }}" class="space-y-6">
                        @csrf

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
                                            {{ (old('unidad_produccion_id', $unidad?->id) == $u->id) ? 'selected' : '' }}>
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
                                <option value="preventivo" {{ old('tipo_mantenimiento') === 'preventivo' ? 'selected' : '' }}>
                                    Preventivo - Mantenimiento programado regular
                                </option>
                                <option value="correctivo" {{ old('tipo_mantenimiento') === 'correctivo' ? 'selected' : '' }}>
                                    Correctivo - Reparación de problemas específicos
                                </option>
                                <option value="limpieza" {{ old('tipo_mantenimiento') === 'limpieza' ? 'selected' : '' }}>
                                    Limpieza - Limpieza profunda y desinfección
                                </option>
                                <option value="reparacion" {{ old('tipo_mantenimiento') === 'reparacion' ? 'selected' : '' }}>
                                    Reparación - Reparaciones mayores de infraestructura
                                </option>
                                <option value="inspeccion" {{ old('tipo_mantenimiento') === 'inspeccion' ? 'selected' : '' }}>
                                    Inspección - Inspección y evaluación general
                                </option>
                                <option value="desinfeccion" {{ old('tipo_mantenimiento') === 'desinfeccion' ? 'selected' : '' }}>
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
                                <option value="baja" {{ old('prioridad') === 'baja' ? 'selected' : '' }}>
                                    Baja - No urgente, puede programarse con flexibilidad
                                </option>
                                <option value="media" {{ old('prioridad') === 'media' ? 'selected' : '' }}>
                                    Media - Importante, programar en las próximas semanas
                                </option>
                                <option value="alta" {{ old('prioridad') === 'alta' ? 'selected' : '' }}>
                                    Alta - Urgente, requiere atención pronta
                                </option>
                                <option value="critica" {{ old('prioridad') === 'critica' ? 'selected' : '' }}>
                                    Crítica - Emergencia, atención inmediata
                                </option>
                            </select>
                        </div>

                        <!-- Fecha Programada -->
                        <div>
                            <label for="fecha_mantenimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha Programada *
                            </label>
                            <input type="date" name="fecha_mantenimiento" id="fecha_mantenimiento" required
                                   value="{{ old('fecha_mantenimiento', now()->addDays(1)->format('Y-m-d')) }}"
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
                                      class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('descripcion_trabajo') }}</textarea>
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
                                      class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('observaciones_antes') }}</textarea>
                        </div>

                        <!-- Opciones especiales -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_vaciado" value="1" 
                                           {{ old('requiere_vaciado') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Requiere vaciado de la unidad
                                    </span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_traslado_peces" value="1" 
                                           {{ old('requiere_traslado_peces') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Requiere traslado de peces
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <a href="{{ $unidad ? route('produccion.mantenimientos', $unidad) : route('produccion.mantenimientos') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                Programar Mantenimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            Información sobre Mantenimientos
                        </h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>Preventivo:</strong> Mantenimiento programado para prevenir problemas futuros</li>
                                <li><strong>Correctivo:</strong> Reparación de problemas específicos o fallas detectadas</li>
                                <li><strong>Limpieza:</strong> Limpieza profunda y desinfección de la unidad</li>
                                <li><strong>Reparación:</strong> Reparaciones mayores de infraestructura y equipos</li>
                                <li><strong>Inspección:</strong> Inspección general del estado de la unidad</li>
                                <li><strong>Desinfección:</strong> Desinfección especializada y sanitización</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
