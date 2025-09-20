<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalles de Unidad de Producción') }}
                <span class="text-base font-normal text-gray-600 dark:text-gray-400">- {{ $unidad->nombre }}</span>
            </h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('unidades.historial', $unidad) }}"
                   class="bg-gray-200 hover:bg-blue-600 hover:text-white text-gray-800 font-semibold py-1.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center text-xs">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Historial
                </a>
                <a href="{{ route('unidades.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-1.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center text-xs">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Volver a Unidades
                </a>
                <a href="{{ route('unidades.edit', $unidad) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center text-xs">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar Unidad
                </a>
                <form method="POST" action="{{ route('unidades.toggle-estado', $unidad) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="bg-{{ $unidad->estado === 'activa' ? 'red' : 'green' }}-600 hover:bg-{{ $unidad->estado === 'activa' ? 'red' : 'green' }}-700 text-white font-bold py-1.5 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center text-xs">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        {{ $unidad->estado === 'activa' ? 'Inhabilitar' : 'Habilitar' }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <!-- Notificaciones flotantes -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />

    <div class="py-4">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mb-8">
            <div class="bg-gradient-to-br from-blue-50 via-white to-green-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 shadow-xl rounded-2xl border border-blue-100 dark:border-gray-700">
                <div class="flex flex-col md:flex-row items-center gap-6 p-8">
                    <div class="flex-shrink-0">
                        <div class="h-28 w-28 rounded-full border-4 border-white shadow-lg flex items-center justify-center text-white font-bold text-2xl
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
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                            <div>
                                <h3 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mb-1">{{ $unidad->nombre }}</h3>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-base font-mono text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900 px-2 py-0.5 rounded">{{ $unidad->codigo }}</span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold 
                                        {{ $unidad->tipo === 'tanque' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                           ($unidad->tipo === 'estanque' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                           ($unidad->tipo === 'jaula' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 
                                           'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200')) }}">
                                        @if($unidad->tipo === 'sistema_especializado')
                                            Sistema Especializado
                                        @else
                                            {{ ucfirst($unidad->tipo) }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div>
                                @if($unidad->estado === 'activa')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 shadow">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" /></svg>
                                        Activa
                                    </span>
                                @elseif($unidad->estado === 'mantenimiento')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 shadow">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /></svg>
                                        En Mantenimiento
                                    </span>
                                @elseif($unidad->estado === 'inactiva')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 shadow">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12l6 6 6-6" /></svg>
                                        Inactiva
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 shadow">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2" /></svg>
                                        Retirada
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            @if($unidad->capacidad_maxima)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 dark:text-gray-400">Capacidad Máxima:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($unidad->capacidad_maxima, 0) }} L</span>
                            </div>
                            @endif
                            @if($unidad->area)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 dark:text-gray-400">Área:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($unidad->area, 2) }} m²</span>
                            </div>
                            @endif
                            @if($unidad->profundidad)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 dark:text-gray-400">Profundidad:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($unidad->profundidad, 2) }} m</span>
                            </div>
                            @endif
                            @if($unidad->fecha_construccion)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 dark:text-gray-400">Construcción:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $unidad->fecha_construccion->format('d/m/Y') }}</span>
                            </div>
                            @endif
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 dark:text-gray-400">Registro:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $unidad->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if(isset($estadisticas_mantenimiento) && $estadisticas_mantenimiento['total'] > 0)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 dark:text-gray-400">Mantenimientos:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $estadisticas_mantenimiento['completados'] }}/{{ $estadisticas_mantenimiento['total'] }} completados</span>
                            </div>
                            @endif
                        </div>
                        @if($unidad->descripcion)
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Descripción</h4>
                            <p class="text-gray-700 dark:text-gray-300">{{ $unidad->descripcion }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Lotes Activos -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($unidad->lotes && $unidad->lotes->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Lotes en esta Unidad</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $unidad->lotes->count() }} lotes registrados</span>
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
                                        Fecha Siembra
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
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $lote->fecha_siembra ? $lote->fecha_siembra->format('d/m/Y') : 'No registrada' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay lotes</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Esta unidad no tiene lotes asignados actualmente.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>