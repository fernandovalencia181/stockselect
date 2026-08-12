<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-3xl font-black text-gray-900 mb-2">Tu carrito</h1>
        <p class="text-gray-500 text-sm mb-8">Revisa tus artículos antes de pagar — sin registro necesario.</p>

        @if(session('error'))
            <div
                class="mb-6 flex items-center gap-3 bg-red-50 border border-red-100 text-red-600 rounded-2xl px-5 py-3.5 text-[13px] font-medium">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div
                class="mb-6 flex items-center gap-3 bg-gray-50 border border-gray-200 text-gray-700 rounded-2xl px-5 py-3.5 text-[13px] font-medium">
                <svg class="w-4 h-4 shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(empty($cart))
            {{-- Carrito vacío - Diseño Premium --}}
            <div class="text-center py-20 md:py-32 bg-white rounded-3xl border border-dashed border-gray-200 shadow-sm px-6 relative overflow-hidden">
                {{-- Decoración de fondo --}}
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-gray-50 rounded-full blur-3xl opacity-50"></div>
                <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-gray-50 rounded-full blur-3xl opacity-50"></div>

                <div class="mb-6 relative inline-block">
                    <div class="absolute inset-0 bg-gray-50 rounded-full scale-150 blur-xl opacity-50"></div>
                    <svg class="h-20 w-20 text-gray-200 relative" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 mb-4 tracking-tight">Tu carrito está esperando.</h2>
                <p class="text-[14px] md:text-[15px] text-gray-400 max-w-sm mx-auto mb-10 leading-relaxed font-medium">
                    Parece que aún no has añadido nada a tu selección. Explora nuestra moda urbana y estrena look hoy mismo.
                </p>
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-3 bg-gray-900 text-white font-bold px-10 py-4 rounded-2xl hover:bg-black transition-all shadow-xl active:scale-95 group">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Seguir explorando
                </a>
            </div>

        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ===== LISTA DE ARTÍCULOS ===== --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cart as $cartKey => $item)
                        @php
                            $productModel = \App\Models\Product::find($item['id']);
                            $cartVariant = \App\Models\ProductVariant::find($item['variant_id'] ?? null);
                            $maxStock = $cartVariant ? $cartVariant->stock : 999;
                            $atMax = $item['quantity'] >= $maxStock;
                        @endphp
                        <div
                            class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row gap-4 items-start sm:items-center group hover:border-gray-200 transition-all">

                            {{-- Imagen del producto --}}
                            <div
                                class="w-20 h-20 rounded-xl bg-[#F5F5F7] overflow-hidden shrink-0 flex items-center justify-center p-1 border border-gray-100">
                                @if($productModel && !empty($productModel->images))
                                    <img src="{{ Storage::url($productModel->images[0]) }}" alt="{{ $item['name'] }}"
                                        class="w-full h-full object-contain mix-blend-multiply">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($item['name']) }}&background=E5E7EB&color=111827&bold=true&size=200"
                                        alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-900 line-clamp-2 text-sm">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">Talla: {{ $item['size'] ?? 'Única' }}</p>
                                <p class="text-lg font-black text-gray-900 mt-1">{{ number_format($item['price'], 2) }} €</p>
                            </div>

                            {{-- Controles de cantidad --}}
                            <div class="flex items-center gap-3 shrink-0">
                                <form action="{{ route('cart.update', $cartKey) }}" method="POST"
                                    class="flex items-center gap-2">
                                    @csrf
                                    <div class="flex items-center border border-gray-200 rounded-full overflow-hidden">
                                        <button type="submit" name="quantity" value="{{ max(0, $item['quantity'] - 1) }}"
                                            class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 transition-colors text-gray-600 font-bold text-lg">−</button>
                                        <span
                                            class="w-8 text-center text-sm font-bold text-gray-900">{{ $item['quantity'] }}</span>
                                        <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}"
                                            class="w-8 h-8 flex items-center justify-center transition-colors font-bold text-lg {{ $atMax ? 'opacity-30 cursor-not-allowed pointer-events-none text-gray-300' : 'hover:bg-gray-100 text-gray-600' }}"
                                            {{ $atMax ? 'disabled' : '' }}>+</button>
                                    </div>
                                </form>

                                {{-- Eliminar --}}
                                <form action="{{ route('cart.remove', $cartKey) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-full transition-all"
                                        title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach

                    {{-- Botones de acción secundarios --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <a href="{{ route('home') }}"
                            class="flex items-center justify-center gap-2 text-sm font-medium text-gray-600 hover:text-black border border-gray-200 px-4 py-2.5 rounded-full hover:border-black transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Seguir comprando
                        </a>

                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 text-sm font-medium text-red-400 hover:text-red-600 px-4 py-2.5 rounded-full hover:bg-red-50 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Vaciar carrito
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ===== RESUMEN DEL PEDIDO ===== --}}
                <div class="lg:col-span-1">
                    <div x-data="{
                                    subtotal: {{ $subtotal }},
                                    fmt(n) { return n.toFixed(2).replace('.', ',') + ' €'; }
                                }" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24">

                        <h2 class="text-lg font-black text-gray-900 mb-5">Resumen del pedido</h2>

                        {{-- Líneas de precio --}}
                        <div class="space-y-3 text-sm mb-4">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal ({{ collect($cart)->sum('quantity') }} art.)</span>
                                <span class="font-semibold text-gray-900">{{ number_format($subtotal, 2, ',', '.') }} €</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-600">
                                <span>Entrega en mano</span>
                                <span class="font-bold text-green-600 bg-green-50 px-2.5 py-0.5 rounded-full text-xs">Envío gratis</span>
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="flex justify-between items-center border-t border-gray-100 pt-4 mb-5">
                            <span class="font-bold text-gray-900 text-base">Total a pagar</span>
                            <span class="text-2xl font-black text-gray-900"
                                x-text="fmt(subtotal)">{{ number_format($subtotal, 2, ',', '.') }} €</span>
                        </div>

                        {{-- Información de entrega y pago --}}
                        <div class="mb-6 bg-gray-50 p-4 rounded-2xl border border-gray-100 space-y-2.5">
                            <div class="flex items-center gap-2.5 text-xs text-gray-700 font-bold">
                                <span class="text-base">📍</span>
                                <span>Entrega en mano (Gratis)</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-xs text-gray-700 font-bold">
                                <span class="text-base">💶</span>
                                <span>Pago en efectivo al recibir la prenda</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-xs text-gray-500 font-medium">
                                <span class="text-base">💬</span>
                                <span>Coordinamos día y hora por WhatsApp</span>
                            </div>
                        </div>

                        {{-- Botón ir al Checkout --}}
                        <a href="{{ route('checkout.index') }}"
                            class="w-full flex items-center justify-center gap-2 bg-[#128C7E] hover:bg-[#075E54] text-white font-extrabold py-3.5 px-6 rounded-2xl transition-all shadow-lg text-sm hover:shadow-xl hover:-translate-y-0.5 duration-200">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Tramitar pedido por WhatsApp
                        </a>

                        <p class="text-center text-xs text-gray-400 mt-3">
                            Entrega en mano · Pago en efectivo
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>