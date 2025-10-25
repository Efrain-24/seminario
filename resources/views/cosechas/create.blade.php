
@extends('layouts.app')

@section('title', 'Nueva Cosecha - Factura')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Notificaciones -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Factura Style -->
    <div class="max-w-5xl mx-auto bg-white border border-gray-200 rounded-lg shadow-lg">
        <!-- Header de Factura -->
        <div class="border-b border-gray-200 p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Beyond Learning</h1>
                    <p class="text-gray-600">Guatemala</p>
                </div>
                <div class="text-right">
                    <h2 class="text-2xl font-bold text-blue-600 mb-2">VENTA</h2>
                    <div class="mb-2">
                        <label class="text-sm text-gray-600">Fecha:</label>
                        <input type="date" id="fecha_factura" value="{{ date('Y-m-d') }}" 
                               class="ml-2 px-2 py-1 border border-gray-300 rounded">
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Número:</span>
                        <span class="ml-2 font-bold">Automático</span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('produccion.cosechas.guardar-multiple') }}" method="POST" id="factura-form">
            @csrf
            
            <!-- Campo oculto para indicar que es venta -->
            <input type="hidden" name="destino" value="venta">
            
            <!-- Información del Cliente -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Cliente</h3>
                
                <!-- Botones de tipo de factura -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-2">Tipo de factura:</label>
                    <div class="flex gap-2">
                        <button type="button" id="btn_cf" onclick="seleccionarTipo('CF')" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            CF (Consumidor Final)
                        </button>
                        <button type="button" id="btn_nit" onclick="seleccionarTipo('NIT')" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                            NIT
                        </button>
                    </div>
                </div>

                <!-- Sección CF (inicialmente oculta) -->
                <div id="seccion_cf" class="hidden">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-green-800 font-semibold">Consumidor Final seleccionado</span>
                        </div>
                        <p class="text-green-700 text-sm">La factura será emitida a Consumidor Final sin datos específicos del cliente.</p>
                    </div>
                </div>

                <!-- Sección NIT (inicialmente visible) -->
                <div id="seccion_nit">
                    <!-- Búsqueda por NIT -->
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">Buscar por NIT:</label>
                        <div class="flex">
                            <input type="text" id="buscar_nit" placeholder="Escriba el NIT para buscar..." 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   oninput="buscarClientePorNit(this.value)" autocomplete="off">
                            <button type="button" onclick="limpiarBusqueda()" 
                                    class="px-4 py-2 bg-gray-500 text-white rounded-r-md hover:bg-gray-600 focus:ring-2 focus:ring-gray-500">
                                Limpiar
                            </button>
                        </div>
                        
                        <!-- Indicador de búsqueda -->
                        <div id="indicador_busqueda" class="hidden mt-2 text-sm text-blue-600">
                            <svg class="w-4 h-4 animate-spin inline mr-1" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="m12 2 4 9h-8l4-9z"></path>
                            </svg>
                            Buscando cliente...
                        </div>

                        <!-- Resultados de búsqueda -->
                        <div id="resultados_busqueda" class="hidden mt-2 bg-white border border-gray-300 rounded-md shadow-lg max-h-40 overflow-y-auto">
                            <!-- Los resultados se llenarán dinámicamente -->
                        </div>

                        <!-- Mensaje de cliente no encontrado -->
                        <div id="cliente_no_encontrado" class="hidden mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="text-yellow-800 text-sm">Cliente no encontrado. Complete los datos para crear un nuevo cliente.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Campos de datos del cliente -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Nombre: *</label>
                            <input type="text" name="cliente_nombre" id="cliente_nombre" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Nombre completo del cliente">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">NIT: *</label>
                            <input type="text" name="cliente_nit" id="cliente_nit" required
                                   pattern="[0-9]*"
                                   maxlength="15"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Solo números (ej: 1234567890)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agregar Productos -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-4 mb-4">
                    <input type="text" id="buscar_lote" placeholder="Buscar artículo..." 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           onkeyup="filtrarLotes()">
                    <select id="select_lote" onchange="seleccionarLote()" 
                            class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Agregar artículo...</option>
                        @if(isset($lotes))
                            @foreach($lotes as $lote)
                                <option value="{{ $lote->id }}" 
                                        data-nombre="{{ $lote->especie ?? 'Pescado' }}"
                                        data-disponible="{{ $lote->cantidad_actual ?? 0 }}"
                                        data-codigo="{{ $lote->codigo_lote ?? $lote->id }}"
                                        data-especie="{{ $lote->especie ?? 'No especificada' }}"
                                        data-precio-libra="{{ $lote->precio_libra ?? 0 }}">
                                    {{ $lote->especie ?? 'Pescado' }} - {{ $lote->codigo_lote }} ({{ $lote->cantidad_actual ?? 0 }} peces)
                                    @if($lote->precio_libra)
                                        - Q{{ number_format($lote->precio_libra, 2) }}/lb
                                    @else
                                        <span style="color: #ef4444;"> - Sin precio configurado</span>
                                    @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <button type="button" onclick="agregarLinea()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                        Agregar
                    </button>
                </div>

                <!-- Tabla de productos -->
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-md">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">ID Artículo</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Concepto</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border-b">Cantidad (peces)</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border-b">Peso (libras)</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border-b">Total (Q)</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border-b">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_productos">
                            <tr id="fila_vacia">
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div>Total productos</div>
                                    <div class="font-bold">0</div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-700">Total a pagar</td>
                                <td class="px-4 py-3 text-center font-bold text-lg">Q<span id="total_general">0.00</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Total a Pagar -->
            <div class="p-6 bg-gray-50">
                <div class="flex justify-end">
                    <div class="text-right">
                        <div class="text-xl text-gray-700 mb-2">TOTAL A PAGAR</div>
                        <div class="text-4xl font-bold text-blue-600">Q<span id="total_final">0.00</span></div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="p-6 flex justify-between">
                <a href="{{ route('produccion.cosechas.index') }}" 
                   class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:ring-2 focus:ring-gray-500">
                    Cancelar
                </a>
                <button type="button" onclick="procesarVenta()" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                    Guardar Venta
                </button>
            </div>

            <!-- Hidden fields for form data -->
            <input type="hidden" name="fecha" id="fecha_hidden">
            <input type="hidden" name="lineas" id="lineas_data">
        </form>
    </div>
</div>

<script>
let lineaCounter = 0;
let totalGeneral = 0;
let lotes = @json($lotes ?? []);
console.log('Datos de lotes recibidos:', lotes);
let busquedaTimeout = null;

// Variables para manejo de clientes
let tipoCliente = 'CF'; // Por defecto CF (Consumidor Final)
window.tipoCliente = 'CF'; // Variable global como respaldo

// Función para buscar cliente por NIT - DEBE ESTAR AQUÍ PARA EL HTML
function buscarClientePorNit(nit) {
    // Limpiar timeout anterior
    if (busquedaTimeout) {
        clearTimeout(busquedaTimeout);
    }
    
    const indicador = document.getElementById('indicador_busqueda');
    const resultados = document.getElementById('resultados_busqueda');
    const noEncontrado = document.getElementById('cliente_no_encontrado');
    
    // Ocultar elementos
    if (resultados) resultados.classList.add('hidden');
    if (noEncontrado) noEncontrado.classList.add('hidden');
    
    // Si no hay NIT, salir
    if (!nit || nit.length < 3) {
        if (indicador) indicador.classList.add('hidden');
        return;
    }
    
    // Mostrar indicador de búsqueda
    if (indicador) indicador.classList.remove('hidden');
    
    // Buscar con delay de 500ms
    busquedaTimeout = setTimeout(() => {
        realizarBusquedaCliente(nit);
    }, 500);
}

// Función para realizar la búsqueda AJAX del cliente
function realizarBusquedaCliente(nit) {
    const indicador = document.getElementById('indicador_busqueda');
    const resultados = document.getElementById('resultados_busqueda');
    const noEncontrado = document.getElementById('cliente_no_encontrado');
    
    // Simular llamada AJAX - aquí deberías hacer la llamada real a tu API
    fetch(`/api/clientes/buscar-nit?nit=${encodeURIComponent(nit)}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (indicador) indicador.classList.add('hidden');
        
        if (data.success && data.clientes && data.clientes.length > 0) {
            mostrarResultadosClientes(data.clientes);
        } else {
            // Cliente no encontrado
            if (noEncontrado) noEncontrado.classList.remove('hidden');
            limpiarCamposCliente();
            // Prellenar el NIT en el campo
            const nitField = document.getElementById('cliente_nit');
            if (nitField) nitField.value = nit;
        }
    })
    .catch(error => {
        console.log('Error en búsqueda de cliente (esto es normal):', error);
        if (indicador) indicador.classList.add('hidden');
        // Cliente no encontrado - mostrar el formulario para crear nuevo cliente
        if (noEncontrado) noEncontrado.classList.remove('hidden');
        limpiarCamposCliente();
        // Prellenar el NIT en el campo
        const nitField = document.getElementById('cliente_nit');
        if (nitField) nitField.value = nit;
    });
}

// Función para seleccionar tipo de cliente (CF o NIT)
function seleccionarTipo(tipo) {
    console.log('DEBUG: seleccionarTipo llamada con tipo:', tipo);
    
    // ASEGURAR que la variable global se actualice
    window.tipoCliente = tipo;
    tipoCliente = tipo;
    
    console.log('DEBUG: tipoCliente actualizado a:', tipoCliente);
    console.log('DEBUG: window.tipoCliente actualizado a:', window.tipoCliente);
    
    const btnCF = document.getElementById('btn_cf');
    const btnNIT = document.getElementById('btn_nit');
    const seccionCF = document.getElementById('seccion_cf');
    const seccionNIT = document.getElementById('seccion_nit');
    
    if (tipo === 'CF') {
        // Activar CF
        btnCF.className = 'px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors';
        btnNIT.className = 'px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors';
        
        seccionCF.classList.remove('hidden');
        seccionNIT.classList.add('hidden');
        
        // Limpiar validaciones de NIT
        limpiarCamposCliente();
    } else {
        // Activar NIT
        btnNIT.className = 'px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors';
        btnCF.className = 'px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors';
        
        seccionNIT.classList.remove('hidden');
        seccionCF.classList.add('hidden');
    }
}

// Función para mostrar resultados de clientes encontrados
function mostrarResultadosClientes(clientes) {
    const resultados = document.getElementById('resultados_busqueda');
    
    let html = '';
    clientes.forEach(cliente => {
        html += `
            <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 cliente-resultado"
                 onclick="seleccionarClienteExistente('${cliente.nit}', '${cliente.nombre}')">
                <div class="font-semibold text-gray-800">${cliente.nombre}</div>
                <div class="text-sm text-gray-600">NIT: ${cliente.nit}</div>
            </div>
        `;
    });
    
    resultados.innerHTML = html;
    resultados.classList.remove('hidden');
}

// Función para seleccionar un cliente existente de los resultados
function seleccionarClienteExistente(nit, nombre) {
    document.getElementById('cliente_nit').value = nit;
    document.getElementById('cliente_nombre').value = nombre;
    
    // Ocultar resultados
    document.getElementById('resultados_busqueda').classList.add('hidden');
    document.getElementById('cliente_no_encontrado').classList.add('hidden');
    
    // Actualizar campo de búsqueda
    document.getElementById('buscar_nit').value = nit;
}

// Función para limpiar campos de cliente
function limpiarCamposCliente() {
    document.getElementById('cliente_nombre').value = '';
    document.getElementById('cliente_nit').value = '';
}

// Función para limpiar búsqueda
function limpiarBusqueda() {
    document.getElementById('buscar_nit').value = '';
    document.getElementById('resultados_busqueda').classList.add('hidden');
    document.getElementById('cliente_no_encontrado').classList.add('hidden');
    document.getElementById('indicador_busqueda').classList.add('hidden');
    limpiarCamposCliente();
}

// Función para filtrar lotes en el select
function filtrarLotes() {
    const busqueda = document.getElementById('buscar_lote').value.toLowerCase();
    const select = document.getElementById('select_lote');
    const options = select.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') return; // Skip the placeholder option
        
        const texto = option.textContent.toLowerCase();
        if (texto.includes(busqueda)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}

// Función para seleccionar un lote del dropdown
function seleccionarLote() {
    const select = document.getElementById('select_lote');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        document.getElementById('buscar_lote').value = selectedOption.textContent;
    }
}

// Función para agregar una línea a la tabla
function agregarLinea() {
    const select = document.getElementById('select_lote');
    const selectedOption = select.options[select.selectedIndex];
    
    if (!selectedOption.value) {
        alert('Por favor selecciona un artículo');
        return;
    }
    
    const loteId = selectedOption.value;
    const especie = selectedOption.dataset.especie || selectedOption.dataset.nombre || 'Pescado';
    const disponible = parseInt(selectedOption.dataset.disponible);
    const codigo = selectedOption.dataset.codigo;
    const precioLibraLote = parseFloat(selectedOption.dataset.precioLibra) || 0;
    
    console.log('Datos del lote seleccionado:', {
        loteId: loteId,
        especie: especie,
        codigo: codigo,
        disponible: disponible,
        precioLibraLote: precioLibraLote,
        datasetCompleto: selectedOption.dataset
    });
    
    // Verificar si el lote ya está en la tabla
    if (document.querySelector(`tr[data-lote-id="${loteId}"]`)) {
        alert('Este lote ya está agregado en la tabla');
        return;
    }
    
    lineaCounter++;
    
    const tabla = document.getElementById('tabla_productos');
    const filaVacia = document.getElementById('fila_vacia');
    
    // Ocultar fila vacía si existe
    if (filaVacia) {
        filaVacia.style.display = 'none';
    }
    
    const nuevaFila = document.createElement('tr');
    nuevaFila.dataset.loteId = loteId;
    nuevaFila.dataset.lineaId = lineaCounter;
    nuevaFila.dataset.precioLibraLote = precioLibraLote;
    nuevaFila.innerHTML = `
        <td class="px-4 py-3 border-b">${codigo}</td>
        <td class="px-4 py-3 border-b">${especie}</td>
        <td class="px-4 py-3 border-b text-center">
            <input type="number" min="1" max="${disponible}" value="1" 
                   class="w-20 px-2 py-1 border border-gray-300 rounded text-center cantidad-input"
                   onchange="calcularLineaTotal(this)" onkeyup="calcularLineaTotal(this)">
            <div class="text-xs text-gray-500">Max: ${disponible}</div>
        </td>
        <td class="px-4 py-3 border-b text-center">
            <input type="number" step="0.01" min="0.01" value="0.00" 
                   class="w-24 px-2 py-1 border border-gray-300 rounded text-center peso-input"
                   placeholder="Peso en libras"
                   onchange="calcularLineaTotal(this)" onkeyup="calcularLineaTotal(this)">
            <div class="text-xs text-gray-500">Precio: Q<span class="precio-libra">0.00</span>/lb</div>
        </td>
        <td class="px-4 py-3 border-b text-center font-bold total-linea">Q0.00</td>
        <td class="px-4 py-3 border-b text-center">
            <button type="button" onclick="eliminarLinea(this)" 
                    class="px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">
                ✕
            </button>
        </td>
    `;
    
    tabla.appendChild(nuevaFila);
    
    // Limpiar selección
    select.value = '';
    document.getElementById('buscar_lote').value = '';
    
    actualizarTotal();
}

// Función para calcular el total de una línea
function calcularLineaTotal(input) {
    const fila = input.closest('tr');
    const cantidad = parseFloat(fila.querySelector('.cantidad-input').value) || 0;
    const pesoLibras = parseFloat(fila.querySelector('.peso-input').value) || 0;
    
    // Usar precio del lote específico
    const precioLibraLote = parseFloat(fila.dataset.precioLibraLote) || 0;
    let precioLibra;
    
    if (precioLibraLote > 0) {
        // Usar precio del lote directamente (ya está en libras)
        precioLibra = precioLibraLote;
    } else {
        // Mostrar error si el lote no tiene precio configurado
        console.error('ERROR: Lote sin precio configurado. precioLibraLote:', precioLibraLote);
        alert('Error: El lote seleccionado no tiene precio configurado. Configure el precio del lote antes de continuar.');
        return;
    }
    
    // Actualizar el precio mostrado
    fila.querySelector('.precio-libra').textContent = precioLibra.toFixed(2);
    
    // Calcular total basado en peso × precio por libra
    const total = pesoLibras * precioLibra;
    
    fila.querySelector('.total-linea').textContent = `Q${total.toFixed(2)}`;
    
    actualizarTotal();
}

// Función para eliminar una línea
function eliminarLinea(btn) {
    const fila = btn.closest('tr');
    fila.remove();
    
    // Mostrar fila vacía si no hay más productos
    const tabla = document.getElementById('tabla_productos');
    if (tabla.children.length === 0) {
        document.getElementById('fila_vacia').style.display = 'table-row';
    }
    
    actualizarTotal();
}

// Función para actualizar el total general
function actualizarTotal() {
    const totalesLinea = document.querySelectorAll('.total-linea');
    let total = 0;
    
    totalesLinea.forEach(elemento => {
        const valor = parseFloat(elemento.textContent.replace('Q', '')) || 0;
        total += valor;
    });
    
    document.getElementById('total_general').textContent = total.toFixed(2);
    document.getElementById('total_final').textContent = total.toFixed(2);
    
    totalGeneral = total;
}

// Función para buscar cliente (placeholder)
function buscarCliente() {
    const busqueda = document.getElementById('buscar_cliente').value;
    
    // Aquí podrías implementar una búsqueda AJAX de clientes
    // Por ahora, solo mostramos un mensaje
    if (busqueda.trim()) {
        alert('Función de búsqueda de clientes no implementada aún');
    }
}

// Función para procesar la venta
function procesarVenta() {
    console.log('====== INICIO PROCESAR VENTA ======');
    
    // VERIFICACIÓN INMEDIATA: ¿Qué UI está visible?
    const seccionNIT = document.getElementById('seccion_nit');
    const nombreInput = document.getElementById('cliente_nombre');
    const nitInput = document.getElementById('cliente_nit');
    
    console.log('Sección NIT visible?', !seccionNIT.classList.contains('hidden'));
    console.log('Nombre cliente value:', nombreInput?.value);
    console.log('NIT cliente value:', nitInput?.value);
    
    // FORZAR TIPO SEGÚN UI VISIBLE Y DATOS
    if (!seccionNIT.classList.contains('hidden') && nombreInput?.value && nitInput?.value) {
        console.log('FORZANDO TIPO A NIT - hay datos de cliente');
        tipoCliente = 'NIT';
        window.tipoCliente = 'NIT';
    } else {
        console.log('FORZANDO TIPO A CF - usando consumidor final');
        tipoCliente = 'CF';
        window.tipoCliente = 'CF';
    }
    
    console.log('DEBUG: tipoCliente actual:', tipoCliente);
    console.log('====================================');
    
    // Validar que hay productos
    const filas = document.querySelectorAll('#tabla_productos tr[data-lote-id]');
    if (filas.length === 0) {
        mostrarMensaje('error', 'Debe agregar al menos un producto a la venta.');
        return;
    }

    // Validar datos del cliente
    let clienteNombre = '';
    
    if (tipoCliente === 'NIT') {
        clienteNombre = document.getElementById('cliente_nombre')?.value;
        
        // Validar nombre para clientes NIT
        if (!clienteNombre || clienteNombre.trim() === '') {
            mostrarMensaje('error', 'El nombre del cliente es obligatorio.');
            return;
        }

        const clienteNit = document.getElementById('cliente_nit')?.value;
        if (!clienteNit || clienteNit.trim() === '') {
            mostrarMensaje('error', 'El NIT del cliente es obligatorio para clientes con NIT.');
            return;
        }
    }

    // Recopilar datos de productos
    const productos = [];
    let totalVenta = 0;

    filas.forEach(fila => {
        const loteId = fila.dataset.loteId;
        const cantidadPeces = parseInt(fila.querySelector('.cantidad-input').value) || 0;
        const pesoLibras = parseFloat(fila.querySelector('.peso-input').value) || 0;
        const precioLibraLote = parseFloat(fila.dataset.precioLibraLote) || 0;

        if (cantidadPeces > 0 && pesoLibras > 0 && precioLibraLote > 0) {
            const subtotal = pesoLibras * precioLibraLote;
            totalVenta += subtotal;

            productos.push({
                lote_id: loteId,
                cantidad_peces: cantidadPeces,
                peso_libras: pesoLibras
            });
        }
    });

    if (productos.length === 0) {
        mostrarMensaje('error', 'Debe ingresar cantidades y pesos válidos para los productos.');
        return;
    }

    // Preparar datos para envío
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // VERIFICAR TIPO DE CLIENTE - usar window.tipoCliente como respaldo
    const tipoClienteActual = window.tipoCliente || tipoCliente || 'CF';
    formData.append('tipo_cliente', tipoClienteActual);
    
    console.log('=== DEBUG TIPO CLIENTE ===');
    console.log('tipoCliente variable:', tipoCliente);
    console.log('window.tipoCliente:', window.tipoCliente);
    console.log('tipoClienteActual usado:', tipoClienteActual);
    console.log('FormData tipo_cliente:', formData.get('tipo_cliente'));
    
    // Manejar datos del cliente según el tipo
    if (tipoClienteActual === 'CF') {
        formData.append('cliente_nombre', 'Consumidor Final');
        formData.append('cliente_nit', null);
        console.log('DEBUG: Enviando CF - Cliente: Consumidor Final, NIT: null');
    } else {
        // Para NIT, tomar los valores del formulario
        const nombreClienteForm = document.getElementById('cliente_nombre').value;
        const nitClienteForm = document.getElementById('cliente_nit').value;
        
        formData.append('cliente_nombre', nombreClienteForm);
        formData.append('cliente_nit', nitClienteForm);
        
        console.log('DEBUG: Enviando NIT - Cliente:', nombreClienteForm, 'NIT:', nitClienteForm);
    }

    // Agregar productos
    productos.forEach((producto, index) => {
        formData.append(`productos[${index}][lote_id]`, producto.lote_id);
        formData.append(`productos[${index}][cantidad_peces]`, producto.cantidad_peces);
        formData.append(`productos[${index}][peso_libras]`, producto.peso_libras);
    });

    // Mostrar loading
    mostrarMensaje('info', 'Procesando venta...');

    // Enviar datos
    fetch('{{ route("produccion.cosechas.guardar-multiple") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Si es una redirección o HTML (no JSON), manejar como redirección
        if (response.redirected || response.headers.get('content-type')?.includes('text/html')) {
            mostrarMensaje('success', 'Venta procesada exitosamente. Redirigiendo...');
            setTimeout(() => {
                // Verificar si es venta para redirigir correctamente
                const destino = document.querySelector('input[name="destino"]:checked')?.value || 
                               document.querySelector('input[name="destino"]')?.value || 
                               'venta'; // Por defecto asumir venta en esta página
                const redirectUrl = destino === 'venta' 
                    ? '{{ route("ventas.index") }}' 
                    : '{{ route("produccion.cosechas.index") }}';
                window.location.href = response.url || redirectUrl;
            }, 1500);
            return null;
        }
        
        // Intentar parsear como JSON
        return response.json().catch(error => {
            console.error('Error parsing JSON:', error);
            // Si hay un error al parsear JSON, pero la respuesta fue exitosa, asumir éxito
            if (response.ok) {
                mostrarMensaje('success', 'Venta registrada exitosamente.');
                setTimeout(() => {
                    // Verificar si es venta para redirigir correctamente
                    const destino = document.querySelector('input[name="destino"]:checked')?.value || 
                                   document.querySelector('input[name="destino"]')?.value || 
                                   'venta'; // Por defecto asumir venta en esta página
                    const redirectUrl = destino === 'venta' 
                        ? '{{ route("ventas.index") }}' 
                        : '{{ route("produccion.cosechas.index") }}';
                    window.location.href = redirectUrl;
                }, 1500);
                return null;
            }
            throw error;
        });
    })
    .then(data => {
        if (data === null) return; // Ya manejado arriba
        
        console.log('Response data:', data);
        
        if (data.success) {
            mostrarMensaje('success', data.message || 'Venta registrada exitosamente.');
            // Limpiar formulario después de un tiempo
            setTimeout(() => {
                // Verificar si es venta para redirigir correctamente
                const destino = document.querySelector('input[name="destino"]:checked')?.value || 
                               document.querySelector('input[name="destino"]')?.value || 
                               'venta'; // Por defecto asumir venta en esta página
                const redirectUrl = destino === 'venta' 
                    ? '{{ route("ventas.index") }}' 
                    : '{{ route("produccion.cosechas.index") }}';
                window.location.href = redirectUrl;
            }, 2000);
        } else if (data.errors) {
            let errorMsg = 'Errores de validación:\n';
            Object.values(data.errors).forEach(errorArray => {
                errorArray.forEach(error => {
                    errorMsg += '- ' + error + '\n';
                });
            });
            mostrarMensaje('error', errorMsg);
        } else if (data.message) {
            mostrarMensaje('error', data.message);
        } else {
            mostrarMensaje('error', 'Error desconocido al procesar la venta.');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        mostrarMensaje('error', 'Error de conexión o del servidor. Revise la consola para más detalles.');
    });
}

// Función para mostrar mensajes
function mostrarMensaje(tipo, mensaje) {
    // Eliminar mensajes anteriores
    const mensajesAnteriores = document.querySelectorAll('.mensaje-flotante');
    mensajesAnteriores.forEach(msg => msg.remove());

    // Crear nuevo mensaje
    const div = document.createElement('div');
    div.className = `mensaje-flotante fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-md`;
    
    switch(tipo) {
        case 'success':
            div.className += ' bg-green-500 text-white';
            break;
        case 'error':
            div.className += ' bg-red-500 text-white';
            break;
        case 'warning':
            div.className += ' bg-yellow-500 text-black';
            break;
        case 'info':
            div.className += ' bg-blue-500 text-white';
            break;
    }

    div.innerHTML = `
        <div class="flex items-center">
            <span class="flex-1">${mensaje}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-xl">&times;</button>
        </div>
    `;

    document.body.appendChild(div);

    // Auto-ocultar después de 5 segundos (excepto errores)
    if (tipo !== 'error') {
        setTimeout(() => {
            if (div.parentElement) {
                div.remove();
            }
        }, 5000);
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    // Configurar fecha actual
    document.getElementById('fecha_factura').value = new Date().toISOString().split('T')[0];
    
    // Configurar event listener del formulario
    const form = document.getElementById('factura-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // Usar la función procesarVenta en su lugar
            procesarVenta();
        });
    }
});
</script>
@endsection