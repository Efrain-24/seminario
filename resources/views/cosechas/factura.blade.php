@extends('layouts.app')

@section('title', 'Nueva Cosecha - Estilo Factura')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg">
        <!-- Header de la Factura -->
        <div class="border-b border-gray-200 p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-blue-900">REGISTRO DE COSECHA</h1>
                    <p class="text-gray-600 mt-1">Sistema de Gestión Piscícola</p>
                </div>
                <div class="text-right">
                    <div class="mb-2">
                        <label class="text-sm font-medium text-gray-700">Fecha:</label>
                        <input type="date" id="fecha" name="fecha" value="{{ date('Y-m-d') }}" 
                               class="ml-2 px-3 py-1 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Número:</label>
                        <span class="ml-2 px-3 py-1 bg-blue-100 text-blue-800 rounded-md font-mono">
                            COS-{{ date('Y') }}-{{ str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Cliente (para ventas) -->
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user mr-2 text-blue-600"></i>
                Cliente (Solo para ventas)
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID Cliente:</label>
                    <div class="flex">
                        <input type="text" id="buscar_cliente" placeholder="Buscar cliente..."
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:ring-2 focus:ring-blue-500">
                        <button type="button" onclick="buscarCliente()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre:</label>
                    <input type="text" id="nombre_cliente" readonly 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección:</label>
                    <input type="text" id="direccion_cliente" readonly 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                </div>
            </div>
        </div>

        <!-- Selector de Lotes -->
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-fish mr-2 text-green-600"></i>
                    Seleccionar Lotes para Cosecha
                </h3>
                <button type="button" onclick="agregarLote()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>
                    Agregar Lote
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                <div>
                    <input type="text" id="buscar_lote" placeholder="Buscar lote..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select id="agregar_lote" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Selecciona un lote --</option>
                        @foreach($lotes as $lote)
                            <option value="{{ $lote->id }}" 
                                    data-codigo="{{ $lote->codigo_lote }}"
                                    data-especie="{{ $lote->especie }}"
                                    data-cantidad="{{ $lote->cantidad_actual }}"
                                    data-unidad="{{ $lote->unidadProduccion->nombre ?? 'N/A' }}">
                                {{ $lote->codigo_lote }} - {{ $lote->especie }} ({{ $lote->cantidad_actual }} peces)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tabla de Lotes Seleccionados -->
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 border">ID Lote</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 border">Código</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 border">Especie</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 border">Cantidad Cosechada</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 border">Peso (kg)</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 border">Precio/kg (Q)</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 border">Total (Q)</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-700 border">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla_lotes">
                        <!-- Las filas se agregan dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Totales -->
            <div class="mt-6 flex justify-end">
                <div class="w-full max-w-md">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="text-gray-600">Total Lotes:</div>
                            <div class="font-medium text-right" id="total_lotes">0</div>
                            
                            <div class="text-gray-600">Total Peces:</div>
                            <div class="font-medium text-right" id="total_peces">0</div>
                            
                            <div class="text-gray-600">Total Peso:</div>
                            <div class="font-medium text-right" id="total_peso">0.00 kg</div>
                            
                            <div class="border-t border-gray-300 pt-2 font-bold text-gray-800">TOTAL A PAGAR:</div>
                            <div class="border-t border-gray-300 pt-2 font-bold text-right text-lg text-blue-600" id="total_pagar">
                                Q0.00
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Destino:</label>
                    <select name="destino" id="destino" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="venta">Venta</option>
                        <option value="consumo">Consumo Interno</option>
                        <option value="muestra">Muestra</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Responsable:</label>
                    <input type="text" name="responsable" id="responsable" 
                           value="{{ auth()->user()->name ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones:</label>
                <textarea name="observaciones" id="observaciones" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md"
                          placeholder="Observaciones adicionales sobre la cosecha..."></textarea>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-8 flex justify-between">
                <button type="button" onclick="cancelar()" 
                        class="px-6 py-3 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </button>
                <button type="button" onclick="guardarCosecha()" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Cosecha
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
let contadorFilas = 0;
let lotesSeleccionados = [];

function agregarLote() {
    const select = document.getElementById('agregar_lote');
    const option = select.options[select.selectedIndex];
    
    if (!option.value) {
        alert('Por favor selecciona un lote');
        return;
    }
    
    // Verificar si el lote ya está agregado
    if (lotesSeleccionados.includes(option.value)) {
        alert('Este lote ya ha sido agregado');
        return;
    }
    
    const loteId = option.value;
    const codigo = option.dataset.codigo;
    const especie = option.dataset.especie;
    const cantidadDisponible = option.dataset.cantidad;
    const unidad = option.dataset.unidad;
    
    contadorFilas++;
    
    const fila = `
        <tr id="fila_${contadorFilas}" data-lote-id="${loteId}">
            <td class="px-4 py-3 border text-sm">${loteId}</td>
            <td class="px-4 py-3 border text-sm font-medium">${codigo}</td>
            <td class="px-4 py-3 border text-sm">${especie}</td>
            <td class="px-4 py-3 border">
                <input type="number" id="cantidad_${contadorFilas}" min="1" max="${cantidadDisponible}" 
                       onchange="calcularTotales()" onkeyup="calcularTotales()"
                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                <small class="text-gray-500">Máx: ${cantidadDisponible}</small>
            </td>
            <td class="px-4 py-3 border">
                <input type="number" id="peso_${contadorFilas}" step="0.01" min="0.1" 
                       onchange="calcularTotales()" onkeyup="calcularTotales()"
                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
            </td>
            <td class="px-4 py-3 border">
                <input type="number" id="precio_${contadorFilas}" step="0.01" min="0" 
                       onchange="calcularTotales()" onkeyup="calcularTotales()"
                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
            </td>
            <td class="px-4 py-3 border text-sm font-medium text-right" id="total_${contadorFilas}">Q0.00</td>
            <td class="px-4 py-3 border text-center">
                <button type="button" onclick="eliminarFila(${contadorFilas}, '${loteId}')" 
                        class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    document.getElementById('tabla_lotes').insertAdjacentHTML('beforeend', fila);
    lotesSeleccionados.push(loteId);
    
    // Limpiar select
    select.selectedIndex = 0;
    
    calcularTotales();
}

function eliminarFila(numeroFila, loteId) {
    document.getElementById(`fila_${numeroFila}`).remove();
    lotesSeleccionados = lotesSeleccionados.filter(id => id !== loteId);
    calcularTotales();
}

function calcularTotales() {
    let totalLotes = 0;
    let totalPeces = 0;
    let totalPeso = 0;
    let totalPagar = 0;
    
    const filas = document.querySelectorAll('#tabla_lotes tr');
    
    filas.forEach((fila, index) => {
        const numeroFila = fila.id.split('_')[1];
        const cantidad = parseFloat(document.getElementById(`cantidad_${numeroFila}`)?.value || 0);
        const peso = parseFloat(document.getElementById(`peso_${numeroFila}`)?.value || 0);
        const precio = parseFloat(document.getElementById(`precio_${numeroFila}`)?.value || 0);
        
        const totalFila = peso * precio;
        document.getElementById(`total_${numeroFila}`).textContent = `Q${totalFila.toFixed(2)}`;
        
        totalLotes++;
        totalPeces += cantidad;
        totalPeso += peso;
        totalPagar += totalFila;
    });
    
    document.getElementById('total_lotes').textContent = totalLotes;
    document.getElementById('total_peces').textContent = totalPeces;
    document.getElementById('total_peso').textContent = `${totalPeso.toFixed(2)} kg`;
    document.getElementById('total_pagar').textContent = `Q${totalPagar.toFixed(2)}`;
}

function buscarCliente() {
    const clienteId = document.getElementById('buscar_cliente').value;
    // Implementar búsqueda de cliente si es necesario
    alert('Función de búsqueda de cliente en desarrollo');
}

function guardarCosecha() {
    const filas = document.querySelectorAll('#tabla_lotes tr');
    
    if (filas.length === 0) {
        alert('Debes agregar al menos un lote');
        return;
    }
    
    const cosechas = [];
    
    filas.forEach(fila => {
        const loteId = fila.dataset.loteId;
        const numeroFila = fila.id.split('_')[1];
        const cantidad = document.getElementById(`cantidad_${numeroFila}`).value;
        const peso = document.getElementById(`peso_${numeroFila}`).value;
        const precio = document.getElementById(`precio_${numeroFila}`).value;
        
        if (!cantidad || !peso) {
            alert('Todos los campos de cantidad y peso son obligatorios');
            return;
        }
        
        cosechas.push({
            lote_id: loteId,
            cantidad_cosechada: cantidad,
            peso_cosechado_kg: peso,
            precio_kg: precio || 0,
            fecha: document.getElementById('fecha').value,
            destino: document.getElementById('destino').value,
            responsable: document.getElementById('responsable').value,
            observaciones: document.getElementById('observaciones').value
        });
    });
    
    if (cosechas.length === 0) return;
    
    // Enviar datos al servidor
    fetch('{{ route("cosechas.store-multiple") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            cosechas: cosechas
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cosechas guardadas exitosamente');
            window.location.href = '{{ route("cosechas.index") }}';
        } else {
            alert('Error al guardar: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

function cancelar() {
    if (confirm('¿Estás seguro de cancelar? Se perderán todos los datos.')) {
        window.location.href = '{{ route("cosechas.index") }}';
    }
}

// Filtro de búsqueda en tiempo real
document.getElementById('buscar_lote').addEventListener('keyup', function() {
    const filtro = this.value.toLowerCase();
    const select = document.getElementById('agregar_lote');
    const opciones = select.options;
    
    for (let i = 1; i < opciones.length; i++) {
        const texto = opciones[i].text.toLowerCase();
        opciones[i].style.display = texto.includes(filtro) ? 'block' : 'none';
    }
});
</script>
@endsection
@endsection