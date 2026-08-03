<x-mail::message>
# ¡Gracias por tu pedido, {{ $order->customer_name }}!

Tu pedido **#{{ $order->id }}** ha sido confirmado y ya estamos preparando tus artículos.

<x-mail::table>
| Producto | Cantidad | Precio |
| :--- | :---: | :--- |
@foreach($order->items as $item)
| {{ $item->product->name }} (Talla {{ $item->variant->size ?? '-' }}) | {{ $item->quantity }} | {{ number_format($item->price_at_time, 2) }} € |
@endforeach
| **Total** | | **{{ number_format($order->total_amount, 2) }} €** |
</x-mail::table>

**Método de envío:** {{ $order->shipping_method === 'local_pickup' ? 'Recogida Local (Mollerussa)' : 'Envío Standard' }}  
**Dirección:** {{ $order->shipping_address ?? 'No indicada' }}

<x-mail::button :url="config('app.url')">
Visitar Stock Select
</x-mail::button>

Si tienes cualquier duda, puedes responder a este email o contactarnos por WhatsApp.

Gracias por elegir **Stock Select**.
</x-mail::message>
