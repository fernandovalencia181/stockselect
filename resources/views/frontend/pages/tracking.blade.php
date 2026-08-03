<x-app-layout title="Seguimiento de Pedido">
    <div class="max-w-4xl mx-auto px-5 sm:px-8 lg:px-10 py-16 md:py-24">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-gray-900 mb-4">Seguimiento de Pedido</h1>
            <p class="text-lg text-gray-500">Introduce tus datos para conocer el estado actual de tu envío.</p>
        </div>

        {{-- Formulario de búsqueda --}}
        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 md:p-12 shadow-sm mb-12">
            <form action="{{ route('pages.tracking') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @csrf
                <div class="md:col-span-1">
                    <label for="order_id" class="block text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">ID del Pedido</label>
                    <input type="text" name="order_id" id="order_id" value="{{ old('order_id') }}" placeholder="Ej: 12345"
                           class="w-full bg-gray-50 border-0 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-gray-900 transition-all text-gray-900 font-medium">
                </div>
                <div class="md:col-span-1">
                    <label for="email" class="block text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Email de Compra</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="tu@email.com"
                           class="w-full bg-gray-50 border-0 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-gray-900 transition-all text-gray-900 font-medium">
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold h-[58px] rounded-2xl transition-all shadow-lg active:scale-[0.98]">
                        Localizar Envío
                    </button>
                </div>
            </form>
            @error('tracking')
                <p class="mt-4 text-sm text-red-500 font-medium text-center">{{ $message }}</p>
            @enderror
        </div>

        @if($searched && $order)
            {{-- Resultados del Seguimiento --}}
            <div class="space-y-12 animate-in fade-in slide-in-from-bottom-4 duration-700">
                
                {{-- Card de Estado con Timeline --}}
                <div class="bg-gray-950 text-white rounded-[3rem] p-8 md:p-12 shadow-2xl overflow-hidden relative">
                    {{-- Decoración de fondo --}}
                    <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12 relative z-10">
                        <div>
                            <p class="text-amber-500 text-[11px] font-black uppercase tracking-[0.2em] mb-2">Pedido #{{ $order->id }}</p>
                            <h2 class="text-3xl font-black">
                                @if($order->status == 'pending') Recibido
                                @elseif($order->status == 'paid') Pagado
                                @elseif($order->status == 'processing') En Preparación
                                @elseif($order->status == 'shipped') Enviado
                                @elseif($order->status == 'delivered') Entregado
                                @else {{ ucfirst($order->status) }} @endif
                            </h2>
                        </div>
                        <div class="text-left md:text-right">
                            <p class="text-gray-500 text-[11px] font-black uppercase tracking-widest mb-1">Fecha de Pedido</p>
                            <p class="text-lg font-bold">{{ $order->created_at->format('d M, Y') }}</p>
                        </div>
                    </div>

                    {{-- Timeline Visual --}}
                    @php
                        $steps = [
                            'pending' => ['label' => 'Recibido', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'paid' => ['label' => 'Pagado', 'icon' => 'M5 13l4 4L19 7'],
                            'processing' => ['label' => 'En Preparación', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'shipped' => ['label' => 'Enviado', 'icon' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8'],
                            'delivered' => ['label' => 'Entregado', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ];
                        $current_status = $order->status;
                        $found_current = false;
                        $status_order = ['pending', 'paid', 'processing', 'shipped', 'delivered'];
                    @endphp

                    <div class="relative pt-8 pb-4 z-10">
                        <div class="absolute top-1/2 left-0 w-full h-0.5 bg-gray-800 -translate-y-11 hidden md:block"></div>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-8 md:gap-0 relative">
                            @foreach($status_order as $idx => $s)
                                @php
                                    $is_done = array_search($current_status, $status_order) >= $idx;
                                    $is_active = $current_status == $s;
                                @endphp
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-14 h-14 rounded-2xl mb-4 flex items-center justify-center transition-all duration-500 shadow-xl
                                                {{ $is_done ? 'bg-amber-500 text-gray-950 scale-110 shadow-amber-500/20' : 'bg-gray-800 text-gray-500' }}">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $steps[$s]['icon'] }}" />
                                        </svg>
                                    </div>
                                    <p class="text-[12px] font-black uppercase tracking-widest {{ $is_done ? 'text-white' : 'text-gray-600' }}">{{ $steps[$s]['label'] }}</p>
                                    @if($is_done && $idx < count($status_order) - 1)
                                        {{-- Línea de progreso móvil/secuencial --}}
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Card de Productos --}}
                <div class="bg-white border border-gray-100 rounded-[3rem] p-8 md:p-12 shadow-sm">
                    <h3 class="text-2xl font-black text-gray-900 mb-8">Artículos en este pedido</h3>
                    <div class="space-y-6">
                        @foreach($order->items as $item)
                        <div class="flex items-center gap-6 p-4 rounded-3xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                            <div class="w-24 h-24 bg-white rounded-2xl overflow-hidden flex-shrink-0 border border-gray-100">
                                @php
                                    $image = null;
                                    if ($item->product && !empty($item->product->images)) {
                                        $image = is_array($item->product->images) ? $item->product->images[0] : $item->product->images;
                                    }
                                @endphp
                                @if($image)
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $item->product->name ?? 'Producto' }}" class="w-full h-full object-contain mix-blend-multiply">
                                @else
                                    <div class="w-full h-full bg-gray-50 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-gray-900 leading-tight">{{ $item->product->name ?? 'Producto no disponible' }}</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    Talla: <span class="text-gray-900 font-bold uppercase">{{ $item->size ?? 'N/A' }}</span> 
                                    <span class="mx-2 text-gray-200">|</span> 
                                    Cantidad: <span class="text-gray-900 font-bold">{{ $item->quantity }}</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-gray-900">{{ number_format($item->price_at_time, 2) }} €</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="flex flex-col gap-1">
                            <p class="text-[11px] font-black uppercase tracking-widest text-gray-400">Dirección de Entrega</p>
                            <p class="text-gray-900 font-medium">{{ $order->customer_name }}<br>{{ $order->shipping_address }}<br>{{ $order->postal_code }} {{ $order->city }}</p>
                        </div>
                        <div class="bg-gray-50 px-8 py-6 rounded-[2rem] text-right space-y-2 min-w-[200px]">
                            <div class="flex justify-between gap-4 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                <span>Subtotal</span>
                                <span>{{ number_format($order->total_amount - $order->shipping_cost, 2) }} €</span>
                            </div>
                            <div class="flex justify-between gap-4 text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-200 pb-2">
                                <span>Envío</span>
                                <span>{{ number_format($order->shipping_cost, 2) }} €</span>
                            </div>
                            <div class="flex justify-between gap-4 pt-1">
                                <span class="text-[11px] font-black uppercase tracking-widest text-gray-400 self-center">Total</span>
                                <span class="text-3xl font-black text-gray-900">{{ number_format($order->total_amount, 2) }} €</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @elseif($searched)
             <div class="p-10 text-center bg-red-50 rounded-[2.5rem] border border-red-100">
                <p class="text-red-600 font-bold">No hemos podido encontrar el pedido.</p>
                <p class="text-red-500/80 text-sm mt-2">Por favor, verifica que el ID y el Email sean correctos.</p>
             </div>
        @endif
    </div>
</x-app-layout>
