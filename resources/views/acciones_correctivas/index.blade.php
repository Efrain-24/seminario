<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Acciones Correctivas</h2>
    </x-slot>
    <div class="py-8 max-w-7xl mx-auto px-4">
        @if (session('success'))
            <div class="mb-4 rounded p-3 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif
        <div class="flex flex-col sm:flex-row gap-3 sm:items-end sm:justify-between mb-4">
            <div></div>
            <a href="{{ route('acciones_correctivas.create') }}" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Nueva Acción</a>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Título</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Responsable</th>
                        <th class="px-4 py-2 text-left">Fecha Detectada</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($acciones as $accion)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $accion->id }}</td>
                            <td class="px-4 py-2">{{ $accion->titulo }}</td>
                            <td class="px-4 py-2">{{ $accion->descripcion }}</td>
                            <td class="px-4 py-2">{{ $accion->responsable ? $accion->responsable->name : '-' }}</td>
                            <td class="px-4 py-2">{{ $accion->fecha_detectada }}</td>
                            <td class="px-4 py-2">{{ ucfirst($accion->estado) }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('acciones_correctivas.show', $accion) }}" class="text-green-600 dark:text-green-400 hover:underline mr-2">Ver</a>
                                <a href="{{ route('acciones_correctivas.edit', $accion) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Editar</a>
                                <form action="{{ route('acciones_correctivas.destroy', $accion) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro de eliminar?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $acciones->links() }}
        </div>
    </div>
</x-app-layout>
