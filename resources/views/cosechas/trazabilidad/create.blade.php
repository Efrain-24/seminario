<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
            {{ isset($trazabilidad) ? 'Editar Registro de Cosecha' : 'Nuevo Registro de Cosecha' }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4">
        @if (session('error'))
            <div class="mb-4 rounded p-3 bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded p-3 bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200">
                <div class="font-medium">{{ __('Whoops! Something went wrong.') }}</div>
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($trazabilidad) ? route('cosechas.trazabilidad.update', $trazabilidad->id) : route('cosechas.trazabilidad.store') }}" 
              method="POST" 
              id="formTrazabilidad"
              class="space-y-6">
            @csrf
            @if(isset($trazabilidad))
                @method('PUT')
            @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Información Básica -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Información Básica</h3>
                        
                        <!-- Lote -->
                        <div class="mb-4">
                            <label for="lote_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Lote <span class="text-red-600">*</span>
                            </label>
                            <select name="lote_id" 
                                    id="lote_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                                    required>
                                <option value="">Seleccione un lote</option>
                                @foreach($lotes as $lote)
                                    <option value="{{ $lote->id }}" 
                                        {{ (old('lote_id', isset($trazabilidad) ? $trazabilidad->lote_id : '')) == $lote->id ? 'selected' : '' }}>
                                        {{ $lote->codigo_lote }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fecha de Cosecha -->
                        <div class="mb-4">
                            <label for="fecha_cosecha" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Fecha de Cosecha <span class="text-red-600">*</span>
                            </label>
                <input type="date" 
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                    id="fecha_cosecha" 
                    name="fecha_cosecha" 
                    value="{{ old('fecha_cosecha', isset($trazabilidad) ? $trazabilidad->fecha_cosecha->format('Y-m-d') : now()->format('Y-m-d')) }}"
                    required>
                        </div>

                        <!-- Tipo de Cosecha -->
                        <div class="mb-4">
                            <label for="tipo_cosecha" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tipo de Cosecha <span class="text-red-600">*</span>
                            </label>
                            <select name="tipo_cosecha" 
                                    id="tipo_cosecha" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                                    required>
                                <option value="parcial" {{ (old('tipo_cosecha', isset($trazabilidad) ? $trazabilidad->tipo_cosecha : '')) == 'parcial' ? 'selected' : '' }}>
                                    Parcial
                                </option>
                                <option value="total" {{ (old('tipo_cosecha', isset($trazabilidad) ? $trazabilidad->tipo_cosecha : '')) == 'total' ? 'selected' : '' }}>
                                    Total
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Pesos y Cantidades -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Pesos y Cantidades</h3>
                        
                        <!-- Peso Bruto -->
                        <div class="mb-4">
                            <label for="peso_bruto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Peso Bruto (kg) <span class="text-red-600">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                                   id="peso_bruto" 
                                   name="peso_bruto"
                                   value="{{ old('peso_bruto', isset($trazabilidad) ? $trazabilidad->peso_bruto : '') }}"
                                   required>
                        </div>

                        <!-- Peso Neto -->
                        <div class="mb-4">
                            <label for="peso_neto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Peso Neto (kg) <span class="text-red-600">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                                   id="peso_neto" 
                                   name="peso_neto"
                                   value="{{ old('peso_neto', isset($trazabilidad) ? $trazabilidad->peso_neto : '') }}"
                                   required>
                        </div>

                        <!-- Unidades -->
                        <div class="mb-4">
                            <label for="unidades" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Unidades
                            </label>
                            <input type="number" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                                   id="unidades" 
                                   name="unidades"
                                   value="{{ old('unidades', isset($trazabilidad) ? $trazabilidad->unidades : '') }}">
                        </div>
                    </div>
                </div>

                <!-- Costos -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Costos</h3>
                    
                    <!-- Costo Mano de Obra -->
                    <div class="mb-4">
                        <label for="costo_mano_obra" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Costo Mano de Obra (Q)
                        </label>
                        <input type="number" 
                               step="0.01" 
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 costo" 
                               id="costo_mano_obra" 
                               name="costo_mano_obra"
                               value="{{ old('costo_mano_obra', isset($trazabilidad) ? $trazabilidad->costo_mano_obra : '0.00') }}">
                    </div>

                    <!-- Costo Insumos -->
                    <div class="mb-4">
                        <label for="costo_insumos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Costo Insumos (Q)
                        </label>
                        <input type="number" 
                               step="0.01" 
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 costo" 
                               id="costo_insumos" 
                               name="costo_insumos"
                               value="{{ old('costo_insumos', isset($trazabilidad) ? $trazabilidad->costo_insumos : '0.00') }}">
                    </div>

                    <!-- Costo Operativo -->
                    <div class="mb-4">
                        <label for="costo_operativo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Costo Operativo (Q)
                        </label>
                        <input type="number" 
                               step="0.01" 
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 costo" 
                               id="costo_operativo" 
                               name="costo_operativo"
                               value="{{ old('costo_operativo', isset($trazabilidad) ? $trazabilidad->costo_operativo : '0.00') }}">
                    </div>

                    <!-- Costo Total -->
                    <div class="mb-4">
                        <label for="costo_total" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Costo Total (Q)
                        </label>
                        <input type="number" 
                               step="0.01" 
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                               id="costo_total" 
                               name="costo_total"
                               value="{{ old('costo_total', isset($trazabilidad) ? $trazabilidad->costo_total : '0.00') }}"
                               readonly>
                    </div>
                </div>

                    <!-- Destino y Notas -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Información de Destino</h3>
                        
                        <!-- Tipo de Destino -->
                        <div class="mb-4">
                            <label for="destino_tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tipo de Destino <span class="text-red-600">*</span>
                            </label>
                            <select name="destino_tipo" 
                                    id="destino_tipo" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                                    required>
                                <option value="cliente_final" {{ (old('destino_tipo', isset($trazabilidad) ? $trazabilidad->destino_tipo : '')) == 'cliente_final' ? 'selected' : '' }}>
                                    Cliente Final
                                </option>
                                <option value="bodega" {{ (old('destino_tipo', isset($trazabilidad) ? $trazabilidad->destino_tipo : '')) == 'bodega' ? 'selected' : '' }}>
                                    Bodega
                                </option>
                                <option value="mercado_local" {{ (old('destino_tipo', isset($trazabilidad) ? $trazabilidad->destino_tipo : '')) == 'mercado_local' ? 'selected' : '' }}>
                                    Mercado Local
                                </option>
                                <option value="exportacion" {{ (old('destino_tipo', isset($trazabilidad) ? $trazabilidad->destino_tipo : '')) == 'exportacion' ? 'selected' : '' }}>
                                    Exportación
                                </option>
                            </select>
                        </div>

                        <!-- Detalle del Destino -->
                        <div class="mb-4">
                            <label for="destino_detalle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Detalle del Destino <span class="text-red-600">*</span>
                            </label>
                            <input type="text" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                                   id="destino_detalle" 
                                   name="destino_detalle"
                                   value="{{ old('destino_detalle', isset($trazabilidad) ? $trazabilidad->destino_detalle : '') }}"
                                   required>
                        </div>

                        <!-- Notas -->
                        <div class="mb-4">
                            <label for="notas" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Notas Adicionales
                            </label>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                                     id="notas" 
                                     name="notas" 
                                     rows="3">{{ old('notas', isset($trazabilidad) ? $trazabilidad->notas : '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('cosechas.trazabilidad.index') }}" 
                       class="px-4 py-2 text-sm rounded bg-gray-600 hover:bg-gray-700 text-white">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm rounded bg-blue-600 hover:bg-blue-700 text-white">
                        {{ isset($trazabilidad) ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar Select2 con estilos personalizados de Tailwind
    $('#lote_id, #tipo_cosecha, #destino_tipo').select2({
        theme: 'default',
        width: '100%',
        dropdownParent: $('body'),
        selectionCssClass: 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100',
        dropdownCssClass: 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100'
    });

    // Calcular costo total
    function calcularCostoTotal() {
        let total = 0;
        $('.costo').each(function() {
            total += parseFloat($(this).val() || 0);
        });
        $('#costo_total').val(total.toFixed(2));
    }

        // Eventos para recalcular costos
        $('.costo').on('input', calcularCostoTotal);

        // Función para validar pesos
        function validarPesos() {
            let pesoBruto = parseFloat($('#peso_bruto').val()) || 0;
            let pesoNeto = parseFloat($('#peso_neto').val()) || 0;
            
            if (pesoNeto > pesoBruto) {
                $('#peso_neto').addClass('border-red-500');
                $('#error-peso').text('El peso neto (' + pesoNeto + ' kg) no puede ser mayor que el peso bruto (' + pesoBruto + ' kg)').removeClass('hidden');
                return false;
            } else {
                $('#peso_neto').removeClass('border-red-500');
                $('#error-peso').text('').addClass('hidden');
                return true;
            }
        }

        // Validar pesos en tiempo real
        $('#peso_bruto, #peso_neto').on('input', validarPesos);

        // Validar antes de enviar el formulario
        $('#formTrazabilidad').on('submit', function(e) {
            if (!validarPesos()) {
                e.preventDefault();
                $([document.documentElement, document.body]).animate({
                    scrollTop: $("#peso_neto").offset().top - 100
                }, 500);
                return false;
            }
        });    // Validación del tipo de cosecha según el estado del lote
    $('#lote_id').on('change', function() {
        let loteId = $(this).val();
        if (loteId) {
            // Aquí puedes agregar una llamada AJAX para verificar el estado del lote
            // y habilitar/deshabilitar la opción de cosecha total según corresponda
        }
    });
});
</script>
@endpush
