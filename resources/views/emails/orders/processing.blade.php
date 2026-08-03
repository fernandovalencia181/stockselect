<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu pedido está siendo preparado</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f9f9f9; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #333333; border-radius: 8px; overflow: hidden; margin-top: 40px; }
        .header { background-color: #000000; padding: 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 4px; text-transform: uppercase; font-weight: 900; }
        .content { padding: 40px; text-align: center; }
        .content h2 { font-size: 24px; font-weight: 900; margin-bottom: 20px; color: #000000; }
        .content p { font-size: 16px; line-height: 1.6; color: #666666; margin-bottom: 20px; }
        .status-box { background-color: #fefce8; border: 1px solid #fde68a; border-radius: 12px; padding: 30px; margin: 30px 0; }
        .status-icon { font-size: 48px; margin-bottom: 15px; }
        .status-label { font-size: 12px; text-transform: uppercase; letter-spacing: 2px; color: #92400e; margin-bottom: 10px; font-weight: bold; }
        .status-text { font-size: 18px; font-weight: 700; color: #78350f; }
        .items-table { width: 100%; border-collapse: collapse; margin: 25px 0; text-align: left; }
        .items-table th { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #999999; padding: 10px 8px; border-bottom: 2px solid #eeeeee; }
        .items-table td { font-size: 14px; color: #333333; padding: 12px 8px; border-bottom: 1px solid #f3f4f6; }
        .items-table .total-row td { border-top: 2px solid #000000; border-bottom: none; font-weight: 900; font-size: 16px; }
        .footer { padding: 40px; text-align: center; font-size: 12px; color: #999999; }
        .footer p { margin: 5px 0; }
        .divider { height: 1px; background-color: #eeeeee; margin: 40px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main" role="presentation">
            <tr>
                <td class="header">
                    <h1>Stock Select</h1>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <h2>¡ESTAMOS PREPARANDO TU PEDIDO!</h2>
                    <p>Hola {{ $order->customer_name }},</p>
                    <p>Queríamos avisarte de que tu pedido <strong>#{{ $order->id }}</strong> ya está en nuestras manos y lo estamos preparando con mucho cuidado para ti.</p>

                    <div class="status-box">
                        <div class="status-icon">📦</div>
                        <div class="status-label">Estado Actual</div>
                        <div class="status-text">En Preparación</div>
                    </div>

                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="text-align: left;">Producto</th>
                                <th style="text-align: center;">Talla</th>
                                <th style="text-align: center;">Ud.</th>
                                <th style="text-align: right;">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'Producto' }}</td>
                                <td style="text-align: center;">{{ $item->variant->size ?? '-' }}</td>
                                <td style="text-align: center;">{{ $item->quantity }}</td>
                                <td style="text-align: right;">{{ number_format($item->price_at_time, 2) }} €</td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="3">Total</td>
                                <td style="text-align: right;">{{ number_format($order->total_amount, 2) }} €</td>
                            </tr>
                        </tbody>
                    </table>

                    <p>En breve recibirás otro email con el <strong>número de seguimiento</strong> cuando el paquete salga de nuestras instalaciones.</p>
                    <p style="font-size: 14px; color: #999999;">Si tienes alguna pregunta, no dudes en respondernos a este correo o escribirnos por WhatsApp.</p>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <div class="divider"></div>
                    <p>&copy; {{ date('Y') }} Stock Select. Todos los derechos reservados.</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
