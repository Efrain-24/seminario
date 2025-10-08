<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Detalle de Acción Correctiva
        </h2>
    </x-slot>

    <!-- Notificaciones -->
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />
    <x-notification type="warning" :message="session('warning')" />

    <div class="py-8 max-w-4xl mx-auto px-4">
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="space-y-6">
                <!-- Información Principal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Título</label>
                        <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3">{{ $accion->titulo }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Responsable</label>
                        <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3">{{ $accion->responsable ? $accion->responsable->name : '-' }}</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Descripción</label>
                    <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3">{{ $accion->descripcion }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha Prevista</label>
                        <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3">{{ $accion->fecha_prevista }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha Límite</label>
                        <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3">{{ $accion->fecha_limite }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Estado</label>
                        <div class="w-full rounded border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($accion->estado === 'pendiente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($accion->estado === 'en_progreso') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($accion->estado === 'completada') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($accion->estado === 'cancelada') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                {{ ucfirst(str_replace('_', ' ', $accion->estado)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sección de Seguimientos -->
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Seguimientos</h3>
                        <button type="button" 
                                onclick="toggleSeguimientoForm()" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Agregar Seguimiento
                        </button>
                    </div>

                    <!-- Formulario para agregar seguimiento -->
                    <div id="seguimientoForm" class="hidden mb-6 p-6 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                        <form id="formSeguimiento" action="{{ route('acciones_correctivas.agregarSeguimiento', $accion->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Descripción del Seguimiento</label>
                                    <textarea name="descripcion" id="descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100" placeholder="Describa el progreso, observaciones o cambios realizados..." required></textarea>
                                </div>

                                <div>
                                    <label for="archivo_evidencia" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Archivo de Evidencia (Opcional)</label>
                                    <input type="file" name="archivo_evidencia" id="archivo_evidencia" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formatos permitidos: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX. Máximo 10MB.</p>
                                </div>

                                <div>
                                    <label for="cambiar_estado" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Cambiar Estado (Opcional)
                                        <span class="text-xs text-blue-600 dark:text-blue-400 font-normal ml-2">Se registrará automáticamente</span>
                                    </label>
                                    <select name="cambiar_estado" id="cambiar_estado" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                                        <option value="">No cambiar estado</option>
                                        <option value="pendiente" {{ $accion->estado === 'pendiente' ? 'disabled' : '' }}>Pendiente</option>
                                        <option value="en_progreso" {{ $accion->estado === 'en_progreso' ? 'disabled' : '' }}>En Progreso</option>
                                        <option value="completada" {{ $accion->estado === 'completada' ? 'disabled' : '' }}>Completada</option>
                                        <option value="cancelada" {{ $accion->estado === 'cancelada' ? 'disabled' : '' }}>Cancelada</option>
                                    </select>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Estado actual: <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $accion->estado)) }}</span>
                                        <br>
                                        <span class="text-blue-600 dark:text-blue-400">Si seleccionas un nuevo estado, se registrará automáticamente en el historial</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-4">
                                <button type="button" onclick="toggleSeguimientoForm()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                                    Cancelar
                                </button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Guardar Seguimiento
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Formulario para editar seguimiento -->
                    <div id="editarSeguimientoForm" class="hidden mb-6 p-6 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">Editar Seguimiento</h4>
                            <button type="button" onclick="cancelarEdicion()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <form id="formEditarSeguimiento" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label for="edit_descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Descripción del Seguimiento</label>
                                    <textarea name="descripcion" id="edit_descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100" placeholder="Describa el progreso, observaciones o cambios realizados..." required></textarea>
                                </div>

                                <div>
                                    <label for="edit_archivo_evidencia" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Archivo de Evidencia (Opcional)</label>
                                    <input type="file" name="archivo_evidencia" id="edit_archivo_evidencia" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-100">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formatos permitidos: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX. Máximo 10MB.</p>
                                    <div id="archivoActual" class="mt-2 hidden">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Archivo actual: <span id="nombreArchivoActual" class="font-medium"></span></p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-4">
                                <button type="button" onclick="cancelarEdicion()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                                    Cancelar
                                </button>
                                <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    Actualizar Seguimiento
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de seguimientos -->
                    <div class="space-y-4">
                        @forelse($accion->seguimientos as $seguimiento)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 bg-white dark:bg-gray-800">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                                            {{ substr($seguimiento->usuario->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $seguimiento->usuario->name }}</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $seguimiento->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Botones de acción -->
                                    <div class="flex items-center gap-2">
                                        <button onclick="editarSeguimiento({{ $seguimiento->id }})" 
                                                class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" 
                                                title="Editar seguimiento">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="eliminarSeguimiento({{ $seguimiento->id }})" 
                                                class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" 
                                                title="Eliminar seguimiento">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    @php
                                        // Mostrar solo la descripción del usuario, sin la información del sistema
                                        $descripcionParts = explode('[Sistema]', $seguimiento->descripcion);
                                        $descripcionUsuario = trim($descripcionParts[0]);
                                    @endphp
                                    
                                    <!-- Solo descripción del usuario -->
                                    <p class="text-gray-800 dark:text-gray-200">{{ $descripcionUsuario }}</p>
                                    
                                </div>

                                @if($seguimiento->archivo_evidencia)
                                    <div class="mt-4">
                                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">Archivo de Evidencia</h5>
                                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-900">
                                            <div class="flex items-center gap-3">
                                                @if(in_array(strtolower(pathinfo($seguimiento->nombre_archivo_original, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                                    <img src="{{ asset('storage/' . $seguimiento->archivo_evidencia) }}" alt="{{ $seguimiento->nombre_archivo_original }}" class="w-12 h-12 object-cover rounded">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $seguimiento->nombre_archivo_original }}</p>
                                                    @if($seguimiento->tamaño_archivo)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($seguimiento->tamaño_archivo / 1024, 1) }} KB</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $seguimiento->archivo_evidencia) }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                                    Ver archivo
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sin seguimientos</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No hay seguimientos registrados para esta acción correctiva.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="flex gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <a href="{{ route('acciones_correctivas.edit', $accion->id) }}" class="px-4 py-2 rounded bg-yellow-500 hover:bg-yellow-600 text-white">Editar</a>
                        <a href="{{ route('acciones_correctivas.index') }}" class="px-4 py-2 rounded bg-gray-500 hover:bg-gray-600 text-white">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript simplificado -->
    <script>
        function toggleSeguimientoForm() {
            const form = document.getElementById('seguimientoForm');
            form.classList.toggle('hidden');
            
            if (!form.classList.contains('hidden')) {
                document.getElementById('descripcion').focus();
            }
        }

        // El cambio de estado se maneja automáticamente en el backend
        // No necesitamos modificar la descripción del usuario

        // Manejar envío del formulario
        document.getElementById('formSeguimiento').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recargar la página para mostrar el nuevo seguimiento
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'No se pudo guardar el seguimiento'));
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar el seguimiento');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });

        // Funciones para editar seguimiento
        function editarSeguimiento(seguimientoId) {
            // Ocultar formulario de agregar si está visible
            document.getElementById('seguimientoForm').classList.add('hidden');
            
            // Obtener datos del seguimiento
            fetch(`/acciones_correctivas/{{ $accion->id }}/seguimiento/${seguimientoId}/editar`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                // Rellenar el formulario de edición
                document.getElementById('edit_descripcion').value = data.descripcion;
                
                // Mostrar archivo actual si existe
                if (data.archivo_evidencia) {
                    document.getElementById('archivoActual').classList.remove('hidden');
                    document.getElementById('nombreArchivoActual').textContent = data.nombre_archivo_original;
                } else {
                    document.getElementById('archivoActual').classList.add('hidden');
                }
                
                // Configurar la acción del formulario
                document.getElementById('formEditarSeguimiento').action = `/acciones_correctivas/{{ $accion->id }}/seguimiento/${seguimientoId}`;
                
                // Mostrar el formulario de edición
                document.getElementById('editarSeguimientoForm').classList.remove('hidden');
                document.getElementById('edit_descripcion').focus();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del seguimiento');
            });
        }

        function cancelarEdicion() {
            document.getElementById('editarSeguimientoForm').classList.add('hidden');
            document.getElementById('formEditarSeguimiento').reset();
        }

        function eliminarSeguimiento(seguimientoId) {
            if (confirm('¿Estás seguro de que deseas eliminar este seguimiento? Esta acción no se puede deshacer.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/acciones_correctivas/{{ $accion->id }}/seguimiento/${seguimientoId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Manejar envío del formulario de edición
        document.getElementById('formEditarSeguimiento').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Actualizando...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recargar la página para mostrar los cambios
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'No se pudo actualizar el seguimiento'));
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el seguimiento');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    </script>
</x-app-layout>
