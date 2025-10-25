<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ticket - {{ $venta->codigo_venta ?? 'Sin código' }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            margin: 0;
            padding: 5px;
            width: 58mm; /* Ancho típico de impresora térmica */
            color: #000;
            line-height: 1.2;
        }
        
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .separator { 
            border-top: 1px dashed #000; 
            margin: 8px 0; 
            height: 1px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .company {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .left { text-align: left; }
        .right { text-align: right; }
        
        .total-section {
            margin-top: 8px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .total-final {
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .footer {
            margin-top: 10px;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- ENCABEZADO -->
    <div class="header">
        <div class="company">BEYOND LEARNING</div>
        <div>Guatemala</div>
        <div>Sistema de Acuicultura</div>
    </div>
    
    <div class="separator"></div>
    
    <!-- INFORMACIÓN BÁSICA -->
    <div class="center bold" style="font-size: 11px;">TICKET DE VENTA</div>
    
    <div class="row">
        <span>Código:</span>
        <span>{{ $venta->codigo_venta ?? 'Sin código' }}</span>
    </div>
    
    <div class="row">
        <span>Emisión:</span>
        <span>{{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y H:i') }}</span>
    </div>
    
    <div class="row">
        <span>Cliente:</span>
        <span>{{ $venta->cliente ?? 'Sin cliente' }}</span>
    </div>
    
    <div class="separator"></div>
    
    <!-- PRODUCTO(S) -->
    @if(isset($venta->es_venta_multiple) && $venta->es_venta_multiple)
        <!-- VENTA MÚLTIPLE -->
        <div class="bold">PRODUCTOS:</div>
        
        @foreach($venta->productos as $index => $producto)
        <div style="margin-bottom: 8px; border-bottom: 1px dotted #ccc; padding-bottom: 4px;">
            <div class="bold" style="font-size: 9px;">{{ $index + 1 }}. {{ $producto->lote->codigo_lote ?? 'N/A' }}</div>
            
            <div class="row" style="font-size: 9px;">
                <span>Especie:</span>
                <span>{{ $producto->lote->especie ?? 'Sin especie' }}</span>
            </div>
            
            <div class="row" style="font-size: 9px;">
                <span>Cantidad:</span>
                <span>{{ $producto->cantidad_cosechada ?? 0 }} peces</span>
            </div>
            
            <div class="row" style="font-size: 9px;">
                <span>Peso:</span>
                <span>{{ number_format(($producto->peso_cosechado_kg ?? 0) * 2.20462, 2) }} lb</span>
            </div>
            
            <div class="row" style="font-size: 9px;">
                <span>Precio/lb:</span>
                <span>Q{{ number_format(($producto->precio_kg ?? 0) / 2.20462, 2) }}</span>
            </div>
            
            <div class="row" style="font-size: 9px;">
                <span>Subtotal:</span>
                <span class="bold">Q{{ number_format($producto->total_venta ?? 0, 2) }}</span>
            </div>
        </div>
        @endforeach
        
        <div class="row" style="margin-top: 8px;">
            <span class="bold">Total productos:</span>
            <span class="bold">{{ $venta->productos->count() }}</span>
        </div>
        
        <div class="row">
            <span class="bold">Total peces:</span>
            <span class="bold">{{ $venta->productos->sum('cantidad_cosechada') }}</span>
        </div>
        
        <div class="row">
            <span class="bold">Total peso:</span>
            <span class="bold">{{ number_format($venta->productos->sum('peso_cosechado_kg') * 2.20462, 2) }} lb</span>
        </div>
        
    @else
        <!-- VENTA INDIVIDUAL -->
        <div class="bold">PRODUCTO:</div>
        
        @if(isset($venta->lote) && $venta->lote)
        <div class="row">
            <span>Lote:</span>
            <span>{{ $venta->lote->codigo_lote ?? 'N/A' }}</span>
        </div>
        
        <div class="row">
            <span>Especie:</span>
            <span>{{ $venta->lote->especie ?? 'Sin especie' }}</span>
        </div>
        @else
        <div class="row">
            <span>Lote:</span>
            <span>{{ $venta->lote_id ?? 'N/A' }}</span>
        </div>
        @endif
        
        <div class="row">
            <span>Cantidad:</span>
            <span>{{ $venta->cantidad_cosechada ?? 0 }} peces</span>
        </div>
        
        <div class="row">
            <span>Peso:</span>
            <span>{{ number_format(($venta->peso_cosechado_kg ?? 0) * 2.20462, 2) }} lb</span>
        </div>
    @endif
    
    <div class="separator"></div>
    
    <!-- TOTALES -->
    <div class="total-section">
        @if(isset($venta->es_venta_multiple) && $venta->es_venta_multiple)
            <div class="row">
                <span>Precio promedio/lb:</span>
                <span>Q{{ number_format($venta->total_venta / ($venta->productos->sum('peso_cosechado_kg') * 2.20462), 2) }}</span>
            </div>
        @else
            <div class="row">
                <span>Precio/lb:</span>
                <span>Q{{ number_format(($venta->precio_kg ?? 0) / 2.20462, 2) }}</span>
            </div>
        @endif
        
        <div class="row total-final">
            <span>TOTAL:</span>
            <span>Q{{ number_format($venta->total_venta ?? 0, 2) }}</span>
        </div>
    </div>
    
    <div class="separator"></div>
    
    <!-- PIE DE PÁGINA -->
    <div class="footer">
        <div class="center bold">¡Gracias por su compra!</div>
        <div class="center">{{ now()->format('d/m/Y H:i:s') }}</div>
        <br>
        <div class="center">Conserve este ticket</div>
        <div class="center">como comprobante de venta</div>
    </div>
</body>
</html>