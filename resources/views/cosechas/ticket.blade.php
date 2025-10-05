<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ticket de Venta - {{ $cosecha->codigo_venta }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 10px;
            color: #666;
        }
        
        .ticket-info {
            margin-bottom: 20px;
        }
        
        .ticket-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            background-color: #f0f0f0;
            padding: 8px;
            border: 1px solid #ddd;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 2px 0;
        }
        
        .info-label {
            font-weight: bold;
            width: 40%;
        }
        
        .info-value {
            width: 60%;
        }
        
        .details-section {
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .section-header {
            background-color: #f8f9fa;
            padding: 8px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }
        
        .section-content {
            padding: 10px;
        }
        
        .totals {
            margin-top: 20px;
            border-top: 2px solid #000;
            padding-top: 15px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .total-final {
            font-size: 16px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-completada {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-pendiente {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-cancelada {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">SISTEMA DE ACUICULTURA</div>
        <div class="company-info">
            Gestión Integral de Cosechas y Ventas<br>
            Teléfono: +502 xxxx-xxxx | Email: info@acuicultura.gt
        </div>
    </div>

    <div class="ticket-title">
        TICKET DE VENTA DE COSECHA
    </div>

    <div class="ticket-info">
        <div class="info-row">
            <span class="info-label">Código de Venta:</span>
            <span class="info-value">{{ $cosecha->codigo_venta }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Venta:</span>
            <span class="info-value">{{ $cosecha->fecha_venta->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Emisión:</span>
            <span class="info-value">{{ now()->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Estado:</span>
            <span class="info-value">
                <span class="status-badge status-{{ $cosecha->estado_venta }}">
                    {{ strtoupper($cosecha->estado_venta) }}
                </span>
            </span>
        </div>
    </div>

    <div class="details-section">
        <div class="section-header">INFORMACIÓN DEL CLIENTE</div>
        <div class="section-content">
            <div class="info-row">
                <span class="info-label">Cliente:</span>
                <span class="info-value">{{ $cosecha->cliente }}</span>
            </div>
            @if($cosecha->telefono_cliente)
            <div class="info-row">
                <span class="info-label">Teléfono:</span>
                <span class="info-value">{{ $cosecha->telefono_cliente }}</span>
            </div>
            @endif
            @if($cosecha->email_cliente)
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $cosecha->email_cliente }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="details-section">
        <div class="section-header">🐟 INFORMACIÓN DE LA COSECHA</div>
        <div class="section-content">
            <div class="info-row">
                <span class="info-label">Lote:</span>
                <span class="info-value">{{ $cosecha->lote->codigo_lote ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de Cosecha:</span>
                <span class="info-value">{{ $cosecha->fecha->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cantidad Cosechada:</span>
                <span class="info-value">{{ number_format($cosecha->cantidad_cosechada) }} unidades</span>
            </div>
            <div class="info-row">
                <span class="info-label">Peso Total:</span>
                <span class="info-value">{{ number_format($cosecha->peso_cosechado_kg, 2) }} kg</span>
            </div>
            @if($cosecha->responsable)
            <div class="info-row">
                <span class="info-label">Responsable:</span>
                <span class="info-value">{{ $cosecha->responsable }}</span>
            </div>
            @endif
            @if($cosecha->observaciones)
            <div class="info-row">
                <span class="info-label">Observaciones de Cosecha:</span>
                <span class="info-value">{{ $cosecha->observaciones }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="details-section">
        <div class="section-header">DETALLES DE LA VENTA</div>
        <div class="section-content">
            <div class="info-row">
                <span class="info-label">Peso Vendido:</span>
                <span class="info-value">{{ number_format($cosecha->peso_cosechado_kg, 2) }} kg</span>
            </div>
            <div class="info-row">
                <span class="info-label">Precio por kg:</span>
                <span class="info-value">Q {{ number_format($cosecha->precio_kg, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Método de Pago:</span>
                <span class="info-value">{{ ucfirst($cosecha->metodo_pago) }}</span>
            </div>
            @if($cosecha->observaciones_venta)
            <div class="info-row">
                <span class="info-label">Observaciones de Venta:</span>
                <span class="info-value">{{ $cosecha->observaciones_venta }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal ({{ number_format($cosecha->peso_cosechado_kg, 2) }} kg × Q {{ number_format($cosecha->precio_kg, 2) }}):</span>
            <span>Q {{ number_format($cosecha->total_venta, 2) }}</span>
        </div>
        
        <div class="total-row">
            <span>Tipo de Cambio:</span>
            <span>Q {{ number_format($cosecha->tipo_cambio, 4) }} por USD</span>
        </div>
        
        <div class="total-row">
            <span>Equivalente en USD:</span>
            <span>USD $ {{ number_format($cosecha->total_usd, 2) }}</span>
        </div>
        
        <div class="total-row total-final">
            <span>TOTAL A PAGAR:</span>
            <span>Q {{ number_format($cosecha->total_venta, 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <p><strong>¡Gracias por su compra!</strong></p>
        <p>Este ticket es válido como comprobante de venta</p>
        <p>Sistema generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <hr style="margin: 10px 0; border: 1px solid #ddd;">
        <p style="font-size: 8px;">
            Ticket generado por el Sistema de Gestión de Acuicultura<br>
            Para consultas o reclamos, conserve este comprobante<br>
            Cosecha ID: {{ $cosecha->id }} | Lote: {{ $cosecha->lote->codigo_lote ?? 'N/A' }}
        </p>
    </div>
</body>
</html>
