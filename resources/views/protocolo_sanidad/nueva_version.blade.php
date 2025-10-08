<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Crear Nueva Versión: {{ $protocoloSanidad->nombre }} (v{{ $protocoloSanidad->version }})
        </h2>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-8 max-w-2xl mx-auto px-4">
        <!-- Información de la versión actual -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Versión Actual</h3>
            <p class="text-sm text-blue-700 dark:text-blue-300">
                <strong>{{ $protocoloSanidad->nombre }}</strong> - Versión {{ $protocoloSanidad->version }} ({{ $protocoloSanidad->estado }})
            </p>
            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                Al crear una nueva versión, la versión actual será marcada como obsoleta y la nueva versión será la vigente.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form action="{{ route('protocolo-sanidad.guardar-nueva-version', $protocoloSanidad) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Nombre</label>
                    <input type="text" name="nombre" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" value="{{ $protocoloSanidad->nombre }}" required>
                </div>
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Descripción</label>
                    <textarea name="descripcion" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">{{ $protocoloSanidad->descripcion }}</textarea>
                </div>
                <div>
                    <label for="fecha_implementacion" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha de Implementación</label>
                    <input type="date" name="fecha_implementacion" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" value="{{ now()->toDateString() }}" required>
                </div>
                <div>
                    <label for="responsable" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Responsable</label>
                    <select name="responsable" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                        <option value="">Seleccione...</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->name }}" @if($protocoloSanidad->responsable == $usuario->name) selected @endif>{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Actividades -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Actividades del Protocolo</label>
                    <div id="actividades-container" class="space-y-2">
                        @if($protocoloSanidad->actividades && count($protocoloSanidad->actividades) > 0)
                            @foreach($protocoloSanidad->actividades as $index => $actividad)
                                <div class="actividad-item flex items-center gap-2">
                                    <input type="text" name="actividades[]" value="{{ $actividad }}" class="flex-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                                    <button type="button" onclick="eliminarActividad(this)" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" onclick="agregarActividad()" class="mt-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                        Agregar Actividad
                    </button>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                        Crear Nueva Versión
                    </button>
                    <a href="{{ route('protocolo-sanidad.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function agregarActividad() {
            const container = document.getElementById('actividades-container');
            const div = document.createElement('div');
            div.className = 'actividad-item flex items-center gap-2';
            div.innerHTML = `
                <input type="text" name="actividades[]" class="flex-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                <button type="button" onclick="eliminarActividad(this)" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            container.appendChild(div);
        }

        function eliminarActividad(button) {
            button.closest('.actividad-item').remove();
        }
    </script>
</x-app-layout>
