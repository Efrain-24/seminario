<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">Protocolos de Sanidad</h2>
    </x-slot>
    <div class="py-8 max-w-7xl mx-auto px-4">
        @if (session('success'))
            <div class="mb-4 rounded p-3 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif
        <!-- Botón para abrir filtros y filtros rápidos -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex gap-3">
                <!-- Botón para abrir modal de filtros -->
                <button onclick="abrirModalFiltros()" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtros
                    @if(request()->hasAny(['buscar', 'estado', 'responsable', 'fecha_desde', 'fecha_hasta', 'version']))
                        <span class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ collect(request()->only(['buscar', 'estado', 'responsable', 'fecha_desde', 'fecha_hasta', 'version']))->filter()->count() }}</span>
                    @endif
                </button>

                @if(request()->hasAny(['buscar', 'estado', 'responsable', 'fecha_desde', 'fecha_hasta', 'version']))
                    <a href="{{ route('protocolo-sanidad.index') }}" class="px-4 py-2 rounded bg-gray-500 hover:bg-gray-600 text-white flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Limpiar Filtros
                    </a>
                @endif

                <!-- Filtros rápidos -->
                <div class="flex gap-2 items-center">
                    <span class="text-xs text-gray-500 mr-2">Filtros rápidos:</span>
                    <button type="button" onclick="aplicarFiltroRapido('vigente')" class="px-3 py-1 text-xs rounded bg-green-100 hover:bg-green-200 text-green-700 transition-colors">Solo Vigentes</button>
                    <button type="button" onclick="aplicarFechaRapida(30)" class="px-3 py-1 text-xs rounded bg-blue-100 hover:bg-blue-200 text-blue-700 transition-colors">Últimos 30 días</button>
                </div>
            </div>

            <!-- Botones de navegación -->
            <div class="flex gap-2">
                <a href="{{ route('limpieza.index') }}" class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white">Ver Limpiezas</a>
                <a href="{{ route('protocolo-sanidad.create') }}" class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">Nuevo Protocolo</a>
            </div>
        </div>

        <!-- Modal de Filtros -->
        <div id="modalFiltros" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <!-- Header del modal -->
                <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filtros de Protocolos
                    </h3>
                    <button onclick="cerrarModalFiltros()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6m0 12L6 6"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Formulario de filtros -->
                <form method="GET" action="{{ route('protocolo-sanidad.index') }}" class="mt-4">
                    <div class="space-y-6">
                        <!-- Búsqueda general -->
                        <div>
                            <label for="buscar_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Búsqueda General
                            </label>
                            <input type="text" name="buscar" id="buscar_modal" value="{{ request('buscar') }}" 
                                   placeholder="Buscar en nombre, descripción, responsable..." 
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Filtros en grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Estado -->
                            <div>
                                <label for="estado_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                    Estado
                                </label>
                                <select name="estado" id="estado_modal" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Todos los estados</option>
                                    <option value="vigente" {{ request('estado') == 'vigente' ? 'selected' : '' }}>Vigente</option>
                                    <option value="obsoleta" {{ request('estado') == 'obsoleta' ? 'selected' : '' }}>Obsoleta</option>
                                </select>
                            </div>

                            <!-- Versión -->
                            <div>
                                <label for="version_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Versión
                                </label>
                                <select name="version" id="version_modal" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Todas las versiones</option>
                                    @foreach($versiones as $version)
                                        <option value="{{ $version }}" {{ request('version') == $version ? 'selected' : '' }}>
                                            Versión {{ $version }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Responsable -->
                            <div>
                                <label for="responsable_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Responsable
                                </label>
                                <select name="responsable" id="responsable_modal" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Todos los responsables</option>
                                    @foreach($responsables as $responsable)
                                        <option value="{{ $responsable }}" {{ request('responsable') == $responsable ? 'selected' : '' }}>
                                            {{ $responsable }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Filtros de fecha -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Rango de Fechas de Implementación
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="fecha_desde_modal" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Desde</label>
                                    <input type="date" name="fecha_desde" id="fecha_desde_modal" value="{{ request('fecha_desde') }}" 
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="fecha_hasta_modal" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
                                    <input type="date" name="fecha_hasta" id="fecha_hasta_modal" value="{{ request('fecha_hasta') }}" 
                                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones del modal -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="cerrarModalFiltros()" class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Aplicar Filtros
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Información de resultados y filtros activos -->
        <div class="flex flex-col sm:flex-row gap-3 sm:items-end sm:justify-between mb-4">
            <div class="flex items-center gap-4">
                <!-- Contador de resultados -->
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">{{ $protocolos->count() }}</span> 
                    {{ $protocolos->count() == 1 ? 'protocolo encontrado' : 'protocolos encontrados' }}
                </div>

                <!-- Filtros activos -->
                @if(request()->hasAny(['buscar', 'estado', 'responsable', 'fecha_desde', 'fecha_hasta', 'version']))
                    <div class="flex flex-wrap gap-2">
                        @if(request('buscar'))
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                Búsqueda: "{{ request('buscar') }}"
                            </span>
                        @endif
                        @if(request('estado'))
                            <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                                Estado: {{ ucfirst(request('estado')) }}
                            </span>
                        @endif
                        @if(request('version'))
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                Versión: {{ request('version') }}
                            </span>
                        @endif
                        @if(request('responsable'))
                            <span class="px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full">
                                Responsable: {{ request('responsable') }}
                            </span>
                        @endif
                        @if(request('fecha_desde') || request('fecha_hasta'))
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                Fechas: {{ request('fecha_desde') ?: '...' }} - {{ request('fecha_hasta') ?: '...' }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow rounded overflow-hidden">
            <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Versión</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-left">Fecha de Implementación</th>
                        <th class="px-4 py-2 text-left">Responsable</th>
                        <th class="px-4 py-2 text-left">Actividades</th>
                        <th class="px-4 py-2 text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($protocolos as $protocolo)
                        <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" 
                            onclick="window.location.href='{{ route('protocolo-sanidad.show', $protocolo) }}'">
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $protocolo->nombre }}</div>
                                @if($protocolo->descripcion)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($protocolo->descripcion, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded font-medium
                                    {{ $protocolo->estado === 'vigente' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' }}">
                                    v{{ $protocolo->version }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded font-medium
                                    {{ $protocolo->estado === 'vigente' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                                    {{ ucfirst($protocolo->estado) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $protocolo->fecha_implementacion }}</td>
                            <td class="px-4 py-2">{{ $protocolo->responsable }}</td>
                            <td class="px-4 py-2">
                                @if($protocolo->actividades && count($protocolo->actividades) > 0)
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs">
                                        {{ count($protocolo->actividades) }} actividades
                                    </span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 text-xs">Sin actividades</span>
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
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <script>
            // Funciones del modal
            function abrirModalFiltros() {
                document.getElementById('modalFiltros').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function cerrarModalFiltros() {
                document.getElementById('modalFiltros').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Cerrar modal al hacer click fuera de él
            document.addEventListener('click', function(event) {
                const modal = document.getElementById('modalFiltros');
                if (event.target === modal) {
                    cerrarModalFiltros();
                }
            });

            // Función para aplicar filtro rápido por estado
            function aplicarFiltroRapido(estado) {
                // Crear formulario para enviar
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = "{{ route('protocolo-sanidad.index') }}";
                
                const inputEstado = document.createElement('input');
                inputEstado.type = 'hidden';
                inputEstado.name = 'estado';
                inputEstado.value = estado;
                
                form.appendChild(inputEstado);
                document.body.appendChild(form);
                form.submit();
            }

            // Función para aplicar filtro de fecha rápida
            function aplicarFechaRapida(dias) {
                const hoy = new Date();
                const fechaHasta = hoy.toISOString().split('T')[0];
                
                const fechaDesde = new Date();
                fechaDesde.setDate(hoy.getDate() - dias);
                const fechaDesdeStr = fechaDesde.toISOString().split('T')[0];
                
                // Crear formulario para enviar
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = "{{ route('protocolo-sanidad.index') }}";
                
                const inputDesde = document.createElement('input');
                inputDesde.type = 'hidden';
                inputDesde.name = 'fecha_desde';
                inputDesde.value = fechaDesdeStr;
                
                const inputHasta = document.createElement('input');
                inputHasta.type = 'hidden';
                inputHasta.name = 'fecha_hasta';
                inputHasta.value = fechaHasta;
                
                form.appendChild(inputDesde);
                form.appendChild(inputHasta);
                document.body.appendChild(form);
                form.submit();
            }

            // Cerrar modal con ESC
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    cerrarModalFiltros();
                }
            });
        </script>
    </div>
</x-app-layout>
