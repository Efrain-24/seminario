@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('content')

<!-- Notificaciones -->
<x-notification type="success" :message="session('success')" />
<x-notification type="error" :message="session('error')" />
<x-notification type="warning" :message="session('warning')" />

<div class="container" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('ventas.store') }}" method="POST" id="form-venta">
        @csrf
        <!-- Encabezado de la empresa -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
            <div>
                <div style="font-weight: bold; font-size: 1.2em;">Beyond Learning</div>
                <div style="font-size: 0.95em; color: #444;">Guatemala</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 2em; font-weight: bold; letter-spacing: 2px; color: #1a237e;">FACTURA</div>
                <div style="font-size: 0.95em; margin-top: 8px;">
                    <span style="font-weight: bold;">Fecha:</span> <input type="date" name="fecha_venta" class="form-control" value="{{ date('Y-m-d') }}" required style="display: inline-block; width: auto;">
                    <br>
                    <span style="font-weight: bold;">Número:</span> <span style="font-family: monospace;">Automático</span>
                </div>
            </div>
        </div>

        <!-- Datos del cliente -->
        <div style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 12px 18px; margin-bottom: 24px; background: #fafbfc;">
            <div style="font-weight: bold;">Cliente</div>
            <div style="position: relative;">
                <span style="font-weight: 500;">ID cliente:</span>
                <div style="display: flex; align-items: center;">
                    <input type="text" id="buscar_cliente" class="form-control" placeholder="Buscar cliente..." style="display: inline-block; width: auto;">
                    <button type="button" id="abrir_modal" style="margin-left: 8px; background: #1a237e; color: #fff; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">
                        Buscar
                    </button>
                </div>
            </div>
            <input type="hidden" name="cliente_id" id="cliente_id_hidden">
            <div>
                <span style="font-weight: 500;">Nombre:</span> <span id="nombre-cliente"></span>
            </div>
            <div>
                <span style="font-weight: 500;">Dirección:</span> <span id="direccion-cliente"></span>
            </div>
        </div>

        <!-- Tabla de detalle de productos -->
        <div style="margin-bottom: 16px;">
            <div class="flex flex-col md:flex-row md:items-end gap-2 mb-2">
                <div class="flex-1">
                    <input type="text" id="buscador-articulo" class="form-control" placeholder="Buscar artículo..." onkeyup="filtrarArticulos()">
                </div>
                <div class="flex-1">
                    <select id="articulo_id" class="form-control">
                        <option value="">Agregar artículo...</option>
                        @foreach(App\Models\InventarioItem::orderBy('nombre')->get() as $item)
                            <option value="{{ $item->id }}" data-nombre="{{ $item->nombre }}" data-precio="{{ $item->costo_unitario }}">{{ $item->id }} - {{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-primary" onclick="agregarArticulo()">Agregar</button>
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;" id="tabla-articulos">
                <thead>
                    <tr style="background: #f5f5f5; border-bottom: 2px solid #bdbdbd;">
                        <th style="padding: 8px; border: 1px solid #e0e0e0; text-align: right;">ID Artículo</th>
                        <th style="padding: 8px; border: 1px solid #e0e0e0; text-align: left;">Concepto</th>
                        <th style="padding: 8px; border: 1px solid #e0e0e0; text-align: right;">Cantidad</th>
                        <th style="padding: 8px; border: 1px solid #e0e0e0; text-align: right;">Precio Unitario (Q)</th>
                        <th style="padding: 8px; border: 1px solid #e0e0e0; text-align: right;">Total (Q)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr style="background: #f5f5f5;">
                        <th colspan="2" style="padding: 8px; border: 1px solid #e0e0e0; text-align: right;">Total productos</th>
                        <th id="total-cantidad" style="padding: 8px; border: 1px solid #e0e0e0; text-align: right;">0</th>
                        <th style="padding: 8px; border: 1px solid #e0e0e0; text-align: right;">Total a pagar</th>
                        <th id="total-factura" style="padding: 8px; border: 1px solid #e0e0e0; text-align: right;">Q0.00</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Total fuera de la tabla -->
        <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
            <div style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 16px 32px; background: #f5f5f5; min-width: 260px;">
                <div style="font-size: 1.1em; font-weight: bold; color: #222;">TOTAL A PAGAR</div>
                <div style="font-size: 1.5em; font-weight: bold; color: #1a237e;" id="total-final">Q0.00</div>
            </div>
        </div>
        <input type="hidden" name="total" id="input-total">
        <div class="flex justify-end" style="margin-top: 24px;">
            <a href="{{ route('ventas.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Guardar Venta</button>
        </div>
    </form>
</div>

<!-- Modal para buscar cliente -->
<div id="modal_buscar_cliente" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); z-index: 1000;">
    <div style="padding: 16px; border-bottom: 1px solid #e0e0e0; font-weight: bold;">Buscar Cliente</div>
    <div style="padding: 16px;">
        <input type="text" id="input_modal_buscar" class="form-control" placeholder="Escribe el nombre del cliente..." style="width: 100%;">
        <div id="resultados_modal" style="margin-top: 8px; max-height: 200px; overflow-y: auto; border: 1px solid #e0e0e0; border-radius: 4px; padding: 8px; background: #fafbfc;">
            <!-- Resultados de búsqueda aparecerán aquí -->
            <div id="crear-cliente-opcion" style="display:none; margin-top:10px;">
                <button type="button" id="btn-crear-cliente" style="background:#1976d2;color:#fff;padding:6px 12px;border:none;border-radius:4px;cursor:pointer;width:100%;">Crear nuevo cliente</button>
            </div>
        </div>
    </div>
    <div style="padding: 16px; text-align: right; border-top: 1px solid #e0e0e0;">
        <button type="button" id="cerrar_modal" style="background: #e0e0e0; color: #444; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">Cerrar</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- LÓGICA ARTÍCULOS (sin cambios) ---
        // (mantenemos las funciones ya declaradas arriba)

        // --- MODAL CLIENTES ---
        const btnAbrir = document.getElementById('abrir_modal');
        const btnCerrar = document.getElementById('cerrar_modal');
        const modal = document.getElementById('modal_buscar_cliente');
        const inputBuscarRapido = document.getElementById('input_modal_buscar');
        const resultados = document.getElementById('resultados_modal');
        const inputClienteVisible = document.getElementById('buscar_cliente');
        const inputClienteHidden = document.getElementById('cliente_id_hidden');
        const spanNombre = document.getElementById('nombre-cliente');
        const spanDireccion = document.getElementById('direccion-cliente');
        const crearClienteDiv = document.getElementById('crear-cliente-opcion');
        const btnCrearCliente = document.getElementById('btn-crear-cliente');

        function abrirModal() {
            if (!modal) return console.error('Modal no encontrado');
            modal.style.display = 'block';
        }
        function cerrarModal() {
            if (!modal) return;
            modal.style.display = 'none';
        }

        if (btnAbrir) btnAbrir.addEventListener('click', abrirModal);
        if (btnCerrar) btnCerrar.addEventListener('click', cerrarModal);
        if (btnCrearCliente) {
            btnCrearCliente.addEventListener('click', function() {
                window.location.href = "{{ route('clientes.create') }}";
            });
        }

        // Cerrar con ESC
        document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModal(); });
        // Cerrar clic fuera
        window.addEventListener('click', e => { if (e.target === modal) cerrarModal(); });

        if (inputBuscarRapido) {
            inputBuscarRapido.addEventListener('input', function() {
                const query = this.value.trim();
                resultados.innerHTML = '';
                if (crearClienteDiv) crearClienteDiv.style.display = 'none';
                if (query.length < 3) return; // mínimo 3 caracteres
                fetch(`{{ url('/clientes/buscar') }}?q=${encodeURIComponent(query)}`)
                    .then(r => { if(!r.ok) throw new Error('Respuesta no OK'); return r.json(); })
                    .then(data => {
                        if (!Array.isArray(data) || data.length === 0) {
                            resultados.innerHTML = '<div style="padding:6px;color:#777;">Sin resultados</div>';
                            if (crearClienteDiv) {
                                crearClienteDiv.style.display = 'block';
                                resultados.appendChild(crearClienteDiv);
                            }
                            return;
                        }
                        data.forEach(c => {
                            const item = document.createElement('div');
                            item.style.padding = '6px 8px';
                            item.style.cursor = 'pointer';
                            item.style.borderBottom = '1px solid #eee';
                            item.textContent = `${c.nombre} (ID: ${c.id})`;
                            item.addEventListener('mouseover', () => item.style.background = '#f1f5f9');
                            item.addEventListener('mouseout', () => item.style.background = '');
                            item.addEventListener('click', () => {
                                inputClienteVisible.value = c.id;
                                inputClienteHidden.value = c.id;
                                spanNombre.textContent = c.nombre || '';
                                spanDireccion.textContent = c.direccion || '';
                                cerrarModal();
                            });
                            resultados.appendChild(item);
                        });
                        if (crearClienteDiv) crearClienteDiv.style.display = 'none';
                    })
                    .catch(err => {
                        console.error(err);
                        resultados.innerHTML = '<div style="padding:6px;color:#c00;">Error buscando</div>';
                        if (crearClienteDiv) crearClienteDiv.style.display = 'none';
                    });
            });
        }
    });
</script>
@endsection
