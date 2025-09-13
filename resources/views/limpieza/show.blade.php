<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Detalle del Registro de Limpieza</h2>
    </x-slot>
    <div class="py-8 max-w-2xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha</label>
                    <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">{{ $limpieza->fecha }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Área</label>
                    <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                        <div class="flex items-center gap-2">
                            @if(str_starts_with($limpieza->area, 'Unidad:'))
                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"></path>
                                    <path d="M14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                <span class="font-medium">{{ $limpieza->area }}</span>
                            @elseif(str_starts_with($limpieza->area, 'Bodega:'))
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium">{{ $limpieza->area }}</span>
                            @else
                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium">{{ $limpieza->area }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Responsable</label>
                    <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">{{ $limpieza->responsable }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Estado de Ejecución</label>
                    <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                        <span class="inline-flex px-3 py-1 text-sm rounded-full font-medium
                            @if($limpieza->estado === 'completado') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                            @elseif($limpieza->estado === 'en_progreso') bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                            @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                            @if($limpieza->estado === 'no_ejecutado') No Ejecutado
                            @elseif($limpieza->estado === 'en_progreso') En Progreso
                            @else Completado @endif
                        </span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Protocolo de Sanidad</label>
                    <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">{{ $limpieza->protocoloSanidad->nombre_completo ?? '' }}</div>
                </div>

                <!-- Actividades Ejecutadas -->
                @if($limpieza->actividades_ejecutadas && count($limpieza->actividades_ejecutadas) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Actividades Ejecutadas</label>
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 p-4 space-y-3">
                            @php
                                $totalActividades = count($limpieza->actividades_ejecutadas);
                                $actividadesCompletadas = collect($limpieza->actividades_ejecutadas)->where('completada', true)->count();
                            @endphp
                            
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Progreso: {{ $actividadesCompletadas }}/{{ $totalActividades }} actividades completadas
                                </span>
                                <div class="flex items-center gap-2">
                                    <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $totalActividades > 0 ? ($actividadesCompletadas / $totalActividades) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ $totalActividades > 0 ? round(($actividadesCompletadas / $totalActividades) * 100) : 0 }}%
                                    </span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600">Actividad</th>
                                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600 w-24">Estado</th>
                                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-300 dark:border-gray-600 w-1/3">Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($limpieza->actividades_ejecutadas as $index => $actividad)
                                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $actividad['descripcion'] ?? 'Actividad sin descripción' }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if(isset($actividad['completada']) && $actividad['completada'])
                                                        <div class="flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                        <span class="text-xs text-green-600 font-medium">Completada</span>
                                                    @else
                                                        <div class="flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                        <span class="text-xs text-gray-500">Pendiente</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                    @if(isset($actividad['observaciones']) && !empty($actividad['observaciones']))
                                                        {{ $actividad['observaciones'] }}
                                                    @else
                                                        <span class="text-gray-400 italic">Sin observaciones</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if($limpieza->observaciones)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Observaciones Generales</label>
                        <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">{{ $limpieza->observaciones }}</div>
                    </div>
                @endif

                <div class="flex gap-2 mt-4">
                    @if($limpieza->puedeSerEditado())
                        <a href="{{ route('limpieza.edit', $limpieza) }}" class="px-4 py-2 rounded bg-yellow-500 hover:bg-yellow-600 text-white">Editar</a>
                    @else
                        <span class="px-4 py-2 rounded bg-gray-300 text-gray-500 cursor-not-allowed" title="No se puede editar un registro completado">Editar</span>
                    @endif
                    <a href="{{ route('limpieza.index') }}" class="px-4 py-2 rounded bg-gray-500 hover:bg-gray-600 text-white">Volver</a>
                    <a href="{{ route('protocolo-sanidad.index') }}" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Protocolos
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
