<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu pedido está en camino</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f9f9f9; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #333333; border-radius: 8px; overflow: hidden; margin-top: 40px; }
        .header { background-color: #000000; padding: 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 4px; text-transform: uppercase; font-weight: 900; }
        .content { padding: 40px; text-align: center; }
        .content h2 { font-size: 24px; font-weight: 900; margin-bottom: 20px; color: #000000; }
        .content p { font-size: 16px; line-height: 1.6; color: #666666; margin-bottom: 20px; }
        .tracking-box { background-color: #f3f4f6; border-radius: 12px; padding: 30px; margin: 30px 0; }
        .tracking-label { font-size: 12px; text-transform: uppercase; letter-spacing: 2px; color: #999999; margin-bottom: 10px; font-weight: bold; }
        .tracking-code { font-size: 24px; font-weight: 900; color: #000000; letter-spacing: 2px; word-break: break-all; }
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
                    <h2>¡TU PEDIDO ESTÁ EN CAMINO!</h2>
                    <p>Hola {{ $order->customer_name }},</p>
                    <p>Te informamos de que tu pedido <strong>#{{ $order->id }}</strong> acaba de salir de nuestras instalaciones y ya ha sido entregado a la empresa de transporte.</p>
                    
                    @if($order->tracking_number)
                    <div class="tracking-box">
                        <div class="tracking-label">Número de Seguimiento</div>
                        <div class="tracking-code">{{ $order->tracking_number }}</div>
                        <p style="font-size: 13px; margin-top: 15px; margin-bottom: 0; color: #666666;">Puedes utilizar este código en la página web del transportista para conocer el estado exacto de tu envío.</p>
                    </div>
                    @else
                    <div class="tracking-box">
                        <p style="font-size: 14px; margin: 0; color: #666666;">El pedido ha sido enviado. Nos pondremos en contacto contigo si hubiera cualquier actualización.</p>
                    </div>
                    @endif

                    <p>Recuerda que los envíos estándar suelen tardar entre <strong>3 y 5 días laborables</strong> en llegar a su destino.</p>
                    <p>Si tienes alguna duda con tu paquete, no dudes en responder a este correo.</p>
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
