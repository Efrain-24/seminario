<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('proveedores.index') }}" class="mr-4 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nuevo Proveedor
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Registra un nuevo proveedor en el sistema</p>
                </div>
            </div>
            <div>
                <a href="{{ route('proveedores.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver a Lista
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alertas --}}
            @if(session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-center" role="alert">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Formulario --}}
            <form method="POST" action="{{ route('proveedores.store') }}" novalidate class="space-y-6">
                @csrf

                @include('proveedores._form')

                {{-- Botones de acción --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h2m0 0V9a2 2 0 012-2h2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v2M9 7h6"></path>
                                    </svg>
                                    Guardar Proveedor
                                </button>
                                <button type="button" class="inline-flex items-center px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300" onclick="limpiarFormulario()">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Limpiar Formulario
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('proveedores.index') }}" class="inline-flex items-center px-4 py-3 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-300 transition-colors duration-200 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function limpiarFormulario() {
            if (confirm('¿Estás seguro de que deseas limpiar todos los campos del formulario?')) {
                // Limpiar todos los inputs de texto
                document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="url"], input[type="number"], textarea').forEach(function(input) {
                    input.value = '';
                });
                
                // Resetear selects a su primer opción
                document.querySelectorAll('select').forEach(function(select) {
                    select.selectedIndex = 0;
                });
                
                // Desmarcar checkboxes
                document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
                    checkbox.checked = false;
                });
                
                // Reestablecer valores por defecto específicos
                const estadoSelect = document.getElementById('estado');
                if (estadoSelect) estadoSelect.value = 'activo';
                
                const monedaSelect = document.getElementById('moneda_preferida');
                if (monedaSelect) monedaSelect.value = 'GTQ';
                
                const pagoSelect = document.getElementById('forma_pago_preferida');
                if (pagoSelect) pagoSelect.value = 'contado';
                
                const diasCredito = document.getElementById('dias_credito');
                if (diasCredito) diasCredito.value = '0';
                
                const aceptaDevoluciones = document.getElementById('acepta_devoluciones');
                if (aceptaDevoluciones) aceptaDevoluciones.checked = true;
                
                // Actualizar info de tipo y categoría si existen las funciones
                if (typeof actualizarTipoInfo === 'function') actualizarTipoInfo();
                if (typeof actualizarCategoriaInfo === 'function') actualizarCategoriaInfo();
                
                // Quitar clases de validación de error
                document.querySelectorAll('.border-red-300, .border-red-500').forEach(function(element) {
                    element.classList.remove('border-red-300', 'border-red-500');
                    element.classList.add('border-gray-300', 'dark:border-gray-600');
                });
                
                // Mostrar mensaje de confirmación
                showNotification('Formulario limpiado correctamente', 'success');
            }
        }

        function showNotification(message, type = 'info') {
            // Crear elemento de notificación
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full ${
                type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' :
                type === 'error' ? 'bg-red-50 text-red-800 border border-red-200' :
                'bg-blue-50 text-blue-800 border border-blue-200'
            }`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success' ? 
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        }
                    </svg>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animar entrada
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Remover después de 3 segundos
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Validación en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(function(field) {
                field.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.classList.remove('border-gray-300', 'dark:border-gray-600');
                        this.classList.add('border-red-300', 'focus:border-red-500');
                    } else {
                        this.classList.remove('border-red-300', 'focus:border-red-500');
                        this.classList.add('border-gray-300', 'dark:border-gray-600');
                    }
                });
                
                field.addEventListener('input', function() {
                    if (this.classList.contains('border-red-300') && this.value.trim() !== '') {
                        this.classList.remove('border-red-300', 'focus:border-red-500');
                        this.classList.add('border-gray-300', 'dark:border-gray-600');
                    }
                });
            });
            
            // Validación del email
            const emailField = document.getElementById('email');
            if (emailField) {
                emailField.addEventListener('blur', function() {
                    if (this.value && !this.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                        this.classList.remove('border-gray-300', 'dark:border-gray-600');
                        this.classList.add('border-red-300', 'focus:border-red-500');
                    }
                });
            }
            
            // Validación de la URL del sitio web
            const sitioWebField = document.getElementById('sitio_web');
            if (sitioWebField) {
                sitioWebField.addEventListener('blur', function() {
                    if (this.value && !this.value.match(/^https?:\/\/.+/)) {
                        this.classList.remove('border-gray-300', 'dark:border-gray-600');
                        this.classList.add('border-red-300', 'focus:border-red-500');
                    }
                });
            }

            // Validación antes del envío
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                requiredFields.forEach(function(field) {
                    if (field.value.trim() === '') {
                        field.classList.remove('border-gray-300', 'dark:border-gray-600');
                        field.classList.add('border-red-300', 'focus:border-red-500');
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    showNotification('Por favor, completa todos los campos requeridos', 'error');
                    
                    // Scroll al primer campo con error
                    const firstError = form.querySelector('.border-red-300');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                }
            });
        });
    </script>
</x-app-layout>