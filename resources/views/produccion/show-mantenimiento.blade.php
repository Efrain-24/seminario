<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalle del Mantenimiento') }}
                <span class="text-base font-normal text-gray-600 dark:text-gray-400">
                    - {{ $mantenimiento->unidadProduccion->nombre }}
                </span>
            </h2>
            <div class="flex space-x-3">
                @can('editar_mantenimientos')
                    @if($mantenimiento->estado_mantenimiento === 'programado')
                        <a href="{{ route('produccion.mantenimientos.edit', $mantenimiento) }}" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar
                        </a>
                    @endif
                @endcan
                <a href="{{ route('produccion.mantenimientos', $mantenimiento->unidadProduccion) }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones flotantes -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Información Principal -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ ucfirst($mantenimiento->tipo_mantenimiento) }}
                            </h3>
                            <div class="mt-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Estado:</span>
                                <span class="inline-block ml-2">
                                    @php
                                        $estados = [
                                            'asignado' => 'Asignado',
                                            'proceso' => 'En proceso',
                                            'terminado' => 'Terminado',
                                            'inconcluso' => 'Inconcluso',
                                        ];
                                        $estadoActual = $estados[$mantenimiento->estado] ?? ucfirst($mantenimiento->estado);
                                    @endphp
                                    <span class="bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 px-3 py-1 rounded-full text-sm font-semibold">
                                        {{ $estadoActual }}
                                    </span>
                                </span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                Creado el {{ $mantenimiento->created_at->format('d/m/Y \a \l\a\s H:i') }}
                            </p>
                        </div>
                        
                        <!-- Badge de Estado -->
                        <div class="flex flex-col items-end space-y-2">
                            @if($mantenimiento->estado_mantenimiento === 'programado')
                                <span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 text-sm font-semibold px-3 py-1 rounded-full">
                                    Programado
                                </span>
                            @elseif($mantenimiento->estado_mantenimiento === 'en_proceso')
                                <span class="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 text-sm font-semibold px-3 py-1 rounded-full">
                                    En Proceso
                                </span>
                            @elseif($mantenimiento->estado_mantenimiento === 'completado')
                                <span class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 text-sm font-semibold px-3 py-1 rounded-full">
                                    Completado
                                </span>
                            @elseif($mantenimiento->estado_mantenimiento === 'cancelado')
                                <span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 text-sm font-semibold px-3 py-1 rounded-full">
                                    Cancelado
                                </span>
                            @endif
                            
                            <!-- Badge de Prioridad -->
                            @if($mantenimiento->prioridad === 'critica')
                                <span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 text-xs font-medium px-2 py-1 rounded-full">
                                    CRÍTICA
                                </span>
                            @elseif($mantenimiento->prioridad === 'alta')
                                <span class="bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300 text-xs font-medium px-2 py-1 rounded-full">
                                    ALTA
                                </span>
                            @elseif($mantenimiento->prioridad === 'media')
                                <span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 text-xs font-medium px-2 py-1 rounded-full">
                                    MEDIA
                                </span>
                            @else
                                <span class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 text-xs font-medium px-2 py-1 rounded-full">
                                    BAJA
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Grid de Información -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <!-- Unidad de Producción -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Unidad de Producción</h4>
                            <p class="text-gray-700 dark:text-gray-300 font-medium">{{ $mantenimiento->unidadProduccion->nombre }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $mantenimiento->unidadProduccion->codigo }} - {{ ucfirst($mantenimiento->unidadProduccion->tipo) }}</p>
                        </div>

                        <!-- Responsable -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Responsable</h4>
                            <p class="text-gray-700 dark:text-gray-300 font-medium">{{ $mantenimiento->usuario->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $mantenimiento->usuario->email }}</p>
                        </div>

                        <!-- Fecha Programada -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Fecha Programada</h4>
                            <p class="text-gray-700 dark:text-gray-300 font-medium">{{ $mantenimiento->fecha_mantenimiento->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                @if($mantenimiento->fecha_mantenimiento->isPast() && $mantenimiento->estado_mantenimiento === 'programado')
                                    Atrasado ({{ $mantenimiento->fecha_mantenimiento->diffForHumans() }})
                                @else
                                    {{ $mantenimiento->fecha_mantenimiento->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Descripción del Trabajo -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Descripción del Trabajo</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $mantenimiento->descripcion_trabajo }}</p>
                        </div>
                    </div>

                    <!-- Observaciones Previas -->
                    @if($mantenimiento->observaciones_antes)
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Observaciones Previas</h4>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $mantenimiento->observaciones_antes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Requerimientos Especiales -->
                    @if($mantenimiento->requiere_vaciado || $mantenimiento->requiere_traslado_peces)
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Requerimientos Especiales</h4>
                        <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 p-4 rounded-lg">
                            <ul class="space-y-2">
                                @if($mantenimiento->requiere_vaciado)
                                <li class="flex items-center text-yellow-800 dark:text-yellow-300">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Requiere vaciado de la unidad
                                </li>
                                @endif
                                @if($mantenimiento->requiere_traslado_peces)
                                <li class="flex items-center text-yellow-800 dark:text-yellow-300">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Requiere traslado de peces
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    @endif

                    <!-- Insumos Agregados -->
                    @if($mantenimiento->insumos && $mantenimiento->insumos->count() > 0)
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Productos / Insumos</h4>
                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 p-4 rounded-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-blue-200 dark:border-blue-700">
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-blue-900 dark:text-blue-100">Producto</th>
                                            <th class="px-4 py-2 text-center text-sm font-semibold text-blue-900 dark:text-blue-100">Cantidad</th>
                                            <th class="px-4 py-2 text-center text-sm font-semibold text-blue-900 dark:text-blue-100">Unitario</th>
                                            <th class="px-4 py-2 text-right text-sm font-semibold text-blue-900 dark:text-blue-100">Total</th>
                                            @if($mantenimiento->estado_mantenimiento === 'en_proceso' || $mantenimiento->estado_mantenimiento === 'completado')
                                            <th class="px-4 py-2 text-center text-sm font-semibold text-blue-900 dark:text-blue-100">Estado</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalCosto = 0; @endphp
                                        @foreach($mantenimiento->insumos as $insumo)
                                            @php 
                                                $cantidad = $insumo->pivot->cantidad;
                                                $costo_unitario = $insumo->pivot->costo_unitario;
                                                $costo_total = $insumo->pivot->costo_total;
                                                $totalCosto += $costo_total;
                                            @endphp
                                            <tr class="border-b border-blue-100 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-800">
                                                <td class="px-4 py-3 text-blue-900 dark:text-blue-100">{{ $insumo->nombre }}</td>
                                                <td class="px-4 py-3 text-center text-blue-900 dark:text-blue-100">{{ $cantidad }} {{ $insumo->unidad_base }}</td>
                                                <td class="px-4 py-3 text-center text-blue-900 dark:text-blue-100">Q{{ number_format($costo_unitario, 2) }}</td>
                                                <td class="px-4 py-3 text-right text-blue-900 dark:text-blue-100 font-semibold">Q{{ number_format($costo_total, 2) }}</td>
                                                @if($mantenimiento->estado_mantenimiento === 'en_proceso' || $mantenimiento->estado_mantenimiento === 'completado')
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex items-center px-3 py-1 text-sm rounded-full font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                        <svg class="w-4 h-4 mr-1 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                                        Usado
                                                    </span>
                                                </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-blue-100 dark:bg-blue-800 font-semibold">
                                            <td colspan="3" class="px-4 py-3 text-right text-blue-900 dark:text-blue-100">Costo Total de Insumos:</td>
                                            <td class="px-4 py-3 text-right text-blue-900 dark:text-blue-100">Q{{ number_format($totalCosto, 2) }}</td>
                                            @if($mantenimiento->estado_mantenimiento === 'en_proceso' || $mantenimiento->estado_mantenimiento === 'completado')
                                            <td></td>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actividades para el técnico -->
                    @if($mantenimiento->actividades && count($mantenimiento->actividades) > 0)
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Actividades para el técnico</h4>
                        @php
                            $total_actividades = count($mantenimiento->actividades);
                            $actividades_completadas = 0;
                            if ($mantenimiento->actividades_ejecutadas) {
                                $actividades_completadas = count(array_filter($mantenimiento->actividades_ejecutadas, function($a) {
                                    return isset($a['completada']) && $a['completada'] === true;
                                }));
                            }
                            $porcentaje = $total_actividades > 0 ? round(($actividades_completadas / $total_actividades) * 100) : 0;
                        @endphp
                        <div class="bg-purple-50 dark:bg-purple-900 border border-purple-200 dark:border-purple-700 p-4 rounded-lg">
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-purple-900 dark:text-purple-100">Progreso: {{ $actividades_completadas }}/{{ $total_actividades }} completadas</span>
                                    <span class="text-sm font-bold text-purple-700 dark:text-purple-300">{{ $porcentaje }}%</span>
                                </div>
                                <div class="w-full bg-purple-200 rounded-full h-3 dark:bg-purple-700">
                                    <div class="bg-purple-600 h-3 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                @foreach($mantenimiento->actividades as $idx => $actividad)
                                <div class="flex items-center p-2 bg-white dark:bg-gray-800 rounded-lg">
                                    @if($mantenimiento->actividades_ejecutadas && isset($mantenimiento->actividades_ejecutadas[$idx]) && $mantenimiento->actividades_ejecutadas[$idx]['completada'])
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-900 dark:text-gray-100 line-through">{{ $actividad }}</span>
                                    @else
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $actividad }}</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Seguimiento y Resultados -->
            @if($mantenimiento->estado_mantenimiento === 'en_proceso' || $mantenimiento->estado_mantenimiento === 'completado')
            <!-- Actividades Ejecutadas (similar a limpieza) -->
            @php
                // Si el mantenimiento tiene actividades guardadas como array, mostrar el progreso
                $actividades = $mantenimiento->actividades ?? [];
                $actividadesEjecutadas = $mantenimiento->actividades_ejecutadas ?? [];
                $total = is_array($actividades) ? count($actividades) : 0;
                $completadas = 0;
                if (is_array($actividadesEjecutadas)) {
                    foreach ($actividades as $i => $actividad) {
                        if (isset($actividadesEjecutadas[$i]) && ($actividadesEjecutadas[$i]['estado'] ?? null) === 'completada') {
                            $completadas++;
                        }
                    }
                }
            @endphp
            @if($total > 0)
            <div class="mb-6">
                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Actividades Ejecutadas</h4>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <div class="mb-2 font-medium text-green-700 dark:text-green-300 flex items-center justify-between">
                        <span>Progreso: {{ $completadas }}/{{ $total }} actividades completadas</span>
                        <span class="ml-2 text-green-700 dark:text-green-300 font-bold">{{ $total > 0 ? number_format(($completadas/$total)*100, 0) : 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-600 mb-4">
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $total > 0 ? ($completadas/$total)*100 : 0 }}%"></div>
                    </div>
                    <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Actividad</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Estado</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($actividades as $i => $actividad)
                                <tr>
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $actividad['nombre'] ?? $actividad }}</td>
                                    <td class="px-4 py-2">
                                        @if(isset($actividadesEjecutadas[$i]) && ($actividadesEjecutadas[$i]['estado'] ?? null) === 'completada')
                                            <span class="inline-flex items-center px-3 py-1 text-sm rounded-full font-medium bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                                Completada
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 text-sm rounded-full font-medium bg-gray-100 text-gray-800">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $actividadesEjecutadas[$i]['observaciones'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @endif
            @if($mantenimiento->estado_mantenimiento === 'en_proceso' || $mantenimiento->estado_mantenimiento === 'completado')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Seguimiento y Resultados</h3>
                    
                    @if($mantenimiento->fecha_inicio)
                    <div class="mb-4">
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Fecha de inicio:</strong> {{ $mantenimiento->fecha_inicio->format('d/m/Y \a \l\a\s H:i') }}
                        </p>
                    </div>
                    @endif

                    @if($mantenimiento->fecha_fin)
                    <div class="mb-4">
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Fecha de finalización:</strong> {{ $mantenimiento->fecha_fin->format('d/m/Y \a \l\a\s H:i') }}
                        </p>
                        @if($mantenimiento->fecha_inicio)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Duración total: {{ $mantenimiento->fecha_inicio->diffInHours($mantenimiento->fecha_fin) }} horas
                        </p>
                        @endif
                    </div>
                    @endif

                    @if($mantenimiento->observaciones_despues)
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Observaciones del Trabajo Realizado</h4>
                        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $mantenimiento->observaciones_despues }}</p>
                        </div>
                    </div>
                    @endif

                    @if($mantenimiento->materiales_utilizados)
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Materiales Utilizados</h4>
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $mantenimiento->materiales_utilizados }}</p>
                        </div>
                    </div>
                    @endif

                    @if($mantenimiento->costo_mantenimiento)
                    <div class="mb-4">
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Costo del mantenimiento:</strong> Q{{ number_format($mantenimiento->costo_mantenimiento, 2) }}
                        </p>
                    </div>
                    @endif

                    @if($mantenimiento->proxima_revision)
                    <div class="mb-4">
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Próxima revisión sugerida:</strong> {{ $mantenimiento->proxima_revision->format('d/m/Y') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Acciones disponibles -->
            @if($mantenimiento->estado_mantenimiento !== 'completado' && $mantenimiento->estado_mantenimiento !== 'cancelado')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Acciones</h3>
                    
                    <div class="flex flex-wrap gap-4">
                        @if($mantenimiento->estado_mantenimiento === 'programado')
                            <form method="POST" action="{{ route('produccion.mantenimientos.iniciar', $mantenimiento) }}" class="inline">
                                @csrf
                                <input type="hidden" name="_method" value="PATCH">
                                <button type="submit" 
                                        onclick="return confirm('¿Estás seguro de que quieres iniciar este mantenimiento?')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Iniciar Mantenimiento
                                </button>
                            </form>
                        @endif

                        @if($mantenimiento->estado_mantenimiento === 'en_proceso')
                            <button onclick="openCompletarModal()" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Completar Mantenimiento
                            </button>
                        @endif

                        @if($mantenimiento->estado_mantenimiento !== 'completado')
                            <button onclick="openCancelarModal()" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'gerente')
            <!-- Eliminar Mantenimiento -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Eliminar Mantenimiento</h3>
                    
                    <form method="POST" action="{{ route('produccion.mantenimientos.eliminar', $mantenimiento->id) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este mantenimiento?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                            Eliminar Mantenimiento
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Modal para Completar Mantenimiento -->
    <div id="completarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Completar Mantenimiento</h3>
                <form method="POST" action="{{ route('produccion.mantenimientos.completar', $mantenimiento) }}" class="space-y-4 max-h-96 overflow-y-auto">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    
                    <!-- Resumen de Mantenimiento -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                        <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Unidad:</strong> {{ $mantenimiento->unidadProduccion->nombre }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Tipo:</strong> {{ ucfirst(str_replace('_', ' ', $mantenimiento->tipo_mantenimiento)) }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Descripción:</strong> {{ $mantenimiento->descripcion_trabajo }}</p>
                    </div>
                    
                    <!-- Debug Info -->
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        Insumos: {{ $mantenimiento->insumos ? $mantenimiento->insumos->count() : 0 }} | 
                        Actividades: {{ $mantenimiento->actividades ? count($mantenimiento->actividades) : 0 }}
                    </div>
                    
                    <!-- Checklist de Insumos -->
                    @if($mantenimiento->insumos && $mantenimiento->insumos->count() > 0)
                    <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-3">Productos / Insumos Utilizados</h4>
                        <div class="space-y-2">
                            @foreach($mantenimiento->insumos as $insumo)
                            <div class="flex items-center">
                                <input type="checkbox" id="insumo_{{ $insumo->id }}" 
                                       name="insumos_utilizados[]" 
                                       value="{{ $insumo->id }}"
                                       checked
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label for="insumo_{{ $insumo->id }}" class="ml-2 block text-sm text-blue-900 dark:text-blue-100">
                                    <span class="font-medium">{{ $insumo->nombre }}</span>
                                    <span class="text-blue-700 dark:text-blue-300">({{ $insumo->pivot->cantidad }} {{ $insumo->unidad_base }})</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mt-2">✓ Marca los productos que fueron utilizados en el mantenimiento</p>
                    </div>
                    @else
                    <div class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">No hay insumos/productos asociados a este mantenimiento</p>
                    </div>
                    @endif
                    
                    <!-- Checklist de Actividades -->
                    @if($mantenimiento->actividades && count($mantenimiento->actividades) > 0)
                    <div class="bg-purple-50 dark:bg-purple-900 border border-purple-200 dark:border-purple-700 p-4 rounded-lg">
                        <h4 class="font-semibold text-purple-900 dark:text-purple-100 mb-3">Actividades Completadas</h4>
                        <div class="space-y-2">
                            @foreach($mantenimiento->actividades as $idx => $actividad)
                            <div class="flex items-center">
                                <input type="checkbox" id="actividad_{{ $idx }}" 
                                       name="actividades_completadas[]" 
                                       value="{{ $idx }}"
                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="actividad_{{ $idx }}" class="ml-2 block text-sm text-purple-900 dark:text-purple-100">
                                    {{ $actividad }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-purple-700 dark:text-purple-300 mt-2">✓ Marca las actividades que fueron completadas</p>
                    </div>
                    @else
                    <div class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">No hay actividades asociadas a este mantenimiento</p>
                    </div>
                    @endif
                    
                    <div>
                        <label for="observaciones_despues" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Observaciones del trabajo realizado
                        </label>
                        <textarea name="observaciones_despues" rows="4" 
                                  class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                                  placeholder="Describe el trabajo realizado, problemas encontrados, soluciones aplicadas..."></textarea>
                    </div>

                    <div>
                        <label for="materiales_utilizados" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Materiales utilizados
                        </label>
                        <textarea name="materiales_utilizados" rows="3" 
                                  class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                                  placeholder="Lista de materiales, repuestos, químicos utilizados..."></textarea>
                    </div>

                    <div>
                        <label for="costo_mantenimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Costo del mantenimiento (Q)
                        </label>
                        <input type="number" name="costo_mantenimiento" step="0.01" min="0" 
                               class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div>
                        <label for="proxima_revision" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Próxima revisión sugerida
                        </label>
                        <input type="date" name="proxima_revision" min="{{ now()->addDays(1)->format('Y-m-d') }}" 
                               class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>

                    <div class="flex justify-end space-x-4 pt-4">
                        <button type="button" onclick="closeCompletarModal()" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                            Completar Mantenimiento
                        </button>
                    </div>
                </form>

                <!-- Opciones de eliminación -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Eliminar Mantenimiento</h3>
                    <div class="flex flex-col md:flex-row md:space-x-4 space-y-2 md:space-y-0">
                        <form method="POST" action="{{ route('produccion.mantenimientos.eliminar', $mantenimiento) }}" onsubmit="return confirm('¿Seguro que deseas eliminar solo este mantenimiento?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">Eliminar solo este mantenimiento</button>
                        </form>
                        @if($mantenimiento->repeat_type && $mantenimiento->repeat_type !== 'none')
                        <form method="POST" action="{{ route('produccion.mantenimientos.eliminarCiclo', $mantenimiento) }}" onsubmit="return confirm('¿Seguro que deseas eliminar todos los mantenimientos relacionados de este ciclo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-6 rounded">Eliminar todos los relacionados</button>
                        </form>
                        @endif
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Cancelar Mantenimiento -->
    <div id="cancelarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Cancelar Mantenimiento</h3>
                <form method="POST" action="{{ route('produccion.mantenimientos.cancelar', $mantenimiento) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    
                    <div>
                        <label for="motivo_cancelacion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motivo de la cancelación *
                        </label>
                        <textarea name="motivo_cancelacion" rows="3" required
                                  class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500"
                                  placeholder="Explica por qué se cancela este mantenimiento..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4">
                        <button type="button" onclick="closeCancelarModal()" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">
                            Confirmar Cancelación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCompletarModal() {
            document.getElementById('completarModal').classList.remove('hidden');
        }
        
        function closeCompletarModal() {
            document.getElementById('completarModal').classList.add('hidden');
        }
        
        function openCancelarModal() {
            document.getElementById('cancelarModal').classList.remove('hidden');
        }
        
        function closeCancelarModal() {
            document.getElementById('cancelarModal').classList.add('hidden');
        }

        // Cerrar modales al hacer clic fuera
        document.getElementById('completarModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCompletarModal();
            }
        });
        
        document.getElementById('cancelarModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelarModal();
            }
        });
    </script>
</x-app-layout>
