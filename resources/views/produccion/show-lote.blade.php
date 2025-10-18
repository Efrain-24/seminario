<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalles del Lote: ') }} {{ $lote->codigo_lote }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('produccion.lotes') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Volver a Lotes
                </a>
                
                <a href="{{ route('produccion.lotes.seguimientos.ver', $lote->id) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Seguimiento
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes flash --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Información General del Lote -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Información General
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Código del Lote -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-blue-500 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Código</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $lote->codigo_lote }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Especie -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-green-500 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Especie</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $lote->especie }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-purple-500 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Estado</p>
                                    <div>
                                        @if($lote->estado === 'activo')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Activo
                                            </span>
                                        @elseif($lote->estado === 'trasladado')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                Trasladado
                                            </span>
                                        @elseif($lote->estado === 'cosechado')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                Cosechado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Inactivo
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fecha de Inicio -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-indigo-500 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Fecha de Inicio</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $lote->fecha_inicio->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $lote->fecha_inicio->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Unidad de Producción -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-orange-500 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Unidad de Producción</p>
                                    @if($lote->unidadProduccion)
                                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $lote->unidadProduccion->nombre }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $lote->unidadProduccion->codigo }} - {{ ucfirst($lote->unidadProduccion->tipo) }}</p>
                                    @else
                                        <p class="text-lg font-semibold text-gray-500 dark:text-gray-400">Sin asignar</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Días de vida -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-teal-500 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Días de Vida</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        @php
                                            $dias = $lote->fecha_inicio->diffInDays(now());
                                            $meses = floor($dias / 30);
                                            $diasRestantes = $dias % 30;
                                        @endphp
                                        @if($meses > 0)
                                            {{ $meses }}m {{ $diasRestantes }}d
                                        @else
                                            {{ $dias }} días
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas del Lote -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Población -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Estadísticas de Población
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Cantidad Inicial:</span>
                                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($lote->cantidad_inicial) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Cantidad Actual:</span>
                                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($lote->cantidad_actual) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Supervivencia:</span>
                                <span class="text-lg font-semibold {{ $lote->cantidad_inicial > 0 ? (($lote->cantidad_actual / $lote->cantidad_inicial) >= 0.9 ? 'text-green-600' : (($lote->cantidad_actual / $lote->cantidad_inicial) >= 0.7 ? 'text-yellow-600' : 'text-red-600')) : 'text-gray-600' }}">
                                    @if($lote->cantidad_inicial > 0)
                                        {{ number_format(($lote->cantidad_actual / $lote->cantidad_inicial) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Mortalidad:</span>
                                <span class="text-lg font-semibold text-red-600">{{ number_format($lote->cantidad_inicial - $lote->cantidad_actual) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biometría -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Datos Biométricos Iniciales
                        </h3>
                        <div class="space-y-4">
                            @if($lote->peso_promedio_inicial)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Peso Promedio Inicial:</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ number_format($lote->peso_promedio_inicial * 1000, 1) }}g 
                                        <span class="text-sm text-gray-500">({{ number_format($lote->peso_promedio_inicial, 3) }} kg)</span>
                                    </span>
                                </div>
                            @endif
                            
                            @if($lote->peso_promedio_actual && $lote->peso_promedio_actual != $lote->peso_promedio_inicial)
                                <div class="flex justify-between items-center bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">Peso Promedio Actual:</span>
                                    <span class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                        {{ number_format($lote->peso_promedio_actual * 1000, 1) }}g 
                                        <span class="text-sm">({{ number_format($lote->peso_promedio_actual, 3) }} kg)</span>
                                    </span>
                                </div>
                            @endif
                            
                            @if($lote->talla_promedio_inicial)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Talla Promedio Inicial:</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($lote->talla_promedio_inicial, 2) }} cm</span>
                                </div>
                            @endif

                            @if($lote->biomasa > 0)
                                <div class="flex justify-between items-center bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">Biomasa Actual:</span>
                                    <span class="text-lg font-semibold text-green-600 dark:text-green-400">{{ number_format($lote->biomasa, 2) }} kg</span>
                                </div>
                            @endif

                            @if(!$lote->peso_promedio_inicial && !$lote->talla_promedio_inicial)
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                                    No hay datos biométricos registrados
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Observaciones -->
            @if($lote->observaciones)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Observaciones
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <p class="text-gray-700 dark:text-gray-300">{{ $lote->observaciones }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Acciones Disponibles -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Acciones Disponibles
                    </h3>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('produccion.lotes.seguimiento.crear', $lote->id) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            📏 Registrar Seguimiento
                        </a>
                        
                        @if($lote->estado === 'activo')
                            <a href="{{ route('produccion.traslados.crear', $lote->id) }}" 
                               class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                🔄 Programar Traslado
                            </a>
                        @endif
                        
                        <a href="{{ route('produccion.lotes.edit', $lote) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            📝 Editar Información
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
