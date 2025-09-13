<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Nuevo Protocolo de Sanidad</h2>
    </x-slot>
    <div class="py-8 max-w-2xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form action="{{ route('protocolo-sanidad.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Nombre</label>
                    <input type="text" name="nombre" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                </div>
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Descripción</label>
                    <textarea name="descripcion" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2"></textarea>
                </div>
                <div>
                    <label for="fecha_implementacion" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha de Implementación</label>
                    <input type="date" name="fecha_implementacion" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                </div>
                <div>
                    <label for="responsable" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Responsable</label>
                    <select name="responsable" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                        <option value="">Seleccione...</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->name }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sección de Actividades del Checklist -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Actividades del Protocolo</label>
                    <div id="actividades-container" class="space-y-2">
                        <div class="flex gap-2 actividad-item">
                            <input type="text" name="actividades[]" placeholder="Descripción de la actividad" class="flex-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                            <button type="button" onclick="removeActividad(this)" class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="addActividad()" class="mt-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">
                        + Agregar Actividad
                    </button>
                </div>

                <div class="flex gap-2 mt-4">
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white">Guardar</button>
                    <a href="{{ route('protocolo-sanidad.index') }}" class="px-4 py-2 rounded bg-gray-500 hover:bg-gray-600 text-white">Cancelar</a>
                </div>

                <script>
                    function addActividad() {
                        const container = document.getElementById('actividades-container');
                        const div = document.createElement('div');
                        div.className = 'flex gap-2 actividad-item';
                        div.innerHTML = `
                            <input type="text" name="actividades[]" placeholder="Descripción de la actividad" class="flex-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                            <button type="button" onclick="removeActividad(this)" class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        `;
                        container.appendChild(div);
                    }

                    function removeActividad(button) {
                        const container = document.getElementById('actividades-container');
                        if (container.children.length > 1) {
                            button.closest('.actividad-item').remove();
                        }
                    }
                </script>
            </form>
        </div>
    </div>
</x-app-layout>
