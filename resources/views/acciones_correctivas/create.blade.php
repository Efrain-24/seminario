<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Registrar Acción Correctiva</h2>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-8 max-w-2xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form action="{{ route('acciones_correctivas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Título</label>
                    <input type="text" name="titulo" id="titulo" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" value="{{ old('titulo') }}" required>
                    @error('titulo')
                        <div class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <div class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Responsable</label>
                    <select name="user_id" id="user_id" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                        <option value="">Seleccione un responsable</option>
                        @isset($usuarios)
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" {{ old('user_id') == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                    @error('user_id')
                        <div class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="fecha_prevista" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha Prevista</label>
                    <input type="date" name="fecha_prevista" id="fecha_prevista" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" value="{{ old('fecha_prevista', now()->format('Y-m-d')) }}" required>
                    @error('fecha_prevista')
                        <div class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="fecha_limite" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha Límite</label>
                    <input type="date" name="fecha_limite" id="fecha_limite" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" value="{{ old('fecha_limite') }}">
                    @error('fecha_limite')
                        <div class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Estado</label>
                    <select name="estado" id="estado" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2" required>
                        <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_progreso" {{ old('estado') == 'en_progreso' ? 'selected' : '' }}>En progreso</option>
                        <option value="completada" {{ old('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelada" {{ old('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('estado')
                        <div class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</div>
                    @enderror
                </div>


                <div class="flex gap-2 mt-4">
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white">Registrar</button>
                    <a href="{{ route('acciones_correctivas.index') }}" class="px-4 py-2 rounded bg-gray-500 hover:bg-gray-600 text-white">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
