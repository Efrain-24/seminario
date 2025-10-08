<x-app-layout>
    <x-slot name=        </div>
    </x-slot>
    
    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-12">der">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('proveedores.index') }}" class="mr-4 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar Proveedor
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Modificar información de <strong>{{ $proveedor->nombre }}</strong>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            {{ $proveedor->codigo_formateado }}
                        </span>
                    </p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('proveedores.show', $proveedor) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Ver Detalles
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alertas --}}
            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg flex items-center mb-6" role="alert">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-center mb-6" role="alert">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Información del proveedor actual --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Estado Actual</h3>
                            @if($proveedor->estado == 'activo')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                    Activo
                                </span>
                            @elseif($proveedor->estado == 'inactivo')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                    Inactivo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                    {{ ucfirst($proveedor->estado) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Registrado</h3>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $proveedor->created_at->format('d/m/Y H:i') }}</p>
                            @if($proveedor->created_by)
                                <p class="text-xs text-gray-500 dark:text-gray-400">por Admin</p>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Última Actualización</h3>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $proveedor->updated_at->format('d/m/Y H:i') }}</p>
                            @if($proveedor->updated_by)
                                <p class="text-xs text-gray-500 dark:text-gray-400">por Admin</p>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Total Compras</h3>
                            <p class="text-sm font-bold text-green-600 dark:text-green-400">
                                {{ $proveedor->moneda_preferida }} {{ number_format($proveedor->total_compras_historico, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <form method="POST" action="{{ route('proveedores.update', $proveedor) }}" novalidate>
                @csrf
                @method('PUT')

                @include('proveedores._form')

                {{-- Botones de acción --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <div class="flex flex-wrap gap-4 items-center justify-between">
                        <div class="flex flex-wrap gap-3">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Actualizar Proveedor
                            </button>
                            <button type="button" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-colors duration-200" onclick="resetearFormulario()">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Deshacer Cambios
                            </button>
                        </div>
                        <div>
                            <a href="{{ route('proveedores.show', $proveedor) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Sección de acciones administrativas --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-red-200 dark:border-red-700">
                <div class="px-6 py-4 border-b border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-900/20">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        Acciones Administrativas
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-base font-medium text-yellow-800 dark:text-yellow-200 mb-2">Cambiar Estado</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Cambiar el estado del proveedor sin editar otros datos.</p>
                            <button type="button" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-colors duration-200" onclick="mostrarCambioEstado()">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Cambiar Estado
                            </button>
                        </div>
                        <div>
                            <h4 class="text-base font-medium text-red-800 dark:text-red-200 mb-2">Eliminar Proveedor</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Eliminar permanentemente este proveedor del sistema.</p>
                            <button type="button" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-200" onclick="confirmarEliminacion()">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Eliminar Proveedor
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modales --}}
            <div x-data="{ open: false }" 
                 x-show="open" 
                 @keydown.escape.window="open = false"
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" @click="open = false">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form method="POST" action="{{ route('proveedores.destroy', $proveedor) }}">
                            @csrf
                            @method('DELETE')
                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                            Confirmar Eliminación
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                ¿Estás seguro de que deseas eliminar al proveedor <strong>{{ $proveedor->nombre }}</strong>? Esta acción no se puede deshacer.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Eliminar
                                </button>
                                <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-700">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function resetearFormulario() {
            if (confirm('¿Deseas deshacer todos los cambios y volver a los valores originales?')) {
                location.reload();
            }
        }

        function mostrarCambioEstado() {
            const modalElement = document.querySelector('[x-data*="cambioEstado"]');
            if (modalElement) {
                modalElement._x_dataStack[0].open = true;
            }
        }

        function confirmarEliminacion() {
            const modalElement = document.querySelector('[x-data*="eliminar"]');
            if (modalElement) {
                modalElement._x_dataStack[0].open = true;
            }
        }

        // Mostrar cambios pendientes
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[method="POST"]');
            if (!form) return;
            
            const originalData = new FormData(form);
            let changesPending = false;
            
            // Detectar cambios en el formulario
            form.addEventListener('input', function() {
                const currentData = new FormData(form);
                let hasChanges = false;
                
                for (let [key, value] of currentData.entries()) {
                    if (originalData.get(key) !== value) {
                        hasChanges = true;
                        break;
                    }
                }
                
                if (hasChanges !== changesPending) {
                    changesPending = hasChanges;
                    toggleUnsavedChangesIndicator(hasChanges);
                }
            });
            
            // Advertir antes de salir si hay cambios pendientes
            window.addEventListener('beforeunload', function(e) {
                if (changesPending) {
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            });
        });

        function toggleUnsavedChangesIndicator(show) {
            const submitButton = document.querySelector('button[type="submit"]');
            if (show) {
                submitButton.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                submitButton.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                submitButton.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>Guardar Cambios Pendientes';
            } else {
                submitButton.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                submitButton.classList.add('bg-blue-500', 'hover:bg-blue-600');
                submitButton.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>Actualizar Proveedor';
            }
        }
    </script>
</x-app-layout>