<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ticket de Venta - {{ $venta->codigo_venta }}</title>
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
        
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">SISTEMA DE ACUICULTURA</div>
        <div class="company-info">
            Gestión Integral de Cosechas y Ventas<br>
            Teléfono: +505 xxxx-xxxx | Email: info@acuicultura.com
        </div>
    </div>

    <div class="ticket-title">
        TICKET DE VENTA
    </div>

    <div class="ticket-info">
        <div class="info-row">
            <span class="info-label">Código de Venta:</span>
            <span class="info-value">{{ $venta->codigo_venta }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Venta:</span>
            <span class="info-value">{{ $venta->fecha_venta->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Emisión:</span>
            <span class="info-value">{{ now()->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Estado:</span>
            <span class="info-value">
                <span class="status-badge status-{{ $venta->estado }}">
                    {{ strtoupper($venta->estado) }}
                </span>
            </span>
        </div>
    </div>

    <div class="details-section">
        <div class="section-header">📋 INFORMACIÓN DEL CLIENTE</div>
        <div class="section-content">
            <div class="info-row">
                <span class="info-label">Cliente:</span>
                <span class="info-value">{{ $venta->cliente }}</span>
            </div>
            @if($venta->telefono_cliente)
            <div class="info-row">
                <span class="info-label">Teléfono:</span>
                <span class="info-value">{{ $venta->telefono_cliente }}</span>
            </div>
            @endif
            @if($venta->email_cliente)
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $venta->email_cliente }}</span>
            </div>
            @endif
        </div>
    </div>

    @if($venta->cosechaParcial)
    <div class="details-section">
        <div class="section-header">🐟 INFORMACIÓN DE LA COSECHA</div>
        <div class="section-content">
            <div class="info-row">
                <span class="info-label">Lote:</span>
                <span class="info-value">{{ $venta->cosechaParcial->lote->codigo ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de Cosecha:</span>
                <span class="info-value">{{ $venta->cosechaParcial->fecha->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cantidad Cosechada:</span>
                <span class="info-value">{{ number_format($venta->cosechaParcial->cantidad, 2) }} kg</span>
            </div>
            @if($venta->cosechaParcial->observaciones)
            <div class="info-row">
                <span class="info-label">Observaciones:</span>
                <span class="info-value">{{ $venta->cosechaParcial->observaciones }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="details-section">
        <div class="section-header">💰 DETALLES DE LA VENTA</div>
        <div class="section-content">
            <div class="info-row">
                <span class="info-label">Cantidad Vendida:</span>
                <span class="info-value">{{ number_format($venta->cantidad_kg, 2) }} kg</span>
            </div>
            <div class="info-row">
                <span class="info-label">Precio por kg:</span>
                <span class="info-value">C$ {{ number_format($venta->precio_kg, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Método de Pago:</span>
                <span class="info-value">{{ ucfirst($venta->metodo_pago) }}</span>
            </div>
            @if($venta->observaciones)
            <div class="info-row">
                <span class="info-label">Observaciones:</span>
                <span class="info-value">{{ $venta->observaciones }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal ({{ number_format($venta->cantidad_kg, 2) }} kg × C$ {{ number_format($venta->precio_kg, 2) }}):</span>
            <span>C$ {{ number_format($venta->total, 2) }}</span>
        </div>
        
        <div class="total-row">
            <span>Tipo de Cambio:</span>
            <span>C$ {{ number_format($venta->tipo_cambio, 4) }} por USD</span>
        </div>
        
        <div class="total-row">
            <span>Equivalente en USD:</span>
            <span>USD $ {{ number_format($venta->total_usd, 2) }}</span>
        </div>
        
        <div class="total-row total-final">
            <span>TOTAL A PAGAR:</span>
            <span>C$ {{ number_format($venta->total, 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <p><strong>¡Gracias por su compra!</strong></p>
        <p>Este ticket es válido como comprobante de venta</p>
        <p>Sistema generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <hr style="margin: 10px 0; border: 1px solid #ddd;">
        <p style="font-size: 8px;">
            Ticket generado por el Sistema de Gestión de Acuicultura<br>
            Para consultas o reclamos, conserve este comprobante
        </p>
    </div>
</body>
</html>