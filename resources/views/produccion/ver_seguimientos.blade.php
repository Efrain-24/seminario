<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Seguimientos del Lote: {{ $lote->codigo_lote }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('produccion.seguimiento.lotes') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Volver a Seguimientos
                </a>
                <a href="{{ route('produccion.lotes.seguimiento.crear', $lote->id) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Seguimiento
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Información del Lote -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Información del Lote
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Código</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $lote->codigo_lote }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Especie</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $lote->especie }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Cantidad Actual</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($lote->cantidad_actual) }} peces</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Unidad</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                @if($lote->unidadProduccion)
                                    {{ $lote->unidadProduccion->nombre }}
                                @else
                                    Sin asignar
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Seguimientos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                        Historial de Seguimientos ({{ $seguimientos->total() }})
                    </h3>

                    @if($seguimientos->count() > 0)
                        <div class="space-y-4">
                            @foreach($seguimientos as $seguimiento)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center space-x-4">
                                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                                @if($seguimiento->tipo_seguimiento === 'rutinario') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @elseif($seguimiento->tipo_seguimiento === 'muestreo') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($seguimiento->tipo_seguimiento === 'mortalidad') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @endif">
                                                {{ ucfirst($seguimiento->tipo_seguimiento) }}
                                            </span>
                                            <div>
                                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $seguimiento->fecha_seguimiento->format('d/m/Y') }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Registrado por {{ $seguimiento->usuario->name }} • 
                                                    {{ $seguimiento->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Datos del Seguimiento -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <!-- Población -->
                                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Población</h4>
                                            <div class="space-y-2">
                                                @if($seguimiento->cantidad_actual)
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">Cantidad:</span>
                                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($seguimiento->cantidad_actual) }}</span>
                                                    </div>
                                                @endif
                                                @if($seguimiento->mortalidad > 0)
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">Mortalidad:</span>
                                                        <span class="text-sm font-semibold text-red-600">{{ number_format($seguimiento->mortalidad) }}</span>
                                                    </div>
                                                @endif
                                                @if(!$seguimiento->cantidad_actual && $seguimiento->mortalidad == 0)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin datos de población</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Biometría -->
                                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Biometría</h4>
                                            <div class="space-y-2">
                                                @if($seguimiento->peso_promedio)
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">Peso promedio:</span>
                                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $seguimiento->peso_promedio_gramos }}g ({{ $seguimiento->peso_promedio }}kg)</span>
                                                    </div>
                                                @endif
                                                @if($seguimiento->talla_promedio)
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">Talla promedio:</span>
                                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $seguimiento->talla_promedio }}cm</span>
                                                    </div>
                                                @endif
                                                @if($seguimiento->biomasa > 0)
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">Biomasa:</span>
                                                        <span class="text-sm font-semibold text-blue-600">{{ number_format($seguimiento->biomasa, 1) }}kg</span>
                                                    </div>
                                                @endif
                                                @if(!$seguimiento->peso_promedio && !$seguimiento->talla_promedio)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin datos biométricos</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Parámetros Ambientales -->
                                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Parámetros Ambientales</h4>
                                            <div class="space-y-2">
                                                @if($seguimiento->temperatura_agua)
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">Temperatura:</span>
                                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $seguimiento->temperatura_agua }}°C</span>
                                                    </div>
                                                @endif
                                                @if($seguimiento->ph_agua)
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">pH:</span>
                                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $seguimiento->ph_agua }}</span>
                                                    </div>
                                                @endif
                                                @if($seguimiento->oxigeno_disuelto)
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">Oxígeno:</span>
                                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $seguimiento->oxigeno_disuelto }}mg/L</span>
                                                    </div>
                                                @endif
                                                @if(!$seguimiento->temperatura_agua && !$seguimiento->ph_agua && !$seguimiento->oxigeno_disuelto)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin parámetros ambientales</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Observaciones -->
                                    @if($seguimiento->observaciones)
                                        <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Observaciones</h4>
                                            <p class="text-sm text-blue-700 dark:text-blue-300">{{ $seguimiento->observaciones }}</p>
                                        </div>
                                    @endif

                                    <!-- Botón Eliminar -->
                                    <form method="POST" action="{{ route('seguimientos.destroy', $seguimiento->id) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este seguimiento?');" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium">Eliminar</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginación -->
                        <div class="mt-6">
                            {{ $seguimientos->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sin seguimientos</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Este lote aún no tiene seguimientos registrados.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('produccion.lotes.seguimiento.crear', $lote->id) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Crear Primer Seguimiento
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
