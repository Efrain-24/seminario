<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Mantenimiento') }}
                <span class="text-base font-normal text-gray-600 dark:text-gray-400">- {{ $mantenimiento->unidadProduccion->nombre }}</span>
            </h2>
            <a href="{{ route('produccion.mantenimientos.show', $mantenimiento) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <!-- Notificaciones flotantes -->
    @if ($errors->any())
        <x-notification type="error" message="¡Ups! Hay algunos errores en el formulario. Revisa los campos marcados en rojo." />
    @endif
    <x-notification type="success" :message="session('success')" />
    <x-notification type="error" :message="session('error')" />

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('produccion.mantenimientos.update', $mantenimiento) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Unidad de Producción -->
                        <div>
                            <label for="unidad_produccion_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Unidad de Producción *
                            </label>
                            <select name="unidad_produccion_id" id="unidad_produccion_id" required 
                                    class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar unidad...</option>
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}" @if((int)(old('unidad_produccion_id', $mantenimiento->unidad_produccion_id)) === (int)$u->id) selected @endif>
                                        {{ $u->nombre }} ({{ $u->codigo }}) - {{ ucfirst($u->tipo) }}
                                        @if($u->estado !== 'activo')
                                            - {{ ucfirst($u->estado) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo de Mantenimiento -->
                        <div>
                            <label for="tipo_mantenimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tipo de Mantenimiento *
                            </label>
                            <select name="tipo_mantenimiento" id="tipo_mantenimiento" required 
                                    class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar tipo...</option>
                                <option value="preventivo" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'preventivo' ? 'selected' : '' }}>
                                    Preventivo - Mantenimiento programado regular
                                </option>
                                <option value="correctivo" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'correctivo' ? 'selected' : '' }}>
                                    Correctivo - Reparación de problemas específicos
                                </option>
                                <option value="limpieza" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'limpieza' ? 'selected' : '' }}>
                                    Limpieza - Limpieza profunda y desinfección
                                </option>
                                <option value="reparacion" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'reparacion' ? 'selected' : '' }}>
                                    Reparación - Reparaciones mayores de infraestructura
                                </option>
                                <option value="inspeccion" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'inspeccion' ? 'selected' : '' }}>
                                    Inspección - Inspección y evaluación general
                                </option>
                                <option value="desinfeccion" {{ old('tipo_mantenimiento', $mantenimiento->tipo_mantenimiento) === 'desinfeccion' ? 'selected' : '' }}>
                                    Desinfección - Desinfección especializada
                                </option>
                            </select>
                        </div>

                        <!-- Prioridad -->
                        <div>
                            <label for="prioridad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Prioridad *
                            </label>
                            <select name="prioridad" id="prioridad" required 
                                    class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar prioridad...</option>
                                <option value="baja" {{ old('prioridad', $mantenimiento->prioridad) === 'baja' ? 'selected' : '' }}>
                                    Baja - No urgente, puede programarse con flexibilidad
                                </option>
                                <option value="media" {{ old('prioridad', $mantenimiento->prioridad) === 'media' ? 'selected' : '' }}>
                                    Media - Importante, programar en las próximas semanas
                                </option>
                                <option value="alta" {{ old('prioridad', $mantenimiento->prioridad) === 'alta' ? 'selected' : '' }}>
                                    Alta - Urgente, requiere atención pronta
                                </option>
                                <option value="critica" {{ old('prioridad', $mantenimiento->prioridad) === 'critica' ? 'selected' : '' }}>
                                    Crítica - Emergencia, atención inmediata
                                </option>
                            </select>
                        </div>

                        <!-- Usuario Responsable -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Usuario Responsable *
                            </label>
                            <select name="user_id" id="user_id" required 
                                    class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar responsable...</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('user_id', $mantenimiento->user_id) == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Selecciona quien será el responsable de ejecutar este mantenimiento
                            </p>
                            @error('user_id')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha Programada -->
                        <div>
                            <label for="fecha_mantenimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha Programada *
                            </label>
                            <input type="date" name="fecha_mantenimiento" id="fecha_mantenimiento" required
                                   value="{{ old('fecha_mantenimiento', $mantenimiento->fecha_mantenimiento->format('Y-m-d')) }}"
                                   class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion_trabajo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Descripción del Mantenimiento *
                            </label>
                            <textarea name="descripcion_trabajo" id="descripcion_trabajo" rows="4" required
                                      placeholder="Describe detalladamente el trabajo de mantenimiento a realizar..."
                                      class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('descripcion_trabajo', $mantenimiento->descripcion_trabajo) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Incluye detalles sobre el trabajo a realizar, materiales necesarios, etc.
                            </p>
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label for="observaciones_antes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Observaciones Previas
                            </label>
                            <textarea name="observaciones_antes" id="observaciones_antes" rows="3"
                                      placeholder="Observaciones, notas especiales o requerimientos específicos..."
                                      class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('observaciones_antes', $mantenimiento->observaciones_antes) }}</textarea>
                        </div>

                        <!-- Opciones especiales -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_vaciado" value="1" 
                                           {{ old('requiere_vaciado', $mantenimiento->requiere_vaciado) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Requiere vaciado de la unidad
                                    </span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_traslado_peces" value="1" 
                                           {{ old('requiere_traslado_peces', $mantenimiento->requiere_traslado_peces) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Requiere traslado de peces
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Artículos / Insumos -->
                        <div class="max-w-2xl mx-auto mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Agregar insumos</label>
                            <button type="button" onclick="abrirModalBuscadorInsumo()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center mb-4">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Agregar Insumo
                            </button>
                            
                            <!-- Modal para buscar insumo -->
                            <div id="modal_buscador_insumo" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
                                <div class="fixed inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-96 max-w-full pointer-events-auto">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Buscar Insumo</h3>
                                            <button type="button" onclick="cerrarModalBuscadorInsumo()" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="mb-4">
                                            <input type="text" id="buscador_insumo_factura" class="block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm px-4 py-2 focus:border-blue-500 focus:ring-blue-500" placeholder="Buscar insumo..." onkeyup="buscarInsumoFactura()">
                                        </div>
                                        <div id="resultados_insumo_factura" class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg max-h-64 overflow-y-auto mb-4"></div>
                                        <div class="flex justify-end space-x-2">
                                            <button type="button" onclick="cerrarModalBuscadorInsumo()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-200">
                                                Cancelar
                                            </button>
                                            <button type="button" onclick="agregarInsumoSeleccionadoFactura()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 inline-flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Agregar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2">Artículo</th>
                                        <th class="px-4 py-2">Unidad</th>
                                        <th class="px-4 py-2">Cantidad</th>
                                        <th class="px-4 py-2">Costo Unitario</th>
                                        <th class="px-4 py-2">Total</th>
                                        <th class="px-4 py-2">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody id="insumos_factura_seleccionados">
                                </tbody>
                            </table>
                        </div>

                        <!-- Actividades a completar por el técnico -->
                        <div class="max-w-2xl mx-auto mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Actividades para el técnico</label>
                            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg mb-2">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2">Actividad</th>
                                        <th class="px-4 py-2">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody id="actividades_table">
                                </tbody>
                            </table>
                            <div class="flex items-center space-x-2">
                                <input type="text" id="nueva_actividad" class="border-gray-300 dark:border-gray-600 rounded-md px-2 py-1 w-full" placeholder="Agregar actividad...">
                                <button type="button" onclick="agregarActividad()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded">Agregar</button>
                            </div>
                        </div>
                        <!-- Campo oculto para enviar actividades como JSON -->
                        <input type="hidden" id="actividades_json" name="actividades_json" value="[]">

                        <!-- Botones de acción -->
                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <a href="{{ route('produccion.mantenimientos.show', $mantenimiento) }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                Actualizar Mantenimiento
                            </button>
                        </div>
                        <script>
                        // Script para actividades
                        var actividades = @json($mantenimiento->actividades ?? []);
                        function agregarActividad() {
                            var val = document.getElementById('nueva_actividad').value.trim();
                            if(val) {
                                actividades.push(val);
                                renderActividades();
                                document.getElementById('nueva_actividad').value = '';
                            }
                        }
                        function eliminarActividad(idx) {
                            actividades.splice(idx, 1);
                            renderActividades();
                        }
                        function renderActividades() {
                            var html = '';
                            actividades.forEach((a, idx) => {
                                html += `<tr><td class='px-4 py-2'>${a}</td><td class='px-4 py-2 text-center'><button type='button' onclick='eliminarActividad(${idx})' class='text-red-600 hover:text-red-800'>Eliminar</button></td></tr>`;
                            });
                            document.getElementById('actividades_table').innerHTML = html;
                            // Actualizar campo hidden con todas las actividades
                            document.getElementById('actividades_json').value = JSON.stringify(actividades);
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                            renderActividades();
                        });
                        
                        // Script para insumos
                        var todosInsumosFactura = [
                            @foreach(App\Models\InventarioItem::all() as $insumo)
                                {id: {{ $insumo->id }}, nombre: "{{ $insumo->nombre }}", unidad: "{{ $insumo->unidad }}", costo_unitario: {{ $insumo->costo_unitario }}},
                            @endforeach
                        ];
                        var insumosFacturaSeleccionados = [
                            @foreach($mantenimiento->insumos as $insumo)
                                {id: {{ $insumo->id }}, nombre: "{{ $insumo->nombre }}", unidad: "{{ $insumo->unidad }}", costo_unitario: {{ $insumo->pivot->costo_unitario }}, cantidad: {{ $insumo->pivot->cantidad }}},
                            @endforeach
                        ];
                        
                        function abrirModalBuscadorInsumo() {
                            document.getElementById('modal_buscador_insumo').classList.remove('hidden');
                            document.getElementById('buscador_insumo_factura').focus();
                        }
                        
                        function cerrarModalBuscadorInsumo() {
                            document.getElementById('modal_buscador_insumo').classList.add('hidden');
                            document.getElementById('buscador_insumo_factura').value = '';
                            document.getElementById('resultados_insumo_factura').innerHTML = '';
                            insumoFacturaSeleccionadoId = null;
                        }
                        
                        function buscarInsumoFactura() {
                            var query = document.getElementById('buscador_insumo_factura').value.toLowerCase();
                            // Mostrar todos los insumos sin filtrar por duplicados (permite agregar el mismo insumo múltiples veces)
                            var resultados = todosInsumosFactura.filter(i => i.nombre.toLowerCase().includes(query));
                            var html = '';
                            resultados.forEach(i => {
                                html += `<div class='px-4 py-3 cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900 border-b border-gray-200 dark:border-gray-600 transition' onclick='seleccionarInsumoFactura(${i.id})'><strong>${i.nombre}</strong> <span class="text-gray-600 dark:text-gray-400">(${i.unidad})</span></div>`;
                            });
                            document.getElementById('resultados_insumo_factura').innerHTML = html || '<div class="px-4 py-3 text-gray-500 text-center">No se encontraron resultados</div>';
                        }
                        
                        var insumoFacturaSeleccionadoId = null;
                        function seleccionarInsumoFactura(id) {
                            insumoFacturaSeleccionadoId = id;
                            var insumo = todosInsumosFactura.find(i => i.id === id);
                            document.getElementById('buscador_insumo_factura').value = insumo.nombre;
                            document.getElementById('resultados_insumo_factura').innerHTML = '';
                        }
                        
                        function agregarInsumoSeleccionadoFactura() {
                            if(insumoFacturaSeleccionadoId === null) {
                                alert('Por favor selecciona un insumo');
                                return;
                            }
                            var insumo = todosInsumosFactura.find(i => i.id === insumoFacturaSeleccionadoId);
                            if(insumo) {
                                // Permitir agregar el mismo insumo múltiples veces (se agrega como nueva fila)
                                insumosFacturaSeleccionados.push({...insumo, cantidad: 1});
                                renderInsumosFacturaSeleccionados();
                                cerrarModalBuscadorInsumo();
                            }
                        }
                        
                        function cambiarCantidadFactura(idx, val) {
                            insumosFacturaSeleccionados[idx].cantidad = parseInt(val) || 1;
                            renderInsumosFacturaSeleccionados();
                        }
                        function eliminarInsumoFactura(idx) {
                            insumosFacturaSeleccionados.splice(idx, 1);
                            renderInsumosFacturaSeleccionados();
                        }
                        function renderInsumosFacturaSeleccionados() {
                            var html = '';
                            insumosFacturaSeleccionados.forEach((i, idx) => {
                                var costoUnitario = i.costo_unitario ? parseFloat(i.costo_unitario) : 0;
                                var cantidad = parseInt(i.cantidad) || 1;
                                var total = costoUnitario * cantidad;
                                html += `<tr>
                                    <td class='px-4 py-2'><input type='hidden' name='insumos[]' value='${i.id}'>${i.nombre}</td>
                                    <td class='px-4 py-2'>${i.unidad}</td>
                                    <td class='px-4 py-2'>
                                        <input type='number' name='cantidades[]' value='${cantidad}' min='1' class='w-16 border-gray-300 rounded' onchange='cambiarCantidadFactura(${idx}, this.value)'>
                                    </td>
                                    <td class='px-4 py-2'>Q${costoUnitario.toFixed(2)}</td>
                                    <td class='px-4 py-2'>Q${total.toFixed(2)}</td>
                                    <td class='px-4 py-2 text-center'>
                                        <button type='button' onclick='eliminarInsumoFactura(${idx})' class='text-red-600 hover:text-red-800'>Eliminar</button>
                                    </td>
                                </tr>`;
                            });
                            document.getElementById('insumos_factura_seleccionados').innerHTML = html;
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                            renderInsumosFacturaSeleccionados();
                        });
                        </script>
                    </form>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Importante: Edición de Mantenimiento
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>Solo se pueden editar mantenimientos que estén en estado <strong>Programado</strong>. 
                            Una vez que un mantenimiento ha sido iniciado o completado, ya no puede modificarse.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Cargar automáticamente la unidad de producción
        document.addEventListener('DOMContentLoaded', function() {
            const unidadId = {{ $mantenimiento->unidad_produccion_id ?? 'null' }};
            const unidadNombre = '{{ $mantenimiento->unidadProduccion?->nombre ?? "null" }}';
            
            console.log('Mantenimiento ID:', {{ $mantenimiento->id }});
            console.log('Unidad Producción ID:', unidadId);
            console.log('Unidad Producción Nombre:', unidadNombre);
            
            if (unidadId !== null && unidadId !== 'null') {
                const select = document.getElementById('unidad_produccion_id');
                console.log('Intentando seleccionar unidad ID:', unidadId);
                
                // Convertir a string para la comparación
                select.value = String(unidadId);
                
                console.log('Select value después de asignar:', select.value);
                console.log('Opción seleccionada:', select.options[select.selectedIndex].text);
            } else {
                console.warn('No hay unidad_produccion_id o es null');
            }
        });
    </script>
</x-app-layout>
