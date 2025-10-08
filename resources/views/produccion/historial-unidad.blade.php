<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Historial de Unidad de Producción
                <span class="text-base font-normal text-gray-600 dark:text-gray-400">- {{ $unidad->nombre }}</span>
            </h2>
            <a href="{{ route('produccion.unidades.show', $unidad) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-1.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center text-xs">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver a Detalle
            </a>
        </div>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-8 max-w-7xl mx-auto px-4 space-y-6">
        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="GET" action="{{ route('produccion.unidades.historial', $unidad) }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Filtro por tipo de evento -->
                        <div>
                            <label for="tipo_evento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tipo de Evento
                            </label>
                            <select name="tipo_evento" id="tipo_evento" 
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos los tipos</option>
                                <option value="Mantenimiento" {{ request('tipo_evento') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                <option value="Traslado" {{ request('tipo_evento') == 'Traslado' ? 'selected' : '' }}>Traslado</option>
                                <option value="Alerta" {{ request('tipo_evento') == 'Alerta' ? 'selected' : '' }}>Alerta</option>
                            </select>
                        </div>

                        <!-- Filtro por fecha desde -->
                        <div>
                            <label for="fecha_desde" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha Desde
                            </label>
                            <input type="date" name="fecha_desde" id="fecha_desde" 
                                   value="{{ request('fecha_desde') }}"
                                   class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Filtro por fecha hasta -->
                        <div>
                            <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha Hasta
                            </label>
                            <input type="date" name="fecha_hasta" id="fecha_hasta" 
                                   value="{{ request('fecha_hasta') }}"
                                   class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Botones de filtro -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('produccion.unidades.historial', $unidad) }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm">
                            Limpiar
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Historial de eventos</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo de Evento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                            @forelse($eventos as $evento)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap">{{ $evento->fecha->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap">{{ $evento->tipo }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap">{{ $evento->descripcion }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($evento->enlace)
                                            <a href="{{ $evento->enlace }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-xs px-2 py-1 rounded" target="_blank">Ver detalle</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500 dark:text-gray-300">No hay eventos registrados para esta unidad.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $eventos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
