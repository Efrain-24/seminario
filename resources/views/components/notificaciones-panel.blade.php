<div x-data="notificaciones()" x-init="init()" class="relative">
    <!-- Campana de notificaciones -->
    <button @click="toggle()" 
            class="relative p-2 text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:text-gray-500 dark:focus:text-gray-300 transition duration-150 ease-in-out">
        <i data-lucide="bell" class="w-5 h-5"></i>
        
        <!-- Badge contador -->
        <span x-show="count > 0" 
              x-text="count > 99 ? '99+' : count"
              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full min-w-[20px] h-5">
        </span>
    </button>

    <!-- Panel de notificaciones -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.outside="close()"
         class="absolute right-0 z-50 mt-2 w-[420px] bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 max-h-[500px] overflow-hidden"
         style="transform: translateX(-40px);">
        
        <!-- Header -->
        <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-750">
            <div class="flex items-center space-x-2">
                <i data-lucide="bell" class="w-4 h-4 text-gray-600 dark:text-gray-400"></i>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notificaciones</h3>
                <span x-show="count > 0" 
                      x-text="count"
                      class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                </span>
            </div>
        </div>

        <!-- Lista de notificaciones -->
        <div class="max-h-[400px] overflow-y-auto">
            <template x-if="notificaciones.length === 0">
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-3 text-green-500"></i>
                    <p class="text-sm font-medium mb-1">¡Todo al día!</p>
                    <p class="text-xs">No tienes notificaciones pendientes</p>
                </div>
            </template>

            <template x-for="notificacion in notificaciones" :key="notificacion.id">
                <div class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                     @click="toggleNotificacion(notificacion.id)">
                    <div class="p-3">
                        <div class="flex items-start space-x-3">
                            <!-- Icono -->
                            <div class="flex-shrink-0 mt-0.5">
                                <i :data-lucide="notificacion.icono || 'info'" 
                                   :class="getIconClasses(notificacion.tipo)"
                                   class="w-4 h-4"></i>
                            </div>

                            <!-- Contenido principal -->
                            <div class="flex-1 min-w-0">
                                <!-- Título y badge -->
                                <div class="flex items-start justify-between">
                                    <h4 class="text-xs font-semibold text-gray-900 dark:text-gray-100 leading-tight pr-2" 
                                       x-text="notificacion.titulo"></h4>
                                    <div class="flex items-center space-x-2">
                                        <span :class="getBadgeClasses(notificacion.tipo)"
                                              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0"
                                              x-text="getTipoLabel(notificacion.tipo)">
                                        </span>
                                        
                                        <!-- Botón para marcar como leída -->
                                        <button @click.stop="marcarComoLeida(notificacion.id)"
                                                class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors"
                                                title="Marcar como leída">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                        </button>
                                        
                                        <!-- Indicador de expandir/contraer -->
                                        <i :data-lucide="notificacionExpandida === notificacion.id ? 'chevron-up' : 'chevron-down'" 
                                           class="w-3 h-3 text-gray-400 flex-shrink-0"></i>
                                    </div>
                                </div>

                                <!-- Mensaje expandible -->
                                <div x-show="notificacionExpandida === notificacion.id" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     class="mt-2">
                                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed pr-4" 
                                       x-text="notificacion.mensaje"></p>
                                    
                                    <!-- Enlace si existe -->
                                    <template x-if="notificacion.url">
                                        <div class="mt-2">
                                            <a :href="notificacion.url" 
                                               @click.stop=""
                                               class="inline-flex items-center text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                                <i data-lucide="external-link" class="w-3 h-3 mr-1"></i>
                                                Ver detalles
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer con botón "Ver todas" -->
        <template x-if="notificaciones.length > 0">
            <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750">
                <div class="text-center">
                    <a href="{{ route('notificaciones.todas') }}" 
                       class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition-colors">
                        <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                        Ver todas las notificaciones
                    </a>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function notificaciones() {
    return {
        open: false,
        notificaciones: [],
        count: 0,
        loading: false,
        notificacionExpandida: null,

        init() {
            this.cargarNotificaciones();
            // Actualizar cada 30 segundos
            setInterval(() => {
                this.cargarNotificaciones();
            }, 30000);
        },

        toggle() {
            if (!this.open) {
                this.cargarNotificaciones();
            }
            this.open = !this.open;
            // Cerrar notificación expandida cuando se cierra el panel
            if (!this.open) {
                this.notificacionExpandida = null;
            }
        },

        close() {
            this.open = false;
            this.notificacionExpandida = null;
        },

        toggleNotificacion(id) {
            this.notificacionExpandida = this.notificacionExpandida === id ? null : id;
        },

        async cargarNotificaciones() {
            try {
                const response = await fetch('/notificaciones?limit=100');
                const data = await response.json();
                // Filtrar solo vigentes y no resueltas
                const activas = (data.notificaciones || []).filter(n => !n.resuelta && (!n.fecha_vencimiento || new Date(n.fecha_vencimiento) > new Date()));
                this.notificaciones = activas.slice(0, 5); // mostrar solo las primeras 5
                this.count = activas.length;
                // Reinicializar iconos de Lucide
                setTimeout(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 100);
            } catch (error) {
                console.error('Error cargando notificaciones:', error);
            }
        },

        async cargarCount() {
            try {
                const response = await fetch('/notificaciones/count');
                const data = await response.json();
                this.count = data.count;
            } catch (error) {
                console.error('Error cargando contador:', error);
            }
        },

        getIconClasses(tipo) {
            const classes = {
                'error': 'text-red-500',
                'warning': 'text-yellow-500',
                'success': 'text-green-500',
                'info': 'text-blue-500'
            };
            return classes[tipo] || classes['info'];
        },

        getBadgeClasses(tipo) {
            const classes = {
                'error': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300',
                'warning': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
                'success': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300',
                'info': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300'
            };
            return classes[tipo] || classes['info'];
        },

        getTipoLabel(tipo) {
            const labels = {
                'error': 'Error',
                'warning': 'Alerta',
                'success': 'Éxito',
                'info': 'Info'
            };
            return labels[tipo] || 'Info';
        },

        async marcarComoLeida(notificacionId) {
            try {
                const response = await fetch(`/notificaciones/${notificacionId}/marcar-leida`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    // Recargar las notificaciones
                    await this.cargarNotificaciones();
                } else {
                    console.error('Error marcando notificación como leída');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al marcar notificación como leída');
            }
        }
    };
}
</script>
