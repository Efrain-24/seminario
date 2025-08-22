<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Editar bodega</h2>
    </x-slot>

    <div class="py-8 max-w-xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded border border-gray-200 dark:border-gray-700 p-4">
            <form method="POST" action="{{ route('produccion.inventario.bodegas.update', $bodega) }}" class="grid gap-4">
                @csrf @method('PUT')
                @include('inventario.bodegas._form', ['bodega' => $bodega])
                <div class="flex justify-end gap-2">
                    <a href="{{ route('produccion.inventario.bodegas.index') }}"
                        class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600">Cancelar</a>
                    <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
