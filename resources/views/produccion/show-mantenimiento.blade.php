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
                           class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
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
                </div>
            </div>

            <!-- Seguimiento y Resultados -->
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
                            <strong>Costo del mantenimiento:</strong> ${{ number_format($mantenimiento->costo_mantenimiento, 2) }}
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

        </div>
    </div>

    <!-- Modal para Completar Mantenimiento -->
    <div id="completarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Completar Mantenimiento</h3>
                <form method="POST" action="{{ route('produccion.mantenimientos.completar', $mantenimiento) }}" class="space-y-4">
                    @csrf
                    
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
                            Costo del mantenimiento ($)
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
