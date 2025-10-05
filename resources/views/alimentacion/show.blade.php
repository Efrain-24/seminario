<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalles de Alimentación') }}
                <span class="text-base font-normal text-gray-600 dark:text-gray-400">
                    - {{ $alimentacion->fecha_alimentacion->format('d/m/Y H:i') }}
                </span>
            </h2>
            <div class="flex space-x-3">
                @can('alimentacion.update')
                    <a href="{{ route('alimentacion.edit', $alimentacion) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                @endcan
                <a href="{{ route('alimentacion.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Información Principal -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Alimentación #{{ $alimentacion->id }}
                            </h3>
                            <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $alimentacion->created_at->diffForHumans() }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Por {{ $alimentacion->createdBy->name }}
                                </div>
                                @if($alimentacion->updated_at != $alimentacion->created_at)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"></path>
                                        </svg>
                                        Actualizado {{ $alimentacion->updated_at->diffForHumans() }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Estado/Badge -->
                        @if($alimentacion->costo_total)
                            <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-3 py-1 rounded-full text-sm font-medium">
                                Costo: Q{{ number_format($alimentacion->costo_total, 2) }}
                            </div>
                        @endif
                    </div>

                    <!-- Grid de Información Principal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Lote -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Lote</div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $alimentacion->lote->codigo_lote }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $alimentacion->lote->unidadProduccion->nombre }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $alimentacion->lote->especie }}
                            </div>
                        </div>

                        <!-- Alimento -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tipo de Alimento</div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                @if($alimentacion->tipoAlimento)
                                    {{ $alimentacion->tipoAlimento->nombre_completo }}
                                @elseif($alimentacion->inventarioItem)
                                    {{ $alimentacion->inventarioItem->nombre }}
                                @else
                                    <span class="text-gray-400">Sin producto</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                @if($alimentacion->tipoAlimento)
                                    {{ ucfirst($alimentacion->tipoAlimento->categoria) }}
                                @elseif($alimentacion->inventarioItem && $alimentacion->inventarioItem->categoria)
                                    {{ ucfirst($alimentacion->inventarioItem->categoria) }}
                                @else
                                    <span class="text-gray-400">Sin categoría</span>
                                @endif
                            </div>
                            @if($alimentacion->tipoAlimento && $alimentacion->tipoAlimento->costo_por_kg)
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Q{{ $alimentacion->tipoAlimento->costo_por_kg }}/lbs
                                </div>
                            @elseif($alimentacion->inventarioItem && $alimentacion->inventarioItem->costo_unitario)
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Q{{ $alimentacion->inventarioItem->costo_unitario }}/{{ $alimentacion->inventarioItem->unidad_base }}
                                </div>
                            @else
                                <div class="text-xs text-gray-400">Sin costo registrado</div>
                            @endif
                        </div>

                        <!-- Fecha y Hora -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Fecha y Hora</div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $alimentacion->fecha_alimentacion->format('d/m/Y') }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $alimentacion->hora_alimentacion->format('H:i') }}
                            </div>
                        </div>

                        <!-- Cantidad -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Cantidad Suministrada (libras)</div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ number_format($alimentacion->cantidad_kg, 2) }} lbs
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $alimentacion->metodo_alimentacion_texto }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $alimentacion->frecuencia_diaria }}x al día
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parámetros del Agua y Estado de los Peces -->
            @if($alimentacion->temperatura_agua || $alimentacion->ph_agua || $alimentacion->oxigeno_disuelto || $alimentacion->estado_peces || $alimentacion->porcentaje_consumo)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Parámetros del Agua y Estado de los Peces</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                            @if($alimentacion->temperatura_agua)
                                <div class="text-center">
                                    <div class="bg-blue-100 dark:bg-blue-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $alimentacion->temperatura_agua }}°C</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Temperatura</div>
                                </div>
                            @endif

                            @if($alimentacion->ph_agua)
                                <div class="text-center">
                                    <div class="bg-green-100 dark:bg-green-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                    </div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $alimentacion->ph_agua }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">pH</div>
                                </div>
                            @endif

                            @if($alimentacion->oxigeno_disuelto)
                                <div class="text-center">
                                    <div class="bg-cyan-100 dark:bg-cyan-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-8 h-8 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                        </svg>
                                    </div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $alimentacion->oxigeno_disuelto }} mg/L</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Oxígeno Disuelto</div>
                                </div>
                            @endif

                            @if($alimentacion->estado_peces)
                                <div class="text-center">
                                    <div class="bg-orange-100 dark:bg-orange-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $alimentacion->estado_peces_texto }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Estado</div>
                                </div>
                            @endif

                            @if($alimentacion->porcentaje_consumo)
                                <div class="text-center">
                                    <div class="bg-purple-100 dark:bg-purple-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $alimentacion->porcentaje_consumo }}%</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Consumo</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Observaciones -->
            @if($alimentacion->observaciones)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Observaciones</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $alimentacion->observaciones }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Información Nutricional del Alimento -->
            @if(($alimentacion->tipoAlimento && ($alimentacion->tipoAlimento->proteina_porcentaje || $alimentacion->tipoAlimento->grasa_porcentaje || $alimentacion->tipoAlimento->fibra_porcentaje)) ||
                ($alimentacion->inventarioItem && ($alimentacion->inventarioItem->proteina_porcentaje || $alimentacion->inventarioItem->grasa_porcentaje || $alimentacion->inventarioItem->fibra_porcentaje)))
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Información Nutricional del Alimento</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @if(($alimentacion->tipoAlimento && $alimentacion->tipoAlimento->proteina_porcentaje) || ($alimentacion->inventarioItem && $alimentacion->inventarioItem->proteina_porcentaje))
                                <div class="text-center">
                                    <div class="bg-red-100 dark:bg-red-900 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <span class="text-red-600 dark:text-red-400 font-bold">P</span>
                                    </div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">Proteína</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        @if($alimentacion->tipoAlimento && $alimentacion->tipoAlimento->proteina_porcentaje)
                                            {{ $alimentacion->tipoAlimento->proteina_porcentaje }}%
                                        @else
                                            {{ $alimentacion->inventarioItem->proteina_porcentaje ?? 'N/A' }}%
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if(($alimentacion->tipoAlimento && $alimentacion->tipoAlimento->grasa_porcentaje) || ($alimentacion->inventarioItem && $alimentacion->inventarioItem->grasa_porcentaje))
                                <div class="text-center">
                                    <div class="bg-yellow-100 dark:bg-yellow-900 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <span class="text-yellow-600 dark:text-yellow-400 font-bold">G</span>
                                    </div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">Grasa</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        @if($alimentacion->tipoAlimento && $alimentacion->tipoAlimento->grasa_porcentaje)
                                            {{ $alimentacion->tipoAlimento->grasa_porcentaje }}%
                                        @else
                                            {{ $alimentacion->inventarioItem->grasa_porcentaje ?? 'N/A' }}%
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if(($alimentacion->tipoAlimento && $alimentacion->tipoAlimento->fibra_porcentaje) || ($alimentacion->inventarioItem && $alimentacion->inventarioItem->fibra_porcentaje))
                                <div class="text-center">
                                    <div class="bg-green-100 dark:bg-green-900 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <span class="text-green-600 dark:text-green-400 font-bold">F</span>
                                    </div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">Fibra</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        @if($alimentacion->tipoAlimento && $alimentacion->tipoAlimento->fibra_porcentaje)
                                            {{ $alimentacion->tipoAlimento->fibra_porcentaje }}%
                                        @else
                                            {{ $alimentacion->inventarioItem->fibra_porcentaje ?? 'N/A' }}%
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($alimentacion->tipoAlimento->descripcion)
                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $alimentacion->tipoAlimento->descripcion }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Acciones Adicionales -->
            @can('alimentacion.delete')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Zona de Peligro</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Una vez que elimines este registro de alimentación, toda la información se perderá permanentemente.</p>
                        
                        <form action="{{ route('alimentacion.destroy', $alimentacion) }}" method="POST" class="inline"
                              onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro de alimentación? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Eliminar Registro
                            </button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
