<x-app-layout>    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">  <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Historial de Unidad de Producción
                <span class="text-base font-normal text-gray-600 dark:text-gray-400">- {{ $unidad->nombre }}</span>
            </h2>
            <a href="{{ route('unidades.show', ['unidad' => $unidad->id]) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-1.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center text-xs">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver a Detalle
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Resumen de la Unidad -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-16 w-16 rounded-full flex items-center justify-center text-white font-bold text-lg
                            {{ $unidad->tipo === 'tanque' ? 'bg-blue-500' : 
                               ($unidad->tipo === 'estanque' ? 'bg-green-500' : 
                               ($unidad->tipo === 'jaula' ? 'bg-purple-500' : 'bg-orange-500')) }}">
                            @if($unidad->tipo === 'tanque')
                                TQ
                            @elseif($unidad->tipo === 'estanque')
                                ES
                            @elseif($unidad->tipo === 'jaula')
                                JL
                            @else
                                SE
                            @endif
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $unidad->nombre }}</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $unidad->codigo }} - {{ ucfirst($unidad->tipo) }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $unidad->lotes->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Lotes Históricos</div>
                        </div>
                        <div class="bg-orange-50 dark:bg-orange-900/20 p-3 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $unidad->mantenimientos->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Mantenimientos</div>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $unidad->mantenimientos->where('estado_mantenimiento', 'completado')->count() }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Completados</div>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-3 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ now()->diffInDays($unidad->created_at) }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Días en Servicio</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de Lotes -->
            @if($unidad->lotes && $unidad->lotes->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Historial de Lotes</h3>
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
                                        Fecha Siembra
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Cantidad Inicial
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Estado
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($unidad->lotes as $lote)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $lote->codigo ?? 'L-' . $lote->id }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $lote->especie ?? 'No especificada' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $lote->fecha_siembra ? $lote->fecha_siembra->format('d/m/Y') : 'No registrada' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ number_format($lote->cantidad_inicial ?? 0) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ $lote->estado ?? 'Activo' }}
                                            </span>
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
            @if($unidad->mantenimientos && $unidad->mantenimientos->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Historial de Mantenimientos</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Fecha
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tipo
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Descripción
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Responsable
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Estado
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($unidad->mantenimientos as $mantenimiento)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $mantenimiento->fecha_mantenimiento ? $mantenimiento->fecha_mantenimiento->format('d/m/Y') : 'No registrada' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $mantenimiento->tipo_mantenimiento ?? 'General' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ Str::limit($mantenimiento->descripcion ?? 'Sin descripción', 50) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $mantenimiento->usuario->name ?? 'No asignado' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @php
                                                $estado = $mantenimiento->estado_mantenimiento ?? 'programado';
                                                $colorClass = $estado === 'completado' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                            ($estado === 'en_proceso' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                                            'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200');
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Mensaje si no hay historial -->
            @if((!$unidad->lotes || $unidad->lotes->isEmpty()) && (!$unidad->mantenimientos || $unidad->mantenimientos->isEmpty()))
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sin historial</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Esta unidad no tiene lotes o mantenimientos registrados aún.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>