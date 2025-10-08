@extends('layouts.app')
@section('title','Nueva Entrada de Inventario')
@section('content')
<div class="container mx-auto max-w-5xl">
    <h1 class="text-2xl font-bold mb-4">Entrada de Inventario</h1>
    <form method="POST" action="{{ route('entradas.store') }}" id="form-entrada">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-semibold">Proveedor</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="buscar_proveedor" class="border rounded p-2 flex-1" placeholder="Buscar proveedor..." autocomplete="off">
                    <button type="button" id="btn_modal_proveedor" class="bg-indigo-600 text-white px-3 py-2 rounded text-sm">Buscar</button>
                    <a href="{{ route('proveedores.create') }}" target="_blank" class="bg-gray-500 text-white px-3 py-2 rounded text-sm">Nuevo</a>
                </div>
                <input type="hidden" name="proveedor_id" id="proveedor_id_hidden" value="{{ old('proveedor_id') }}">
                <p class="text-xs text-gray-500 mt-1" id="info-proveedor"></p>
                @error('proveedor_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold">Número Documento (Factura)</label>
                <input type="text" name="numero_documento" value="{{ old('numero_documento') }}" class="w-full border rounded p-2" placeholder="Ingresar número manual">
            </div>
            <div>
                <label class="block text-sm font-semibold">Fecha Ingreso</label>
                <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', date('Y-m-d')) }}" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block text-sm font-semibold">Fecha Documento</label>
                <input type="date" name="fecha_documento" value="{{ old('fecha_documento') }}" class="w-full border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-semibold">Moneda</label>
                <select name="moneda" class="w-full border rounded p-2">
                    <option value="GTQ" @selected(old('moneda')=='GTQ')>GTQ</option>
                    <option value="USD" @selected(old('moneda')=='USD')>USD</option>
                    <option value="EUR" @selected(old('moneda')=='EUR')>EUR</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold">Tipo Cambio (si aplica)</label>
                <input type="number" step="0.0001" name="tipo_cambio" value="{{ old('tipo_cambio') }}" class="w-full border rounded p-2" placeholder="0.0000">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold">Bodega de destino</label>
            <select name="bodega_id" class="w-full border rounded p-2" required>
                <option value="">Selecciona una bodega...</option>
                @foreach($bodegas as $bodega)
                    <option value="{{ $bodega->id }}" @selected(old('bodega_id')==$bodega->id)>{{ $bodega->nombre }}</option>
                @endforeach
            </select>
            @error('bodega_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        <h2 class="text-xl font-semibold mb-2">Detalle de Ítems</h2>
        <div class="flex gap-2 mb-3 items-end">
            <div class="flex-1">
                <label class="block text-sm">Ítem</label>
                <select id="item_select" class="w-full border rounded p-2">
                    <option value="">Seleccionar...</option>
                    @foreach($items as $it)
                        <option value="{{ $it->id }}" data-nombre="{{ $it->nombre }}" data-unidad="{{ $it->unidad_base }}">{{ $it->nombre }} ({{ $it->unidad_base }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm">Cantidad</label>
                <input type="number" step="0.001" id="cantidad_input" class="border rounded p-2 w-28" placeholder="0.000">
            </div>
            <div>
                <label class="block text-sm">Costo Unit.</label>
                <input type="number" step="0.0001" id="costo_input" class="border rounded p-2 w-32" placeholder="0.0000">
            </div>
            <div>
                <button type="button" id="agregar_detalle" class="bg-blue-600 text-white px-4 py-2 rounded">Agregar</button>
            </div>
        </div>
        @error('detalles')<p class="text-red-600 text-xs mb-2">{{ $message }}</p>@enderror
        <table class="w-full text-sm border" id="tabla-detalles">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2 text-left">Ítem</th>
                    <th class="border p-2 text-right">Cantidad</th>
                    <th class="border p-2 text-right">Costo Unit.</th>
                    <th class="border p-2 text-right">Subtotal</th>
                    <th class="border p-2"></th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <th colspan="3" class="border p-2 text-right">Subtotal</th>
                    <th class="border p-2 text-right" id="subtotal_general">0.00</th>
                    <th class="border p-2"></th>
                </tr>
            </tfoot>
        </table>

        <div class="mt-6">
            <label class="block text-sm font-semibold">Observaciones</label>
            <textarea name="observaciones" rows="3" class="w-full border rounded p-2" placeholder="Notas adicionales...">{{ old('observaciones') }}</textarea>
        </div>

        <input type="hidden" name="detalles_json" id="detalles_json">

        <div class="mt-6 flex justify-end gap-2">
            <a href="{{ route('entradas.index') }}" class="px-4 py-2 bg-gray-300 rounded">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Guardar Entrada</button>
        </div>
    </form>
</div>
<!-- Modal Proveedor -->
<div id="modal_buscar_proveedor" class="fixed inset-0 hidden items-center justify-center bg-black/40 z-50">
  <div class="bg-white w-full max-w-md rounded shadow-lg overflow-hidden">
    <div class="px-4 py-3 border-b flex justify-between items-center">
        <h3 class="font-semibold">Buscar Proveedor</h3>
        <button type="button" id="cerrar_modal_proveedor" class="text-gray-500 hover:text-gray-700">&times;</button>
    </div>
    <div class="p-4 space-y-3">
        <input type="text" id="input_modal_proveedor" class="w-full border rounded p-2 text-sm" placeholder="Nombre o NIT...">
        <div id="resultados_modal_proveedor" class="max-h-56 overflow-y-auto border rounded p-2 text-sm bg-gray-50"></div>
    </div>
    <div class="px-4 py-3 border-t text-right">
        <button type="button" id="btn_cerrar_modal_proveedor" class="px-3 py-1 bg-gray-300 rounded text-sm">Cerrar</button>
    </div>
  </div>
</div>

<script>
    // --- BÚSQUEDA PROVEEDOR ---
    const modalProv = document.getElementById('modal_buscar_proveedor');
    const abrirProvBtn = document.getElementById('btn_modal_proveedor');
    const cerrarProvBtn = document.getElementById('cerrar_modal_proveedor');
    const cerrarProvBtn2 = document.getElementById('btn_cerrar_modal_proveedor');
    const inputProveedor = document.getElementById('buscar_proveedor');
    const inputHiddenProveedor = document.getElementById('proveedor_id_hidden');
    const infoProveedor = document.getElementById('info-proveedor');
    const inputModalProv = document.getElementById('input_modal_proveedor');
    const resultadosProv = document.getElementById('resultados_modal_proveedor');

    function abrirModalProv(){ modalProv.classList.remove('hidden'); inputModalProv.focus(); resultadosProv.innerHTML=''; }
    function cerrarModalProv(){ modalProv.classList.add('hidden'); }
    abrirProvBtn.addEventListener('click', abrirModalProv);
    cerrarProvBtn.addEventListener('click', cerrarModalProv);
    cerrarProvBtn2.addEventListener('click', cerrarModalProv);
    document.addEventListener('keydown', e=>{ if(e.key==='Escape') cerrarModalProv(); });

    inputModalProv.addEventListener('input', function(){
        const q = this.value.trim();
        resultadosProv.innerHTML = '<div class="py-2 text-xs text-gray-500">Escribe para buscar...</div>';
        if(q.length < 2){ return; }
        fetch(`{{ route('proveedores.search') }}?q=${encodeURIComponent(q)}`)
            .then(r=>r.json())
            .then(data=>{
                if(!Array.isArray(data) || data.length===0){
                    resultadosProv.innerHTML = '<div class="py-2 text-xs text-gray-500">Sin resultados</div>';
                    return;
                }
                resultadosProv.innerHTML='';
                data.forEach(p=>{
                    const div=document.createElement('div');
                    div.className='px-2 py-1 rounded cursor-pointer hover:bg-indigo-100';
                    div.innerHTML = `<span class="font-medium">${p.nombre}</span> <span class="text-gray-500 text-xs">${p.nit||''}</span>`;
                    div.addEventListener('click',()=>{
                        inputProveedor.value = p.nombre;
                        inputHiddenProveedor.value = p.id;
                        infoProveedor.textContent = `Seleccionado: ${p.nombre} (${p.categoria||'-'})`;
                        cerrarModalProv();
                    });
                    resultadosProv.appendChild(div);
                });
            })
            .catch(()=>{ resultadosProv.innerHTML='<div class="py-2 text-xs text-red-600">Error buscando</div>'; });
    });

    // Permitir búsqueda rápida directa en el input principal (mostrar modal si >2 chars y Enter)
    inputProveedor.addEventListener('keydown', e=>{
        if(e.key==='Enter'){
            e.preventDefault(); abrirModalProv(); inputModalProv.value = inputProveedor.value; inputModalProv.dispatchEvent(new Event('input')); }
    });
    const detalles = [];
    function renderDetalles(){
        const tbody = document.querySelector('#tabla-detalles tbody');
        tbody.innerHTML = '';
        let subtotal = 0;
        detalles.forEach((d,i)=>{
            subtotal += d.cantidad * d.costo_unitario;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="border p-2">${d.nombre}</td>
                <td class="border p-2 text-right">${Number(d.cantidad).toFixed(3)}</td>
                <td class="border p-2 text-right">${Number(d.costo_unitario).toFixed(4)}</td>
                <td class="border p-2 text-right">${(d.cantidad * d.costo_unitario).toFixed(2)}</td>
                <td class="border p-2 text-center"><button type="button" data-index="${i}" class="text-red-600 eliminar-detalle">X</button></td>`;
            tbody.appendChild(tr);
        });
        document.getElementById('subtotal_general').textContent = subtotal.toFixed(2);
        document.getElementById('detalles_json').value = JSON.stringify(detalles);
    }
    document.getElementById('agregar_detalle').addEventListener('click',()=>{
        const sel = document.getElementById('item_select');
        const itemId = sel.value;
        const nombre = sel.options[sel.selectedIndex]?.dataset.nombre;
        const unidad = sel.options[sel.selectedIndex]?.dataset.unidad;
        const cant = parseFloat(document.getElementById('cantidad_input').value);
        const costo = parseFloat(document.getElementById('costo_input').value);
        if(!itemId || !cant || !costo){ alert('Completa ítem, cantidad y costo'); return; }
        detalles.push({item_id:itemId,nombre,unidad,cantidad:cant,costo_unitario:costo});
        renderDetalles();
        document.getElementById('cantidad_input').value='';
        document.getElementById('costo_input').value='';
        sel.value='';
    });
    document.addEventListener('click', e=>{
        if(e.target.classList.contains('eliminar-detalle')){
            const idx = e.target.getAttribute('data-index');
            detalles.splice(idx,1); renderDetalles();
        }
    });
    document.getElementById('form-entrada').addEventListener('submit', e=>{
        if(detalles.length===0){ e.preventDefault(); alert('Agrega al menos un detalle'); }
        // Convertir detalles_json a estructura que backend espera
        const hidden = document.getElementById('detalles_json');
        try {
            const parsed = JSON.parse(hidden.value);
            // Mapear a campos repetidos tradicionales
            parsed.forEach((d,i)=>{
                const form = e.target;
                ['item_id','cantidad','costo_unitario'].forEach(campo=>{
                    const input = document.createElement('input');
                    input.type='hidden';
                    input.name = `detalles[${i}][${campo}]`;
                    input.value = d[campo];
                    form.appendChild(input);
                });
            });
        } catch(err){ console.error(err); }
    });
</script>
@endsection
