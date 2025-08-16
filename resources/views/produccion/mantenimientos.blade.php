<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Mantenimientos') }}
                @if($unidad)
                    <span class="text-base font-normal text-gray-600 dark:text-gray-400">- {{ $unidad->nombre }}</span>
                @endif
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('produccion.mantenimientos.historial', $unidad) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Ver Historial
                </a>
                <a href="{{ route('produccion.mantenimientos.crear', $unidad) }}" 
                   class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Programar Mantenimiento
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones flotantes -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros para Mantenimientos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('produccion.mantenimientos', $unidad) }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Estado -->
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Estado
                                </label>
                                <select name="estado" id="estado" 
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Todos los estados</option>
                                    <option value="programado" {{ request('estado') == 'programado' ? 'selected' : '' }}>Programado</option>
                                    <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                    <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                                    <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>

                            <!-- Tipo de Mantenimiento -->
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tipo
                                </label>
                                <select name="tipo" id="tipo" 
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Todos los tipos</option>
                                    <option value="preventivo" {{ request('tipo') == 'preventivo' ? 'selected' : '' }}>Preventivo</option>
                                    <option value="correctivo" {{ request('tipo') == 'correctivo' ? 'selected' : '' }}>Correctivo</option>
                                    <option value="limpieza" {{ request('tipo') == 'limpieza' ? 'selected' : '' }}>Limpieza</option>
                                    <option value="reparacion" {{ request('tipo') == 'reparacion' ? 'selected' : '' }}>Reparación</option>
                                    <option value="inspeccion" {{ request('tipo') == 'inspeccion' ? 'selected' : '' }}>Inspección</option>
                                    <option value="desinfeccion" {{ request('tipo') == 'desinfeccion' ? 'selected' : '' }}>Desinfección</option>
                                </select>
                            </div>

                            <!-- Prioridad -->
                            <div>
                                <label for="prioridad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Prioridad
                                </label>
                                <select name="prioridad" id="prioridad" 
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Todas las prioridades</option>
                                    <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                                    <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                                    <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="critica" {{ request('prioridad') == 'critica' ? 'selected' : '' }}>Crítica</option>
                                </select>
                            </div>

                            <!-- Unidad (solo si no hay unidad específica) -->
                            @if(!$unidad)
                            <div>
                                <label for="unidad_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Unidad
                                </label>
                                <select name="unidad_id" id="unidad_id" 
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Todas las unidades</option>
                                    @foreach($unidades as $u)
                                        <option value="{{ $u->id }}" {{ request('unidad_id') == $u->id ? 'selected' : '' }}>
                                            {{ $u->nombre }} ({{ $u->codigo }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>

                        <div class="flex items-center space-x-4">
                            <button type="submit" 
                                    class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Filtrar
                            </button>
                            
                            <a href="{{ route('produccion.mantenimientos', $unidad) }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-200">
                                Limpiar filtros
                            </a>
                            
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $mantenimientos->total() }} mantenimiento(s) encontrado(s)
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Mantenimientos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Información sobre la tabla -->
                    <div class="mb-4 p-3 bg-orange-50 dark:bg-orange-900 border border-orange-200 dark:border-orange-700 rounded-md">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-orange-700 dark:text-orange-300">
                                <strong>Tip:</strong> Haz clic en cualquier fila para ver los detalles completos del mantenimiento y realizar acciones.
                            </p>
                        </div>
                    </div>
                    
                    <div class="overflow-hidden">
                        <table class="w-full table-fixed">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="w-48 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Unidad
                                    </th>
                                    <th class="w-40 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tipo/Prioridad
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Descripción
                                    </th>
                                    <th class="w-32 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Fecha
                                    </th>
                                    <th class="w-24 px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="w-28 px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Responsable
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($mantenimientos as $mantenimiento)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 cursor-pointer transform hover:scale-[1.01] hover:shadow-md"
                                        onclick="window.location='{{ route('produccion.mantenimientos.show', $mantenimiento) }}'"
                                        title="Clic para ver detalles del mantenimiento">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-white text-xs
                                                        {{ $mantenimiento->unidadProduccion->tipo === 'tanque' ? 'bg-blue-500' : 
                                                           ($mantenimiento->unidadProduccion->tipo === 'estanque' ? 'bg-green-500' : 
                                                           ($mantenimiento->unidadProduccion->tipo === 'jaula' ? 'bg-purple-500' : 'bg-orange-500')) }}">
                                                        @if($mantenimiento->unidadProduccion->tipo === 'tanque')
                                                            T
                                                        @elseif($mantenimiento->unidadProduccion->tipo === 'estanque')
                                                            E
                                                        @elseif($mantenimiento->unidadProduccion->tipo === 'jaula')
                                                            J
                                                        @else
                                                            S
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="ml-2">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                        {{ $mantenimiento->unidadProduccion->nombre }}
                                                        <svg class="inline w-3 h-3 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                        {{ $mantenimiento->unidadProduccion->codigo }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="space-y-1">
                                                <div class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    {{ $mantenimiento->tipo_mantenimiento === 'preventivo' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                    {{ $mantenimiento->tipo_mantenimiento === 'correctivo' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                    {{ $mantenimiento->tipo_mantenimiento === 'limpieza' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                    {{ $mantenimiento->tipo_mantenimiento === 'reparacion' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                                    {{ $mantenimiento->tipo_mantenimiento === 'inspeccion' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                                    {{ $mantenimiento->tipo_mantenimiento === 'desinfeccion' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}">
                                                    {{ Str::limit(ucfirst($mantenimiento->tipo_mantenimiento), 12) }}
                                                </div>
                                                <div class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    {{ $mantenimiento->prioridad === 'critica' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                    {{ $mantenimiento->prioridad === 'alta' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                                    {{ $mantenimiento->prioridad === 'media' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                    {{ $mantenimiento->prioridad === 'baja' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                                                    {{ ucfirst($mantenimiento->prioridad) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                <div class="break-words leading-relaxed" title="{{ $mantenimiento->descripcion_trabajo }}">
                                                    {{ Str::limit($mantenimiento->descripcion_trabajo, 120) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            <div class="truncate">{{ $mantenimiento->fecha_mantenimiento->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500 truncate">
                                                {{ $mantenimiento->fecha_mantenimiento->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="px-2 py-4">
                                            @if($mantenimiento->estado_mantenimiento === 'programado')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Programado
                                                </span>
                                            @elseif($mantenimiento->estado_mantenimiento === 'en_proceso')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    En Proceso
                                                </span>
                                            @elseif($mantenimiento->estado_mantenimiento === 'completado')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Completado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Cancelado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            <div class="truncate" title="{{ $mantenimiento->usuario->name }}">
                                                {{ $mantenimiento->usuario->name }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-300">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                <p class="text-lg font-medium">No hay mantenimientos registrados</p>
                                                <p class="text-sm text-gray-400">Programa tu primer mantenimiento usando el botón superior</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $mantenimientos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
