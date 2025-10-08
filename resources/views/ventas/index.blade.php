@extends('layouts.app')

@section('title', 'Gestión de Ventas')

@section('content')

<!-- Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Gestión de Ventas
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Administra las ventas de productos acuícolas
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('ventas.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Venta
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado</label>
                <select name="estado" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID cliente</label>
                <div class="relative">
                    <input type="text" name="buscar_cliente" id="buscar_cliente" placeholder="Buscar cliente..." 
                           class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <div id="resultados_busqueda" class="absolute z-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg mt-1 w-full hidden">
                        <!-- Resultados de búsqueda aparecerán aquí -->
                    </div>
                </div>
                <button type="button" onclick="window.location.href='{{ route('clientes.create') }}'" 
                        class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Crear Cliente
                </button>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Filtrar
                </button>
            </div>
        </form }
    </div>

    <!-- Tabla de Ventas -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Código / Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Cosecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Fecha Venta
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Cantidad
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Método Pago
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($ventas as $venta)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" 
                            onclick="window.location.href='{{ route('ventas.show', $venta) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $venta->codigo_venta }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $venta->cliente }}
                                    </div>
                                    @if($venta->telefono_cliente)
                                        <div class="text-xs text-gray-400">
                                            📞 {{ $venta->telefono_cliente }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($venta->cosechaParcial)
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $venta->cosechaParcial->lote->codigo_lote ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $venta->cosechaParcial->fecha->format('d/m/Y') }}
                                    </div>
                                @else
                                    <span class="text-gray-400">Sin cosecha</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $venta->fecha_venta->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ number_format($venta->cantidad_kg, 2) }} kg
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Q{{ number_format($venta->precio_kg, 2) }}/kg
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Q{{ number_format($venta->total, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $venta->estado_badge }}">
                                    {{ ucfirst($venta->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $venta->metodo_pago_badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $venta->metodo_pago)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <!-- Botones de Ticket -->
                                    <a href="{{ route('ventas.ticket.ver', $venta) }}" 
                                       target="_blank"
                                       class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300"
                                       onclick="event.stopPropagation()" 
                                       title="Ver Ticket">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('ventas.ticket.descargar', $venta) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                       onclick="event.stopPropagation()" 
                                       title="Descargar Ticket">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('ventas.edit', $venta) }}" 
                                       class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                       onclick="event.stopPropagation()" 
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @if($venta->estado === 'pendiente')
                                        <form action="{{ route('ventas.completar', $venta) }}" method="POST" class="inline"
                                              onclick="event.stopPropagation()">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                    title="Completar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <h3 class="mt-4 text-lg font-medium">No hay ventas registradas</h3>
                                <p class="mt-2">Comienza registrando tu primera venta.</p>
                                <div class="mt-6">
                                    <a href="{{ route('ventas.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Nueva Venta
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($ventas->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $ventas->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    document.getElementById('buscar_cliente').addEventListener('input', function() {
        const query = this.value;
        const resultados = document.getElementById('resultados_busqueda');
        if (query.length > 2) {
            fetch(`{{ url('/clientes/buscar') }}?q=${query}`)
                .then(response => response.json())
                .then(data => {
                    resultados.innerHTML = '';
                    resultados.classList.remove('hidden');
                    data.forEach(cliente => {
                        const div = document.createElement('div');
                        div.textContent = `${cliente.nombre} (${cliente.id})`;
                        div.classList.add('cursor-pointer', 'hover:bg-gray-200', 'dark:hover:bg-gray-600', 'p-2');
                        div.addEventListener('click', () => {
                            document.getElementById('buscar_cliente').value = cliente.nombre;
                            resultados.classList.add('hidden');
                        });
                        resultados.appendChild(div);
                    });
                });
        } else {
            resultados.classList.add('hidden');
        }
    });
</script>
@endsection
