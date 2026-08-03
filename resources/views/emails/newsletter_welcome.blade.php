<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Stock Select</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f9f9f9; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; color: #333333; border-radius: 8px; overflow: hidden; margin-top: 40px; }
        .header { background-color: #000000; padding: 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 4px; text-transform: uppercase; font-weight: 900; }
        .content { padding: 40px; text-align: center; }
        .content h2 { font-size: 28px; font-weight: 900; margin-bottom: 20px; color: #000000; }
        .content p { font-size: 16px; line-height: 1.6; color: #666666; margin-bottom: 30px; }
        .coupon-box { background-color: #f3f4f6; border: 2px dashed #000000; border-radius: 12px; padding: 30px; margin: 30px 0; }
        .coupon-label { font-size: 12px; text-transform: uppercase; letter-spacing: 2px; color: #999999; margin-bottom: 10px; font-weight: bold; }
        .coupon-code { font-size: 36px; font-weight: 900; color: #000000; letter-spacing: 5px; }
        .btn { display: inline-block; background-color: #000000; color: #ffffff; padding: 18px 36px; text-decoration: none; border-radius: 50px; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 2px; transition: background-color 0.3s ease; }
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
                    <h2>¡HOLA {{ strtoupper($subscriber->name ?? 'FASHIONISTA') }}!</h2>
                    <p>Gracias por unirte a la elite de <strong>Stock Select</strong>. Ahora eres parte de un círculo exclusivo que recibe las mejores ofertas en moda urbana y sneakers de edición limitada antes que nadie.</p>
                    
                    <div class="coupon-box">
                        <div class="coupon-label">Tu regalo de bienvenida</div>
                        <div class="coupon-code">WELCOME5</div>
                        <p style="font-size: 13px; margin-top: 15px; margin-bottom: 0; color: #666666;">Aplica este código en tu próxima compra para obtener un <strong>5% de DESCUENTO</strong> inmediato.</p>
                    </div>

                    <p>Nuestra colección vuela rápido. No dejes que se te escape lo que tienes en el radar.</p>
                    
                    <a href="{{ url('/') }}" class="btn">Explorar la Tienda</a>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <div class="divider"></div>
                    <p>&copy; {{ date('Y') }} Stock Select. Todos los derechos reservados.</p>
                    <p>Has recibido este correo electrónico porque te suscribiste a nuestra newsletter durante el proceso de compra o en nuestra web.</p>
                    <p style="margin-top: 15px;">
                        <a href="{{ url('/ajustes-privacidad') }}" style="color: #999999; text-decoration: underline;">Gestionar Preferencias</a> | 
                        <a href="{{ url('/baja-newsletter') }}" style="color: #999999; text-decoration: underline;">Darse de baja</a>
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
