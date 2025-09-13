<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                {{ $protocoloSanidad->nombre }} (v{{ $protocoloSanidad->version }})
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-sm rounded font-medium
                    {{ $protocoloSanidad->estado === 'vigente' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                    {{ ucfirst($protocoloSanidad->estado) }}
                </span>
            </div>
        </div>
    </x-slot>
    <div class="py-8 max-w-2xl mx-auto px-4">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Nombre</label>
                    <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">{{ $protocoloSanidad->nombre }}</div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Versión</label>
                        <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">v{{ $protocoloSanidad->version }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Estado</label>
                        <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">{{ ucfirst($protocoloSanidad->estado) }}</div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Descripción</label>
                    <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">{{ $protocoloSanidad->descripcion }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha de Implementación</label>
                    <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">{{ $protocoloSanidad->fecha_implementacion }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Responsable</label>
                    <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">{{ $protocoloSanidad->responsable }}</div>
                </div>

                <!-- Historial de Versiones -->
                @if($protocoloSanidad->protocolo_base_id || $protocoloSanidad->versiones->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Historial de Versiones</label>
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 p-4">
                            @php
                                // Obtener todas las versiones relacionadas
                                $protocoloBase = $protocoloSanidad->protocoloBase ?? $protocoloSanidad;
                                $todasVersiones = \App\Models\ProtocoloSanidad::where('protocolo_base_id', $protocoloBase->id)
                                                                             ->orWhere('id', $protocoloBase->id)
                                                                             ->orderBy('version', 'desc')
                                                                             ->get();
                            @endphp
                            <div class="space-y-2">
                                @foreach($todasVersiones as $version)
                                    <div class="flex items-center justify-between p-2 rounded {{ $version->id === $protocoloSanidad->id ? 'bg-blue-100 dark:bg-blue-900/50' : 'bg-white dark:bg-gray-800' }}">
                                        <div class="flex items-center gap-3">
                                            <span class="px-2 py-1 text-xs rounded font-medium
                                                {{ $version->estado === 'vigente' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' }}">
                                                v{{ $version->version }}
                                            </span>
                                            <span class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $version->fecha_implementacion }}
                                            </span>
                                            <span class="px-2 py-1 text-xs rounded
                                                {{ $version->estado === 'vigente' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                                                {{ ucfirst($version->estado) }}
                                            </span>
                                            @if($version->id === $protocoloSanidad->id)
                                                <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">(Actual)</span>
                                            @endif
                                        </div>
                                        @if($version->id !== $protocoloSanidad->id)
                                            <a href="{{ route('protocolo-sanidad.show', $version) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Ver</a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actividades del Protocolo -->
                @if($protocoloSanidad->actividades && count($protocoloSanidad->actividades) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Actividades del Protocolo</label>
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 p-4">
                            <ul class="space-y-2">
                                @foreach($protocoloSanidad->actividades as $index => $actividad)
                                    <li class="flex items-start gap-2">
                                        <span class="text-blue-600 dark:text-blue-400 font-medium">{{ $index + 1 }}.</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ $actividad }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Acciones del Protocolo -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-6">
                    <div class="flex flex-wrap gap-3">
                        @if($protocoloSanidad->estado === 'vigente')
                            <a href="{{ route('protocolo-sanidad.edit', $protocoloSanidad) }}" 
                               class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar
                            </a>
                            <a href="{{ route('protocolo-sanidad.nueva-version', $protocoloSanidad) }}" 
                               class="px-4 py-2 rounded bg-purple-600 hover:bg-purple-700 text-white flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Nueva Versión
                            </a>
                            <button onclick="marcarObsoleto()" 
                                    class="px-4 py-2 rounded bg-yellow-600 hover:bg-yellow-700 text-white flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Marcar como Obsoleto
                            </button>
                        @else
                            <div class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Protocolo Obsoleto - Sin acciones disponibles
                            </div>
                        @endif
                        
                        <a href="{{ route('protocolo-sanidad.index') }}" class="px-4 py-2 rounded bg-gray-500 hover:bg-gray-600 text-white flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function marcarObsoleto() {
            if (confirm('¿Está seguro de marcar este protocolo como obsoleto? Ya no estará disponible para nuevos registros de limpieza.')) {
                // Crear y enviar formulario para marcar como obsoleto
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("protocolo-sanidad.marcar-obsoleto", $protocoloSanidad) }}';
                
                // Token CSRF
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Method override para PATCH
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                form.appendChild(methodField);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>
