<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Programar Mantenimiento') }}
                @if($unidad)
                    <span class="text-base font-normal text-gray-600 dark:text-gray-400">- {{ $unidad->nombre }}</span>
                @endif
            </h2>
            <a href="{{ $unidad ? route('produccion.mantenimientos', $unidad) : route('produccion.mantenimientos') }}" 
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
                    <form method="POST" action="{{ route('produccion.mantenimientos.store') }}" class="space-y-6" id="form-mantenimiento">
                        @csrf

                        <!-- Unidad de Producción -->
                        <div class="max-w-2xl mx-auto">
                            <label for="unidad_produccion_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Unidad de Producción *
                            </label>
                            <select name="unidad_produccion_id" id="unidad_produccion_id" required 
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar unidad...</option>
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}" 
                                            {{ (old('unidad_produccion_id', $unidad?->id) == $u->id) ? 'selected' : '' }}>
                                        {{ $u->nombre }} ({{ $u->codigo }}) - {{ ucfirst($u->tipo) }}
                                        @if($u->estado !== 'activo')
                                            - {{ ucfirst($u->estado) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo de Mantenimiento -->
                        <div class="max-w-2xl mx-auto">
                            <label for="tipo_mantenimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tipo de Mantenimiento *
                            </label>
                            <select name="tipo_mantenimiento" id="tipo_mantenimiento" required 
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar tipo...</option>
                                <option value="preventivo" {{ old('tipo_mantenimiento') === 'preventivo' ? 'selected' : '' }}>
                                    Preventivo - Mantenimiento programado regular
                                </option>
                                <option value="correctivo" {{ old('tipo_mantenimiento') === 'correctivo' ? 'selected' : '' }}>
                                    Correctivo - Reparación de problemas específicos
                                </option>
                                <option value="limpieza" {{ old('tipo_mantenimiento') === 'limpieza' ? 'selected' : '' }}>
                                    Limpieza - Limpieza profunda y desinfección
                                </option>
                                <option value="reparacion" {{ old('tipo_mantenimiento') === 'reparacion' ? 'selected' : '' }}>
                                    Reparación - Reparaciones mayores de infraestructura
                                </option>
                                <option value="inspeccion" {{ old('tipo_mantenimiento') === 'inspeccion' ? 'selected' : '' }}>
                                    Inspección - Inspección y evaluación general
                                </option>
                                <option value="desinfeccion" {{ old('tipo_mantenimiento') === 'desinfeccion' ? 'selected' : '' }}>
                                    Desinfección - Desinfección especializada
                                </option>
                            </select>
                        </div>

                        <!-- Prioridad -->
                        <div class="max-w-2xl mx-auto">
                            <label for="prioridad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Prioridad *
                            </label>
                            <select name="prioridad" id="prioridad" required 
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar prioridad...</option>
                                <option value="baja" {{ old('prioridad') === 'baja' ? 'selected' : '' }}>
                                    Baja - No urgente, puede programarse con flexibilidad
                                </option>
                                <option value="media" {{ old('prioridad') === 'media' ? 'selected' : '' }}>
                                    Media - Importante, programar en las próximas semanas
                                </option>
                                <option value="alta" {{ old('prioridad') === 'alta' ? 'selected' : '' }}>
                                    Alta - Urgente, requiere atención pronta
                                </option>
                                <option value="critica" {{ old('prioridad') === 'critica' ? 'selected' : '' }}>
                                    Crítica - Emergencia, atención inmediata
                                </option>
                            </select>
                        </div>

                        <!-- Usuario Responsable -->
                        <div class="max-w-2xl mx-auto">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Usuario Responsable *
                            </label>
                            <select name="user_id" id="user_id" required 
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Seleccionar responsable...</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('user_id') == $usuario->id ? 'selected' : '' }}>
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

                        <!-- Ciclo de mantenimiento -->
                        <div class="mt-6 max-w-2xl mx-auto">
                            <label class="flex items-center text-base font-medium text-gray-700 dark:text-gray-300">
                                <input type="checkbox" id="ciclico" name="ciclico" onchange="toggleCiclico()" class="mr-2 accent-orange-600">
                                ¿Cíclico?
                            </label>
                        </div>
                        <div id="ciclico_options" class="hidden mt-4 max-w-2xl mx-auto">
                            <!-- Campos ocultos para lógica backend -->
                            <input type="hidden" name="repeat_type" id="repeat_type">
                            <input type="hidden" name="repeat_every" id="repeat_every">
                            <input type="hidden" name="repeat_unit" id="repeat_unit">
                            <input type="hidden" name="repeat_count" id="repeat_count">
                            <input type="hidden" name="advanced_week" id="advanced_week">
                            <input type="hidden" name="advanced_weekday" id="advanced_weekday">
                            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de inicio *</label>
                                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="w-full border-gray-300 dark:border-gray-600 rounded-md" onchange="actualizarPatronMensual()" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de fin *</label>
                                        <input type="date" name="fecha_fin" id="fecha_fin" class="w-full border-gray-300 dark:border-gray-600 rounded-md" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Repetir cada</label>
                                        <div class="flex items-center space-x-2">
                                            <select name="intervalo_valor" id="intervalo_valor" class="w-16 border-gray-300 dark:border-gray-600 rounded-md font-bold text-lg" style="display:inline-block;" required>
                                                @for($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}" {{ old('intervalo_valor', 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <select name="intervalo_tipo" id="intervalo_tipo" class="w-28 border-gray-300 dark:border-gray-600 rounded-md font-bold text-lg" style="display:inline-block;" onchange="togglePatronMensual()">
                                                <option value="semanas">semana</option>
                                                <option value="meses">mes</option>
                                                <option value="anios">año</option>
                                            </select>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ejemplo: <b>cada 2 semanas</b>, <b>cada 3 meses</b>, etc.</p>
                                    </div>
                                    <div id="patron_mensual_box" class="col-span-2 mt-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Patrón de repetición</label>
                                        <div class="flex flex-col md:flex-row md:items-center md:space-x-8 space-y-2 md:space-y-0">
                                            <label class="flex items-center space-x-2">
                                                <input type="radio" name="patron_mensual" id="patron_dia_fijo" value="dia_fijo" checked onclick="togglePatronMensualOpciones()">
                                                <span class="flex items-center space-x-1">
                                                    <span>El día</span>
                                                    <input type="number" name="dia_mes" id="dia_mes" min="1" max="31" class="w-14 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-900 px-2 py-1 text-center shadow-sm focus:ring-orange-500 focus:border-orange-500" readonly />
                                                </span>
                                            </label>
                                            <label class="flex items-center space-x-2 md:ml-6">
                                                <input type="radio" name="patron_mensual" id="patron_semana" value="patron_semana" onclick="togglePatronMensualOpciones()">
                                                <span class="flex items-center space-x-1">
                                                    <span>El</span>
                                                    <select name="semana_ordinal" id="semana_ordinal" class="w-20 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-900 px-2 py-1 shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                                        <option value="1">primer</option>
                                                        <option value="2">segundo</option>
                                                        <option value="3">tercer</option>
                                                        <option value="4">cuarto</option>
                                                        <option value="5">último</option>
                                                    </select>
                                                    <select name="dia_semana" id="dia_semana" class="w-24 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-900 px-2 py-1 shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                                        <option value="1">lunes</option>
                                                        <option value="2">martes</option>
                                                        <option value="3">miércoles</option>
                                                        <option value="4">jueves</option>
                                                        <option value="5">viernes</option>
                                                        <option value="6">sábado</option>
                                                        <option value="7">domingo</option>
                                                    </select>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Botón de calcular fechas eliminado, la lógica es backend -->
                            </div>
                        </div>
                        <script>
                        // Lógica para mapear los campos de la UI avanzada a los campos backend antes de enviar
                        document.getElementById('form-mantenimiento').addEventListener('submit', function(e) {
                            if(document.getElementById('ciclico').checked) {
                                // Determinar tipo de repetición
                                const intervaloTipo = document.getElementById('intervalo_tipo').value;
                                const intervaloValor = document.getElementById('intervalo_valor').value;
                                const fechaInicio = document.getElementById('fecha_inicio').value;
                                const fechaFin = document.getElementById('fecha_fin').value;
                                let repeatType = 'interval';
                                let repeatEvery = intervaloValor;
                                let repeatUnit = 'days';
                                let repeatCount = 24;
                                // Calcular cantidad de repeticiones según fechas
                                if(fechaInicio && fechaFin) {
                                    const d1 = new Date(fechaInicio);
                                    const d2 = new Date(fechaFin);
                                    if(intervaloTipo === 'semanas') {
                                        repeatCount = Math.floor((d2 - d1) / (1000*60*60*24*7) / intervaloValor) + 1;
                                    } else if(intervaloTipo === 'meses') {
                                        repeatCount = (d2.getFullYear() - d1.getFullYear()) * 12 + (d2.getMonth() - d1.getMonth());
                                        repeatCount = Math.floor(repeatCount / intervaloValor) + 1;
                                    } else if(intervaloTipo === 'anios') {
                                        repeatCount = Math.floor((d2.getFullYear() - d1.getFullYear()) / intervaloValor) + 1;
                                    } else {
                                        repeatCount = Math.floor((d2 - d1) / (1000*60*60*24) / intervaloValor) + 1;
                                    }
                                }
                                // Mapear unidad
                                if(intervaloTipo === 'semanas') repeatUnit = 'weeks';
                                else if(intervaloTipo === 'meses') repeatUnit = 'months';
                                else if(intervaloTipo === 'anios') repeatUnit = 'years';
                                else repeatUnit = 'days';
                                // Patrón mensual avanzado
                                if(intervaloTipo === 'meses') {
                                    if(document.getElementById('patron_semana').checked) {
                                        repeatType = 'advanced';
                                        document.getElementById('advanced_week').value = document.getElementById('semana_ordinal').value;
                                        document.getElementById('advanced_weekday').value = document.getElementById('dia_semana').value;
                                    } else {
                                        repeatType = 'interval';
                                    }
                                }
                                document.getElementById('repeat_type').value = repeatType;
                                document.getElementById('repeat_every').value = repeatEvery;
                                document.getElementById('repeat_unit').value = repeatUnit;
                                document.getElementById('repeat_count').value = repeatCount;
                            }
                        });
                        function togglePatronMensual() {
                            // Solo mostrar patrón mensual si el ciclo es mensual
                            const tipo = document.getElementById('intervalo_tipo').value;
                            document.getElementById('patron_mensual_box').classList.toggle('hidden', tipo !== 'meses');
                        }
                        function togglePatronMensualOpciones() {
                            // Habilitar/deshabilitar inputs según el patrón seleccionado
                            const diaFijo = document.getElementById('patron_dia_fijo').checked;
                            document.getElementById('dia_mes').disabled = !diaFijo;
                            document.getElementById('semana_ordinal').disabled = diaFijo;
                            document.getElementById('dia_semana').disabled = diaFijo;
                        }
                        function actualizarPatronMensual() {
                            // Si el usuario elige una fecha de inicio, sugerir el patrón correspondiente
                            const fecha = document.getElementById('fecha_inicio').value;
                            if (!fecha) return;
                            // formato esperado: yyyy-mm-dd
                            const partes = fecha.split('-');
                            if (partes.length === 3) {
                                const dia = parseInt(partes[2], 10);
                                document.getElementById('dia_mes').value = dia;
                                // Calcular ordinal y día de la semana
                                const date = new Date(fecha);
                                const diaSemana = date.getDay(); // 0=domingo, 1=lunes...
                                let ordinal = Math.floor((dia - 1) / 7) + 1;
                                if (ordinal > 4) ordinal = 5; // último
                                document.getElementById('semana_ordinal').value = ordinal;
                                document.getElementById('dia_semana').value = diaSemana === 0 ? 7 : diaSemana;
                            }
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                            togglePatronMensual();
                            togglePatronMensualOpciones();
                            actualizarPatronMensual();
                            if(document.getElementById('intervalo_tipo')) document.getElementById('intervalo_tipo').addEventListener('change', togglePatronMensual);
                            if(document.getElementById('patron_dia_fijo')) document.getElementById('patron_dia_fijo').addEventListener('change', togglePatronMensualOpciones);
                            if(document.getElementById('patron_semana')) document.getElementById('patron_semana').addEventListener('change', togglePatronMensualOpciones);
                            if(document.getElementById('fecha_inicio')) document.getElementById('fecha_inicio').addEventListener('change', actualizarPatronMensual);
                        });
                        </script>
                        </div>
                        <!-- Fecha Programada (solo si no es cíclico) -->
                        <div id="fecha_programada_box" class="mt-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 max-w-2xl mx-auto">
                            <label for="fecha_mantenimiento" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Fecha Programada *
                            </label>
                            <input type="date" name="fecha_mantenimiento" id="fecha_mantenimiento" required
                                   value="{{ old('fecha_mantenimiento', now()->addDays(1)->format('Y-m-d')) }}"
                                   min="{{ now()->format('Y-m-d') }}"
                                   class="block w-full border-gray-300 dark:border-gray-600 dark:bg-white dark:text-gray-900 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 px-3 py-2 text-base">
                        </div>

                        <!-- Descripción -->
                        <div class="max-w-2xl mx-auto">
                            <label for="descripcion_trabajo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Descripción del Mantenimiento *
                            </label>
                            <textarea name="descripcion_trabajo" id="descripcion_trabajo" rows="4" required
                                      placeholder="Describe detalladamente el trabajo de mantenimiento a realizar..."
                                      class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('descripcion_trabajo') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Incluye detalles sobre el trabajo a realizar, materiales necesarios, etc.
                            </p>
                        </div>

                        <!-- Observaciones -->
                        <div class="max-w-2xl mx-auto">
                            <label for="observaciones_antes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Observaciones Previas
                            </label>
                            <textarea name="observaciones_antes" id="observaciones_antes" rows="3"
                                      placeholder="Observaciones, notas especiales o requerimientos específicos..."
                                      class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('observaciones_antes') }}</textarea>
                        </div>

                        <!-- Opciones especiales -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mx-auto">
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_vaciado" value="1" 
                                           {{ old('requiere_vaciado') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Requiere vaciado de la unidad
                                    </span>
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_traslado_peces" value="1" 
                                           {{ old('requiere_traslado_peces') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Requiere traslado de peces
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-center justify-end space-x-4 pt-4 max-w-2xl mx-auto">
                            <a href="{{ $unidad ? route('produccion.mantenimientos', $unidad) : route('produccion.mantenimientos') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                Programar Mantenimiento
                            </button>
                        </div>
                        <script>
                        function toggleCiclico() {
                            const checked = document.getElementById('ciclico').checked;
                            document.getElementById('ciclico_options').classList.toggle('hidden', !checked);
                            document.getElementById('fecha_programada_box').classList.toggle('hidden', checked);
                        }
                        function toggleIntervalo() {
                            const tipo = document.getElementById('intervalo_tipo').value;
                            document.getElementById('intervalo_valor').classList.toggle('hidden', !(tipo === 'cada_x_semanas' || tipo === 'cada_x_meses'));
                            document.getElementById('dia_semana_options').classList.toggle('hidden', tipo !== 'semanal' && tipo !== 'cada_x_semanas');
                            document.getElementById('dia_mes_options').classList.toggle('hidden', tipo !== 'mensual' && tipo !== 'anual' && tipo !== 'cada_x_meses');
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                            if(document.getElementById('intervalo_tipo')) toggleIntervalo();
                            if(document.getElementById('ciclico')) toggleCiclico();
                        });
                        </script>
                    </form>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            Información sobre Mantenimientos
                        </h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>Preventivo:</strong> Mantenimiento programado para prevenir problemas futuros</li>
                                <li><strong>Correctivo:</strong> Reparación de problemas específicos o fallas detectadas</li>
                                <li><strong>Limpieza:</strong> Limpieza profunda y desinfección de la unidad</li>
                                <li><strong>Reparación:</strong> Reparaciones mayores de infraestructura y equipos</li>
                                <li><strong>Inspección:</strong> Inspección general del estado de la unidad</li>
                                <li><strong>Desinfección:</strong> Desinfección especializada y sanitización</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
