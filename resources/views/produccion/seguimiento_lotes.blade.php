<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                Seguimiento de Lotes
            </h2>
            <a href="{{ route('produccion.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver al Módulo
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Estadísticas Generales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-500 rounded-full p-3 mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Lotes</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $lotes->count() }}</p>
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
                                <p class="text-sm text-gray-500 dark:text-gray-400">Seguimientos Totales</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalSeguimientos }}</p>
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
                                <p class="text-sm text-gray-500 dark:text-gray-400">Esta Semana</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $seguimientosRecientes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Lotes con Seguimientos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                        Lotes y Sus Seguimientos Recientes
                    </h3>

                    @if($lotes->count() > 0)
                        <div class="space-y-6">
                            @foreach($lotes as $lote)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $lote->codigo_lote }}
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $lote->especie }} • 
                                                {{ number_format($lote->cantidad_actual) }} peces • 
                                                @if($lote->unidadProduccion)
                                                    {{ $lote->unidadProduccion->nombre }}
                                                @else
                                                    Sin unidad asignada
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('produccion.lotes.seguimiento.crear', $lote->id) }}" 
                                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                                                ➕ Realizar Seguimiento
                                            </a>
                                            <a href="{{ route('produccion.lotes.seguimientos.ver', $lote->id) }}" 
                                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                                                📊 Ver Todos
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Seguimientos Recientes -->
                                    @if($lote->seguimientos->count() > 0)
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                                Últimos Seguimientos ({{ $lote->seguimientos->count() }})
                                            </h5>
                                            <div class="space-y-2">
                                                @foreach($lote->seguimientos as $seguimiento)
                                                    <div class="flex justify-between items-center text-sm">
                                                        <div class="flex items-center space-x-3">
                                                            <span class="px-2 py-1 rounded text-xs font-medium
                                                                @if($seguimiento->tipo_seguimiento === 'rutinario') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                                @elseif($seguimiento->tipo_seguimiento === 'muestreo') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                                @elseif($seguimiento->tipo_seguimiento === 'mortalidad') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                                @endif">
                                                                {{ ucfirst($seguimiento->tipo_seguimiento) }}
                                                            </span>
                                                            <span class="text-gray-600 dark:text-gray-400">
                                                                {{ $seguimiento->fecha_seguimiento->format('d/m/Y') }}
                                                            </span>
                                                            @if($seguimiento->cantidad_actual)
                                                                <span class="text-gray-600 dark:text-gray-400">
                                                                    {{ number_format($seguimiento->cantidad_actual) }} peces
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                                            @if($seguimiento->peso_promedio)
                                                                <span>{{ $seguimiento->peso_promedio }}g</span>
                                                            @endif
                                                            @if($seguimiento->biomasa > 0)
                                                                <span>{{ number_format($seguimiento->biomasa, 1) }}kg</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                                    <strong>Sin seguimientos registrados.</strong> 
                                                    Este lote aún no tiene registros de seguimiento.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay lotes</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Crea tu primer lote para comenzar a hacer seguimientos.</p>
                            <div class="mt-6">
                                <a href="{{ route('produccion.lotes.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Crear Lote
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
