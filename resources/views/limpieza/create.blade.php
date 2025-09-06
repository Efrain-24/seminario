<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Nuevo Registro de Limpieza</h2>
    </x-slot>
    <div class="py-8 max-w-2xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form action="{{ route('limpieza.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha</label>
                    <input type="date" name="fecha" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                </div>
                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Área</label>
                    <input type="text" name="area" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                </div>
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Descripción</label>
                    <textarea name="descripcion" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2"></textarea>
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
                <div>
                    <label for="protocolo_sanidad_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Protocolo de Sanidad</label>
                    <select name="protocolo_sanidad_id" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                        <option value="">Seleccione...</option>
                        @foreach($protocolos as $protocolo)
                            <option value="{{ $protocolo->id }}">{{ $protocolo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white">Guardar</button>
                    <a href="{{ route('limpieza.index') }}" class="px-4 py-2 rounded bg-gray-500 hover:bg-gray-600 text-white">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
