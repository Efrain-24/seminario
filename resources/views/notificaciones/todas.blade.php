<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                Todas las Notificaciones
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $notificaciones->total() }} notificaciones
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4">
        @if($notificaciones->count() > 0)
            <!-- Información del sistema automático -->
            <div class="mb-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Las notificaciones se eliminan automáticamente cuando los problemas se solucionan.
                </p>
            </div>
            
            <div class="space-y-4">
                @foreach($notificaciones as $notificacion)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-start space-x-4">
                                <!-- Icono -->
                                <div class="flex-shrink-0 mt-1">
                                    @php
                                        $iconColors = [
                                            'error' => 'text-red-500',
                                            'warning' => 'text-yellow-500', 
                                            'success' => 'text-green-500',
                                            'info' => 'text-blue-500'
                                        ];
                                        $iconColor = $iconColors[$notificacion->tipo] ?? $iconColors['info'];
                                    @endphp
                                    <i data-lucide="{{ $notificacion->icono ?? 'info' }}" 
                                       class="w-5 h-5 {{ $iconColor }}"></i>
                                </div>

                                <!-- Contenido -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                                {{ $notificacion->titulo }}
                                            </h3>
                                            
                                            <!-- Badge de tipo -->
                                            @php
                                                $badgeClasses = [
                                                    'error' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300',
                                                    'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
                                                    'success' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300',
                                                    'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300'
                                                ];
                                                $badgeClass = $badgeClasses[$notificacion->tipo] ?? $badgeClasses['info'];
                                                
                                                $typeLabels = [
                                                    'error' => 'Error',
                                                    'warning' => 'Alerta', 
                                                    'success' => 'Éxito',
                                                    'info' => 'Info'
                                                ];
                                                $typeLabel = $typeLabels[$notificacion->tipo] ?? 'Info';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mb-2 {{ $badgeClass }}">
                                                {{ $typeLabel }}
                                            </span>
                                            
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                                {{ $notificacion->mensaje }}
                                            </p>

                                            <!-- Enlace si existe -->
                                            @if($notificacion->url)
                                                <div class="mb-3">
                                                    <a href="{{ $notificacion->url }}" 
                                                       class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                                        <i data-lucide="external-link" class="w-4 h-4 mr-1"></i>
                                                        Ver detalles
                                                    </a>
                                                </div>
                                            @endif

                                            <!-- Información adicional -->
                                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 space-x-4">
                                                <span class="flex items-center">
                                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                                    {{ $notificacion->created_at->diffForHumans() }}
                                                </span>
                                                @php
                                                    $tipoAlerta = data_get($notificacion->datos, 'tipo_alerta');
                                                @endphp
                                                @if($notificacion->fecha_vencimiento && ($tipoAlerta !== 'sin_seguimiento'))
                                                    <span class="flex items-center">
                                                        <i data-lucide="calendar" class="w-3 h-3 mr-1"></i>
                                                        Vence: {{ $notificacion->fecha_vencimiento->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Acciones -->
                                        <div class="flex items-center space-x-2 ml-4">
                                            @if(!$notificacion->leida)
                                                <!-- Marcar como leída -->
                                                <form method="POST" action="{{ route('notificaciones.marcar-leida', $notificacion) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors"
                                                            title="Marcar como leída">
                                                        <i data-lucide="check" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <div class="flex items-center text-green-600 dark:text-green-400">
                                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                </div>
                                            @endif
                                            
                                            <!-- Marcar como resuelta (problema solucionado) -->
                                            <form method="POST" action="{{ route('notificaciones.marcar-resuelta', $notificacion) }}" 
                                                  class="inline" 
                                                  onsubmit="return confirm('¿Estás seguro de que este problema ya fue resuelto? La notificación cambiará de estado.')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                                        title="Marcar como problema resuelto">
                                                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-6">
                {{ $notificaciones->links() }}
            </div>
        @else
            <!-- Estado vacío -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                <div class="mx-auto max-w-md">
                    <i data-lucide="check-circle" class="w-16 h-16 mx-auto mb-4 text-green-500"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        ¡Todo al día!
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        No tienes notificaciones pendientes en este momento.
                    </p>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Inicializar iconos de Lucide
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
    @endpush
</x-app-layout>
