<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Historial de Mantenimientos') }}
                @if($unidad)
                    <span class="text-base font-normal text-gray-600 dark:text-gray-400">- {{ $unidad->nombre }}</span>
                @endif
            </h2>
            <div class="flex space-x-3">
                @if($unidad)
                    <a href="{{ route('produccion.unidades.show', $unidad) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        Volver a Unidad
                    </a>
                @else
                    <a href="{{ route('produccion.mantenimientos') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        Volver a Mantenimientos
                    </a>
                @endif
                
                <a href="{{ route('produccion.mantenimientos.crear', $unidad) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nuevo Mantenimiento
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones flotantes -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Resumen Estadístico -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completados</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $estadisticas['completados'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tiempo Prom. (hrs)</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $estadisticas['tiempo_promedio'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Costo Total</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Q{{ number_format($estadisticas['costo_total'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Este Año</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $estadisticas['este_anio'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tipo
                                </label>
                                <select name="tipo" id="tipo" 
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Todos</option>
                                    <option value="preventivo" {{ request('tipo') == 'preventivo' ? 'selected' : '' }}>Preventivo</option>
                                    <option value="correctivo" {{ request('tipo') == 'correctivo' ? 'selected' : '' }}>Correctivo</option>
                                    <option value="limpieza" {{ request('tipo') == 'limpieza' ? 'selected' : '' }}>Limpieza</option>
                                    <option value="reparacion" {{ request('tipo') == 'reparacion' ? 'selected' : '' }}>Reparación</option>
                                    <option value="inspeccion" {{ request('tipo') == 'inspeccion' ? 'selected' : '' }}>Inspección</option>
                                    <option value="desinfeccion" {{ request('tipo') == 'desinfeccion' ? 'selected' : '' }}>Desinfección</option>
                                </select>
                            </div>

                            <div>
                                <label for="anio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Año
                                </label>
                                <select name="anio" id="anio" 
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Todos</option>
                                    @for($year = now()->year; $year >= (now()->year - 5); $year--)
                                        <option value="{{ $year }}" {{ request('anio') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label for="mes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Mes
                                </label>
                                <select name="mes" id="mes" 
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Todos</option>
                                    @for($month = 1; $month <= 12; $month++)
                                        <option value="{{ $month }}" {{ request('mes') == $month ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            @if(!$unidad)
                            <div>
                                <label for="unidad_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Unidad
                                </label>
                                <select name="unidad_id" id="unidad_id" 
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Todas</option>
                                    @foreach($unidades as $u)
                                        <option value="{{ $u->id }}" {{ request('unidad_id') == $u->id ? 'selected' : '' }}>
                                            {{ $u->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="flex items-end">
                                <button type="submit" 
                                        class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                    Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Historial Timeline -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">📋 Historial de Mantenimientos</h3>
                    
                    @forelse($mantenimientos as $mantenimiento)
                        <div class="border-l-4 border-orange-400 pl-6 pb-8 relative">
                            <!-- Punto en la línea -->
                            <div class="absolute -left-2 w-4 h-4 bg-orange-400 rounded-full"></div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ ucfirst($mantenimiento->tipo_mantenimiento) }}
                                            @if(!$unidad)
                                                - {{ $mantenimiento->unidadProduccion->nombre }}
                                            @endif
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                                            {{ $mantenimiento->fecha_mantenimiento->format('d/m/Y') }}
                                            @if($mantenimiento->fecha_fin)
                                                - Completado el {{ $mantenimiento->fecha_fin->format('d/m/Y') }}
                                            @endif
                                        </p>
                                    </div>

                                    <!-- Estado -->
                                    @if($mantenimiento->estado_mantenimiento === 'completado')
                                        <span class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 text-sm font-semibold px-3 py-1 rounded-full">
                                            ✅ Completado
                                        </span>
                                    @elseif($mantenimiento->estado_mantenimiento === 'cancelado')
                                        <span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 text-sm font-semibold px-3 py-1 rounded-full">
                                            ❌ Cancelado
                                        </span>
                                    @endif
                                </div>

                                <!-- Descripción -->
                                <div class="mb-4">
                                    <p class="text-gray-700 dark:text-gray-300">{{ $mantenimiento->descripcion_trabajo }}</p>
                                </div>

                                <!-- Detalles adicionales -->
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Responsable:</span>
                                        <p class="text-gray-900 dark:text-gray-100">{{ $mantenimiento->usuario->name }}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Prioridad:</span>
                                        <p class="text-gray-900 dark:text-gray-100">{{ ucfirst($mantenimiento->prioridad) }}</p>
                                    </div>
                                    @if($mantenimiento->costo_mantenimiento)
                                    <div>
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Costo:</span>
                                        <p class="text-gray-900 dark:text-gray-100">${{ number_format($mantenimiento->costo_mantenimiento, 2) }}</p>
                                    </div>
                                    @endif
                                    @if($mantenimiento->fecha_inicio && $mantenimiento->fecha_fin)
                                    <div>
                                        <span class="font-medium text-gray-600 dark:text-gray-400">Duración:</span>
                                        <p class="text-gray-900 dark:text-gray-100">{{ $mantenimiento->fecha_inicio->diffInHours($mantenimiento->fecha_fin) }} horas</p>
                                    </div>
                                    @endif
                                </div>

                                <!-- Observaciones -->
                                @if($mantenimiento->observaciones_despues)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">Observaciones:</span>
                                    <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $mantenimiento->observaciones_despues }}</p>
                                </div>
                                @endif

                                <!-- Materiales -->
                                @if($mantenimiento->materiales_utilizados)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <span class="font-medium text-gray-600 dark:text-gray-400">Materiales utilizados:</span>
                                    <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $mantenimiento->materiales_utilizados }}</p>
                                </div>
                                @endif

                                <!-- Botón Ver Detalle -->
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <a href="{{ route('produccion.mantenimientos.show', $mantenimiento) }}" 
                                       class="text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300 font-medium">
                                        Ver detalle completo →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">No hay mantenimientos en el historial</p>
                            <p class="text-gray-400 dark:text-gray-500">Los mantenimientos completados aparecerán aquí</p>
                        </div>
                    @endforelse

                    <!-- Paginación -->
                    @if($mantenimientos->hasPages())
                    <div class="mt-6">
                        {{ $mantenimientos->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
