<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $pedido->codigo_pedido }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 0; padding: 18px; }
        .ticket { max-width: 360px; margin: 0 auto; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        .muted { color: #64748b; font-size: 12px; }
        .row { display: flex; justify-content: space-between; gap: 12px; padding: 6px 0; border-bottom: 1px dashed #d1d5db; }
        .section { margin-top: 16px; }
        .total { font-weight: 800; font-size: 16px; }
        @media print { body { padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="ticket">
        <button class="no-print" onclick="window.print()" style="margin-bottom:16px;">Imprimir</button>
        <h1>Pedido WhatsApp {{ $pedido->codigo_pedido }}</h1>
        <div class="muted">{{ optional($pedido->created_at)->format('d/m/Y H:i') }}</div>

        <div class="section">
            <strong>{{ $pedido->cliente_nombre }}</strong><br>
            WhatsApp: {{ $pedido->cliente_whatsapp }}<br>
            Direccion: {{ $pedido->cliente_direccion ?: 'Sin direccion' }}<br>
            @if($pedido->cliente_referencia)
                Referencia: {{ $pedido->cliente_referencia }}<br>
            @endif
            Zona: {{ optional($pedido->zonaDelivery)->nombre ?: 'Sin zona' }}
        </div>

        <div class="section">
            @foreach($pedido->detalles as $detalle)
                <div class="row">
                    <span>{{ $detalle->cantidad_confirmada }} x {{ $detalle->nombre_producto }}</span>
                    <strong>S/ {{ number_format((float) $detalle->subtotal, 2) }}</strong>
                </div>
            @endforeach
        </div>

        <div class="section">
            <div class="row"><span>Productos</span><strong>S/ {{ number_format((float) $pedido->total_productos, 2) }}</strong></div>
            <div class="row"><span>Delivery</span><strong>S/ {{ number_format((float) $pedido->costo_delivery, 2) }}</strong></div>
            <div class="row total"><span>Total</span><strong>S/ {{ number_format((float) $pedido->total_pedido, 2) }}</strong></div>
        </div>

        @if($pedido->nota_interna)
            <div class="section">
                <strong>Nota</strong><br>
                {{ $pedido->nota_interna }}
            </div>
        @endif
    </div>
</body>
</html>
