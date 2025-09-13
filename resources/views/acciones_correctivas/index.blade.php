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
        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 shadow rounded p-4 mb-4">
            <form method="GET" action="{{ route('acciones_correctivas.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar</label>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" 
                           placeholder="Título, descripción..." 
                           class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                    <select name="estado" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_progreso" {{ request('estado') == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Responsable</label>
                    <select name="responsable" class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                        <option value="">Todos los responsables</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ request('responsable') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 rounded bg-gray-600 hover:bg-gray-700 text-white">
                        Filtrar
                    </button>
                    <a href="{{ route('acciones_correctivas.index') }}" class="px-4 py-2 rounded bg-gray-400 hover:bg-gray-500 text-white">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

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
                        <th class="px-4 py-2 text-left">Fecha Prevista</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-left">Evidencias</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($acciones as $accion)
                        <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" 
                            onclick="window.location.href='{{ route('acciones_correctivas.show', $accion) }}'">
                            <td class="px-4 py-2">{{ $accion->id }}</td>
                            <td class="px-4 py-2">{{ $accion->titulo }}</td>
                            <td class="px-4 py-2">{{ $accion->descripcion }}</td>
                            <td class="px-4 py-2">{{ $accion->responsable ? $accion->responsable->name : '-' }}</td>
                            <td class="px-4 py-2">{{ $accion->fecha_prevista }}</td>
                            <td class="px-4 py-2">
                                <div class="relative inline-block">
                                    <!-- Estado Actual -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $accion->estado == 'pendiente' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                        {{ $accion->estado == 'en_progreso' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                        {{ $accion->estado == 'completada' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ $accion->estado == 'cancelada' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                        @if($accion->estado == 'pendiente') Pendiente
                                        @elseif($accion->estado == 'en_progreso') En Progreso  
                                        @elseif($accion->estado == 'completada') Completada
                                        @elseif($accion->estado == 'cancelada') Cancelada
                                        @endif
                                    </span>
                                    
                                    <!-- Selector simple de estado -->
                                    <select onchange="cambiarEstado(this, {{ $accion->id }})" class="ml-1 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200" onclick="event.stopPropagation()">
                                        <option value="">Cambiar...</option>
                                        @if($accion->estado != 'pendiente')
                                            <option value="pendiente">Pendiente</option>
                                        @endif
                                        @if($accion->estado != 'en_progreso')
                                            <option value="en_progreso">En Progreso</option>
                                        @endif
                                        @if($accion->estado != 'completada')
                                            <option value="completada">Completada</option>
                                        @endif
                                        @if($accion->estado != 'cancelada')
                                            <option value="cancelada">Cancelada</option>
                                        @endif
                                    </select>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                @if($accion->evidencias && count($accion->evidencias) > 0)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ count($accion->evidencias) }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $acciones->links() }}
        </div>
    </div>

    <script>
        function cambiarEstado(select, accionId) {
            const nuevoEstado = select.value;
            if (!nuevoEstado) return;
            
            // Crear y enviar formulario
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/acciones_correctivas/${accionId}/cambiar-estado`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';
            
            const estadoField = document.createElement('input');
            estadoField.type = 'hidden';
            estadoField.name = 'estado';
            estadoField.value = nuevoEstado;
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            form.appendChild(estadoField);
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</x-app-layout>
