<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Compras de Insumos en Suministro/Insumo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Listado de Compras</h3>

                    @if($comprasInsumos->isEmpty())
                        <p class="text-gray-500">No se encontraron compras de insumos en la bodega suministro/insumo.</p>
                    @else
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Código</th>
                                    <th class="px-4 py-2">Producto</th>
                                    <th class="px-4 py-2">Cantidad</th>
                                    <th class="px-4 py-2">Costo Total</th>
                                    <th class="px-4 py-2">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comprasInsumos as $compra)
                                    <tr>
                                        <td class="border px-4 py-2">{{ $compra->codigo }}</td>
                                        <td class="border px-4 py-2">{{ $compra->detalle->producto ?? 'N/A' }}</td>
                                        <td class="border px-4 py-2">{{ $compra->detalle->cantidad ?? 'N/A' }}</td>
                                        <td class="border px-4 py-2">Q{{ number_format($compra->detalle->costo_total ?? 0, 2) }}</td>
                                        <td class="border px-4 py-2">{{ optional($compra->fecha)->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>