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
                <a href="{{ route('produccion.unidades') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Volver a Unidades
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensajes de éxito/error -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Estadísticas de Mantenimiento -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Total</h3>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $estadisticas['total'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Pendientes</h3>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $estadisticas['pendientes'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">En Proceso</h3>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $estadisticas['en_proceso'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Completados</h3>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $estadisticas['completados'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Este Mes</h3>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $estadisticas['este_mes'] }}</p>
                </div>
            </div>

            <!-- Filtros -->
            @if(!$unidad)
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-0">
                        <label for="unidad_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filtrar por Unidad</label>
                        <select name="unidad_filter" id="unidad_filter" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm">
                            <option value="">Todas las unidades</option>
                            @foreach($unidades as $u)
                                <option value="{{ $u->id }}" {{ request('unidad_filter') == $u->id ? 'selected' : '' }}>
                                    {{ $u->nombre }} ({{ $u->codigo }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <!-- Lista de Mantenimientos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Unidad
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tipo/Prioridad
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Descripción
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Fecha Programada
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
                                @forelse($mantenimientos as $mantenimiento)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full {{ $mantenimiento->unidadProduccion->tipo === 'tanque' ? 'bg-blue-500' : 'bg-green-500' }} flex items-center justify-center text-white text-xs">
                                                        {{ $mantenimiento->unidadProduccion->tipo === 'tanque' ? '🏊' : '🏞️' }}
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $mantenimiento->unidadProduccion->nombre }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $mantenimiento->unidadProduccion->codigo }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col space-y-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $mantenimiento->tipo_mantenimiento === 'preventivo' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                    {{ $mantenimiento->tipo_mantenimiento === 'correctivo' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                    {{ $mantenimiento->tipo_mantenimiento === 'limpieza' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                    {{ $mantenimiento->tipo_mantenimiento === 'reparacion' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                                    {{ $mantenimiento->tipo_mantenimiento === 'inspeccion' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                                    {{ $mantenimiento->tipo_mantenimiento === 'desinfeccion' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}">
                                                    {{ ucfirst($mantenimiento->tipo_mantenimiento) }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $mantenimiento->prioridad === 'critica' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                                    {{ $mantenimiento->prioridad === 'alta' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                                    {{ $mantenimiento->prioridad === 'media' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                    {{ $mantenimiento->prioridad === 'baja' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                                                    {{ ucfirst($mantenimiento->prioridad) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ Str::limit($mantenimiento->descripcion_trabajo, 60) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $mantenimiento->fecha_mantenimiento->format('d/m/Y') }}
                                            <div class="text-xs text-gray-500">
                                                {{ $mantenimiento->fecha_mantenimiento->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($mantenimiento->estado_mantenimiento === 'programado')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Programado
                                                </span>
                                            @elseif($mantenimiento->estado_mantenimiento === 'en_proceso')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                    En Proceso
                                                </span>
                                            @elseif($mantenimiento->estado_mantenimiento === 'completado')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Completado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Cancelado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $mantenimiento->usuario->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('produccion.mantenimiento.show', $mantenimiento) }}" 
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    Ver
                                                </a>
                                                
                                                @if($mantenimiento->estado_mantenimiento === 'programado')
                                                    <form method="POST" action="{{ route('produccion.mantenimiento.iniciar', $mantenimiento) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                            Iniciar
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if($mantenimiento->estado_mantenimiento === 'en_proceso')
                                                    <a href="{{ route('produccion.mantenimiento.show', $mantenimiento) }}?action=completar" 
                                                       class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">
                                                        Completar
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">
                                            No hay mantenimientos registrados.
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
