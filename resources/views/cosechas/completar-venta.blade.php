@extends('layouts.app')

@section('title', 'Completar Venta - Cosecha')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Completar Venta de Cosecha
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Registrar datos del cliente y completar la venta
            </p>
        </div>
        <a href="{{ route('produccion.cosechas.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
            ← Volver
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Información de la Cosecha -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                Información de la Cosecha
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Lote
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $cosecha->lote->codigo_lote }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Fecha de Cosecha
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $cosecha->fecha->format('d/m/Y') }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Cantidad Cosechada
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ number_format($cosecha->cantidad_cosechada) }} unidades
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Peso Total
                    </label>
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400">
                        {{ number_format($cosecha->peso_cosechado_kg, 2) }} kg
                    </p>
                </div>

                @if($cosecha->responsable)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Responsable
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $cosecha->responsable }}
                    </p>
                </div>
                @endif

                @if($cosecha->observaciones)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Observaciones de Cosecha
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded">
                        {{ $cosecha->observaciones }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Formulario de Venta -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                Datos de la Venta
            </h3>

            <form action="{{ route('produccion.cosechas.procesar-venta', $cosecha) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Cliente -->
                <div>
                    <label for="cliente" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cliente <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="cliente" id="cliente" 
                           value="{{ old('cliente') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('cliente')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="telefono_cliente" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Teléfono
                    </label>
                    <input type="text" name="telefono_cliente" id="telefono_cliente" 
                           value="{{ old('telefono_cliente') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ej: +505 8888-8888">
                    @error('telefono_cliente')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email_cliente" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email
                    </label>
                    <input type="email" name="email_cliente" id="email_cliente" 
                           value="{{ old('email_cliente') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="cliente@ejemplo.com">
                    @error('email_cliente')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Venta -->
                <div>
                    <label for="fecha_venta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Fecha de Venta <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="fecha_venta" id="fecha_venta" 
                           value="{{ old('fecha_venta', date('Y-m-d')) }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('fecha_venta')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Precio por kg -->
                <div>
                    <label for="precio_kg" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Precio por kg (C$) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="precio_kg" id="precio_kg" step="0.01" min="0.01"
                           value="{{ old('precio_kg') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00" 
                           onchange="calcularTotal()"
                           required>
                    @error('precio_kg')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vista previa del total -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Vista Previa del Total</h4>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span>Peso:</span>
                            <span class="font-mono">{{ number_format($cosecha->peso_cosechado_kg, 2) }} kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Precio por kg:</span>
                            <span class="font-mono">C$ <span id="precio-preview">0.00</span></span>
                        </div>
                        <div class="flex justify-between font-medium border-t pt-1">
                            <span>Total:</span>
                            <span class="font-mono">C$ <span id="total-preview">0.00</span></span>
                        </div>
                        @if($tipoCambio)
                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                            <span>USD (TC: {{ number_format($tipoCambio->venta, 4) }}):</span>
                            <span class="font-mono">$ <span id="total-usd-preview">0.00</span></span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Método de Pago -->
                <div>
                    <label for="metodo_pago" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Método de Pago <span class="text-red-500">*</span>
                    </label>
                    <select name="metodo_pago" id="metodo_pago" 
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="">Seleccionar método...</option>
                        <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        <option value="cheque" {{ old('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="credito" {{ old('metodo_pago') == 'credito' ? 'selected' : '' }}>Crédito</option>
                    </select>
                    @error('metodo_pago')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Observaciones -->
                <div>
                    <label for="observaciones_venta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Observaciones de la Venta
                    </label>
                    <textarea name="observaciones_venta" id="observaciones_venta" rows="3"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Notas adicionales sobre la venta...">{{ old('observaciones_venta') }}</textarea>
                    @error('observaciones_venta')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex space-x-3 pt-4">
                    <button type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                        Completar Venta
                    </button>
                    <a href="{{ route('produccion.cosechas.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calcularTotal() {
    const peso = {{ $cosecha->peso_cosechado_kg }};
    const precioInput = document.getElementById('precio_kg');
    const precio = parseFloat(precioInput.value) || 0;
    const total = peso * precio;
    
    document.getElementById('precio-preview').textContent = precio.toFixed(2);
    document.getElementById('total-preview').textContent = total.toFixed(2);
    
    @if($tipoCambio)
    const tipoCambio = {{ $tipoCambio->venta }};
    const totalUsd = total / tipoCambio;
    document.getElementById('total-usd-preview').textContent = totalUsd.toFixed(2);
    @endif
}

// Calcular al cargar la página si hay valor
document.addEventListener('DOMContentLoaded', function() {
    calcularTotal();
});
</script>
@endsection