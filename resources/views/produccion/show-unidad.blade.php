<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalles de Unidad de Producción') }}
                <span class="text-base font-normal text-gray-600 dark:text-gray-400">- {{ $unidad->nombre }}</span>
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('produccion.unidades') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Volver a Unidades
                </a>
                
                <a href="{{ route('produccion.mantenimientos.crear', $unidad) }}" 
                   class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Programar Mantenimiento
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Información General de la Unidad -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Información básica -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 h-16 w-16">
                                    <div class="h-16 w-16 rounded-full {{ $unidad->tipo === 'tanque' ? 'bg-blue-500' : 'bg-green-500' }} flex items-center justify-center text-white text-2xl">
                                        {{ $unidad->tipo === 'tanque' ? '🏊' : '🏞️' }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $unidad->nombre }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $unidad->codigo }}</p>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $unidad->tipo === 'tanque' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                        {{ ucfirst($unidad->tipo) }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Estado:</span>
                                    <span class="font-medium">
                                        @if($unidad->estado === 'activo')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Activo
                                            </span>
                                        @elseif($unidad->estado === 'mantenimiento')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                En Mantenimiento
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Inactivo
                                            </span>
                                        @endif
                                    </span>
                                </div>

                                @if($unidad->capacidad_maxima)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Capacidad Máxima:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($unidad->capacidad_maxima, 0) }} L</span>
                                </div>
                                @endif

                                @if($unidad->area)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Área:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($unidad->area, 2) }} m²</span>
                                </div>
                                @endif

                                @if($unidad->profundidad)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Profundidad:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($unidad->profundidad, 2) }} m</span>
                                </div>
                                @endif

                                @if($unidad->fecha_construccion)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Fecha de Construcción:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $unidad->fecha_construccion->format('d/m/Y') }}</span>
                                </div>
                                @endif

                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Fecha de Registro:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $unidad->created_at->format('d/m/Y') }}</span>
                                </div>

                                @if($unidad->ultimo_mantenimiento_realizado)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Último Mantenimiento:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $unidad->ultimo_mantenimiento_realizado->fecha_mantenimiento->format('d/m/Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Estadísticas de Producción -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Estadísticas de Producción</h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $unidad->lotes->count() }}</div>
                                    <div class="text-sm text-blue-600 dark:text-blue-400">Lotes Totales</div>
                                </div>
                                
                                <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $unidad->lotes->where('estado', 'activo')->count() }}</div>
                                    <div class="text-sm text-green-600 dark:text-green-400">Lotes Activos</div>
                                </div>
                                
                                <div class="bg-orange-50 dark:bg-orange-900 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $estadisticas_mantenimiento['total'] ?? 0 }}</div>
                                    <div class="text-sm text-orange-600 dark:text-orange-400">Mantenimientos</div>
                                </div>
                                
                                <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $estadisticas_mantenimiento['pendientes'] ?? 0 }}</div>
                                    <div class="text-sm text-purple-600 dark:text-purple-400">Mant. Pendientes</div>
                                </div>
                            </div>

                            @if($unidad->capacidad_maxima && isset($unidad->capacidad_ocupada) && $unidad->capacidad_ocupada > 0)
                            <div class="mt-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Capacidad Ocupada</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ number_format(($unidad->capacidad_ocupada / $unidad->capacidad_maxima) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(($unidad->capacidad_ocupada / $unidad->capacidad_maxima) * 100, 100) }}%"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($unidad->descripcion)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Descripción</h4>
                        <p class="text-gray-700 dark:text-gray-300">{{ $unidad->descripcion }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Lotes Activos -->
            @if($unidad->lotes->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Lotes en esta Unidad</h3>
                        <a href="{{ route('produccion.lotes') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                            Ver todos los lotes →
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Lote
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Especie
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Cantidad
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Fecha Inicio
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($unidad->lotes as $lote)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $lote->codigo }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $lote->especie }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($lote->cantidad_inicial) }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $lote->estado === 'activo' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                {{ $lote->estado === 'cosechado' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                {{ $lote->estado === 'vendido' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                                                {{ ucfirst($lote->estado) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $lote->fecha_siembra ? $lote->fecha_siembra->format('d/m/Y') : 'No definida' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('produccion.lotes.show', $lote) }}" 
                                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                Ver detalles
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Historial de Mantenimientos -->
            @if(isset($estadisticas_mantenimiento) && $estadisticas_mantenimiento['total'] > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Historial de Mantenimientos</h3>
                        <a href="{{ route('produccion.mantenimientos', $unidad) }}" class="text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300 text-sm">
                            Ver historial completo →
                        </a>
                    </div>

                    <!-- Resumen de mantenimientos -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $estadisticas_mantenimiento['total'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $estadisticas_mantenimiento['pendientes'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Pendientes</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $estadisticas_mantenimiento['en_proceso'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">En Proceso</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $estadisticas_mantenimiento['completados'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Completados</div>
                        </div>
                    </div>

                    @if($estadisticas_mantenimiento['proximo'])
                    <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Próximo Mantenimiento Programado</h4>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    {{ $estadisticas_mantenimiento['proximo']->tipo_mantenimiento ?? 'Mantenimiento' }} programado para el 
                                    {{ $estadisticas_mantenimiento['proximo']->fecha_mantenimiento->format('d/m/Y') ?? 'fecha por confirmar' }}
                                    @if(isset($estadisticas_mantenimiento['proximo']->fecha_mantenimiento))
                                        ({{ $estadisticas_mantenimiento['proximo']->fecha_mantenimiento->diffForHumans() }})
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Lista de mantenimientos recientes -->
                    @if($unidad->mantenimientos->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tipo
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Fecha
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Descripción
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($unidad->mantenimientos->take(5) as $mantenimiento)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ ucfirst($mantenimiento->tipo_mantenimiento) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $mantenimiento->fecha_mantenimiento->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $mantenimiento->estado_mantenimiento === 'programado' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                {{ $mantenimiento->estado_mantenimiento === 'en_proceso' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                                {{ $mantenimiento->estado_mantenimiento === 'completado' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                {{ $mantenimiento->estado_mantenimiento === 'cancelado' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                                {{ ucfirst(str_replace('_', ' ', $mantenimiento->estado_mantenimiento)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            {{ Str::limit($mantenimiento->descripcion_trabajo, 50) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sin mantenimientos registrados</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Esta unidad no tiene mantenimientos programados o realizados.</p>
                        <div class="mt-6">
                            <a href="{{ route('produccion.mantenimientos.crear', $unidad) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Programar primer mantenimiento
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
