<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                Gestión de Traslados
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('produccion.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Volver al Módulo
                </a>
                <a href="{{ route('produccion.traslados.crear') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Traslado
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-500 rounded-full p-3 mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Traslados</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $estadisticas['total'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-green-500 rounded-full p-3 mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Completados</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $estadisticas['completados'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-yellow-500 rounded-full p-3 mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Pendientes</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $estadisticas['pendientes'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-purple-500 rounded-full p-3 mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Este Mes</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $estadisticas['este_mes'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Traslados -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                        Historial de Traslados
                    </h3>

                    @if($traslados->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Lote
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Origen → Destino
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Fecha
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Cantidad
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Responsable
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($traslados as $traslado)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ $traslado->lote->codigo_lote }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $traslado->lote->especie }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    @if($traslado->unidadOrigen)
                                                        {{ $traslado->unidadOrigen->nombre }}
                                                    @else
                                                        <span class="text-gray-400">Sin origen</span>
                                                    @endif
                                                    →
                                                    {{ $traslado->unidadDestino->nombre }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ ucfirst(str_replace('_', ' ', $traslado->motivo_traslado)) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $traslado->fecha_traslado->format('d/m/Y') }}
                                                @if($traslado->hora_inicio)
                                                    <br><span class="text-xs text-gray-500">{{ $traslado->hora_inicio->format('H:i') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ number_format($traslado->cantidad_trasladada) }}
                                                </div>
                                                @if($traslado->cantidad_perdida > 0)
                                                    <div class="text-sm text-red-600">
                                                        -{{ number_format($traslado->cantidad_perdida) }} pérdidas
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($traslado->estado_traslado === 'completado')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        Completado
                                                    </span>
                                                @elseif($traslado->estado_traslado === 'en_proceso')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        En Proceso
                                                    </span>
                                                @elseif($traslado->estado_traslado === 'planificado')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        Planificado
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        Cancelado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $traslado->usuario->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('produccion.traslados.show', $traslado) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        Ver
                                                    </a>
                                                    @if($traslado->estado_traslado === 'planificado')
                                                        <form method="POST" action="{{ route('produccion.traslados.completar', $traslado) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                                Completar
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('produccion.traslados.cancelar', $traslado) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" 
                                                                    onclick="return confirm('¿Está seguro de cancelar este traslado?')">
                                                                Cancelar
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-6">
                            {{ $traslados->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sin traslados registrados</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Comienza registrando tu primer traslado de lotes.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('produccion.traslados.crear') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Crear Primer Traslado
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
