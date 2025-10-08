<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Ítems</h2>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-8 max-w-6xl mx-auto px-4">
        <div class="flex flex-col sm:flex-row gap-3 sm:items-end sm:justify-between mb-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Buscar</label>
                    <input name="q" value="{{ request('q') }}"
                        class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2"
                        placeholder="Nombre o SKU">
                </div>
                <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Filtrar</button>
            </form>

            <div class="flex gap-2">
                <a href="{{ route('produccion.inventario.items.create') }}"
                    class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                    + Nuevo ítem
                </a>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden border border-gray-200 dark:border-gray-700">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2">SKU</th>
                        <th class="px-4 py-2">Tipo</th>
                        <th class="px-4 py-2">Unidad</th>
                        <th class="px-4 py-2 text-right">Stock mínimo</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $it)
                        <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" 
                            onclick="window.location.href='{{ route('produccion.inventario.items.show', $it) }}'">
                            <td class="px-4 py-2">{{ $it->nombre }}</td>
                            <td class="px-4 py-2 text-center">{{ $it->sku ?? '—' }}</td>
                            <td class="px-4 py-2 text-center capitalize">{{ $it->tipo }}</td>
                            <td class="px-4 py-2 text-center">{{ $it->unidad_base }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($it->stock_minimo, 2) }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('produccion.inventario.items.edit', $it) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">Editar</a>
                                <form action="{{ route('produccion.inventario.items.destroy', $it) }}" method="POST"
                                    class="inline" onsubmit="return confirm('¿Eliminar ítem?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin ítems
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    </div>
</x-app-layout>
