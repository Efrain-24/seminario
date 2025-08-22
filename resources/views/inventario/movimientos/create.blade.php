<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            Nuevo movimiento — {{ ucfirst($tipo) }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto px-4">
        @if ($errors->any())
            <div class="mb-4 rounded p-3 bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200">
                Revisa los campos marcados.
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow rounded border border-gray-200 dark:border-gray-700 p-4">
            <form method="POST" action="{{ route('produccion.inventario.movimientos.store') }}" class="grid gap-4">
                @csrf
                <input type="hidden" name="tipo" value="{{ $tipo }}">

                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Ítem</label>
                    <select name="item_id"
                        class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        required>
                        @foreach ($items as $i)
                            <option value="{{ $i->id }}">{{ $i->nombre }} — unidad: {{ $i->unidad_base }}
                            </option>
                        @endforeach
                    </select>
                    @error('item_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Bodega</label>
                    <select name="bodega_id"
                        class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        required>
                        @foreach ($bodegas as $b)
                            <option value="{{ $b->id }}">{{ $b->nombre }}</option>
                        @endforeach
                    </select>
                    @error('bodega_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Fecha</label>
                    <input type="date" name="fecha" value="{{ now()->toDateString() }}"
                        class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        required>
                    @error('fecha')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                @if ($tipo !== 'ajuste')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Cantidad</label>
                            <input type="number" step="0.0001" min="0.0001" name="cantidad"
                                class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                required>
                            @error('cantidad')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Unidad</label>
                            <select name="unidad"
                                class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                required>
                                <option value="kg">kg</option>
                                <option value="lb">lb</option>
                                <option value="unidad">unidad</option>
                                <option value="litro">litro</option>
                            </select>
                            @error('unidad')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @else
                    <div>
                        <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Nuevo stock (unidad
                            base)</label>
                        <input type="number" step="0.0001" name="nuevo_stock"
                            class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                            required>
                        @error('nuevo_stock')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label class="block text-xs mb-1 text-gray-600 dark:text-gray-300">Descripción</label>
                    <input name="descripcion"
                        class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    @error('descripcion')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('produccion.inventario.movimientos.index') }}"
                        class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600">Cancelar</a>
                    <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
