@extends('layouts.app')

@section('title', 'Nueva Cosecha')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                <h1 class="text-2xl font-bold text-white flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                    Registrar Nueva Cosecha
                </h1>
                <p class="text-blue-100 mt-1">Complete la información de la cosecha</p>
            </div>

            <!-- Formulario -->
            <form action="{{ route('produccion.cosechas.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Información básica -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Lote -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Lote *
                        </label>
                        <select name="lote_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccione un lote</option>
                            @foreach($lotes ?? [] as $lote)
                                <option value="{{ $lote->id }}">
                                    {{ $lote->nombre ?? $lote->codigo_lote }} 
                                    @if(isset($lote->cantidad_actual))
                                        ({{ $lote->cantidad_actual }} disponibles)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('lote_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Fecha *
                        </label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('fecha')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cantidad -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            Cantidad (peces) *
                        </label>
                        <input type="number" name="cantidad_cosechada" value="{{ old('cantidad_cosechada') }}" min="1" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Número de peces">
                        @error('cantidad_cosechada')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Peso -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                            </svg>
                            Peso Total (kg) *
                        </label>
                        <input type="number" name="peso_cosechado_kg" value="{{ old('peso_cosechado_kg') }}" step="0.01" min="0.01" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Peso en kilogramos">
                        @error('peso_cosechado_kg')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Destino -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Destino *
                        </label>
                        <select name="destino" id="destino" required onchange="toggleVentaFields()" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccione el destino</option>
                            <option value="venta" {{ old('destino') == 'venta' ? 'selected' : '' }}>Venta</option>
                            <option value="consumo" {{ old('destino') == 'consumo' ? 'selected' : '' }}>Consumo Interno</option>
                            <option value="muestra" {{ old('destino') == 'muestra' ? 'selected' : '' }}>Muestra</option>
                            <option value="otro" {{ old('destino') == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('destino')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Responsable -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Responsable
                        </label>
                        <input type="text" name="responsable" value="{{ old('responsable', auth()->user()->name ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('responsable')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Campos de venta (ocultos por defecto) -->
                <div id="venta-fields" class="hidden bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-green-800 mb-4">Información de Venta</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Cliente *</label>
                            <input type="text" name="cliente" value="{{ old('cliente') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Nombre del cliente">
                            @error('cliente')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Precio por kg (Q) *</label>
                            <input type="number" name="precio_kg" value="{{ old('precio_kg') }}" step="0.01" min="0" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="0.00" onchange="calcularTotal()">
                            @error('precio_kg')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                            <input type="tel" name="telefono_cliente" value="{{ old('telefono_cliente') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Número de teléfono">
                            @error('telefono_cliente')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Método de Pago</label>
                            <select name="metodo_pago" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="cheque" {{ old('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                            @error('metodo_pago')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Total de venta -->
                    <div class="mt-4 p-4 bg-green-100 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-green-800">Total de Venta:</span>
                            <span id="total-venta" class="text-2xl font-bold text-green-600">Q 0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Observaciones
                    </label>
                    <textarea name="observaciones" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Observaciones adicionales sobre la cosecha...">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('produccion.cosechas.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-center">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Guardar Cosecha
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Mostrar/ocultar campos de venta
function toggleVentaFields() {
    const destino = document.getElementById('destino').value;
    const ventaFields = document.getElementById('venta-fields');
    
    if (destino === 'venta') {
        ventaFields.classList.remove('hidden');
        // Hacer campos obligatorios
        document.querySelector('input[name="cliente"]').required = true;
        document.querySelector('input[name="precio_kg"]').required = true;
    } else {
        ventaFields.classList.add('hidden');
        // Quitar campos obligatorios
        document.querySelector('input[name="cliente"]').required = false;
        document.querySelector('input[name="precio_kg"]').required = false;
        // Limpiar total
        document.getElementById('total-venta').textContent = 'Q 0.00';
    }
}

// Calcular total de venta
function calcularTotal() {
    const peso = parseFloat(document.querySelector('input[name="peso_cosechado_kg"]').value) || 0;
    const precio = parseFloat(document.querySelector('input[name="precio_kg"]').value) || 0;
    const total = peso * precio;
    
    document.getElementById('total-venta').textContent = `Q ${total.toFixed(2)}`;
}

// Inicializar eventos
document.addEventListener('DOMContentLoaded', function() {
    // Configurar eventos para recálculo automático
    const pesoInput = document.querySelector('input[name="peso_cosechado_kg"]');
    const precioInput = document.querySelector('input[name="precio_kg"]');
    
    if (pesoInput) {
        pesoInput.addEventListener('input', calcularTotal);
    }
    
    if (precioInput) {
        precioInput.addEventListener('input', calcularTotal);
    }
    
    // Verificar estado inicial del destino
    toggleVentaFields();
});
</script>
@endsection