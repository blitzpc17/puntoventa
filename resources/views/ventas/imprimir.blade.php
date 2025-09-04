<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta - {{ $venta->folio }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .ticket { width: 80mm; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 10px; }
        .item { margin-bottom: 5px; }
        .total { font-weight: bold; margin-top: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .border-top { border-top: 1px dashed #000; padding-top: 5px; }
        .small { font-size: 10px; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h2>Papelería System</h2>
            <p>Ticket de Venta</p>
            <p><strong>Folio:</strong> {{ $venta->folio }}</p>
        </div>
        
        <div class="border-top">
            <p><strong>Fecha:</strong> {{ $venta->fecha->format('d/m/Y H:i') }}</p>
            <p><strong>Vendedor:</strong> Vendedor prueba</p>
            @if($venta->cliente)
            <p><strong>Cliente:</strong> {{ $venta->cliente->nombre }}</p>
            @endif
        </div>

        <div class="border-top">
            <table width="100%">
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cant</th>
                    <th>Total</th>
                </tr>
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td>${{ number_format($detalle->precio, 2) }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <div class="border-top total">
            <p class="text-right">TOTAL: ${{ number_format($venta->total, 2) }}</p>
            <p><strong>Pago:</strong> {{ strtoupper($venta->tipo_pago) }}</p>
            @if($venta->tipo_pago == 'efectivo')
            <p><strong>Efectivo:</strong> ${{ number_format($venta->efectivo, 2) }}</p>
            <p><strong>Cambio:</strong> ${{ number_format($venta->cambio, 2) }}</p>
            @endif
        </div>

        <div class="border-top text-center small">
            <p>¡Gracias por su compra!</p>
            <p>Tel: (123) 456-7890</p>
            <p>{{ config('app.url') }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 500);
        }
    </script>
</body>
</html>