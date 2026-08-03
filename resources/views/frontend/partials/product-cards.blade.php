{{-- 
    Partial: product-cards.blade.php
    Renderiza las cards del catálogo. Se usa en:
      - home.blade.php (primera carga, SSR)
      - HomeController (respuesta AJAX para "Cargar más")
    
    Variables esperadas: $products (LengthAwarePaginator o Collection)
--}}
@foreach($products as $product)
    @php
        $totalStock = $product->variants->sum('stock');
        $isSoldOut = $totalStock === 0;
        $firstImage = null;
        if (!empty($product->images)) {
            $firstImage = is_array($product->images) ? $product->images[0] : $product->images;
        }
    @endphp

    <a href="{{ route('product.show', $product->slug) }}"
        class="group block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-[0_12px_36px_-10px_rgba(0,0,0,0.12)] transition-all duration-300 hover:-translate-y-1 flex flex-col border border-gray-100 {{ $isSoldOut ? 'opacity-75' : '' }}">

        {{-- Imagen --}}
        <div class="relative w-full aspect-square bg-[#f4f4f4] overflow-hidden">

            @if($firstImage)
                <img src="{{ asset('storage/' . $firstImage) }}" alt="{{ $product->name }}"
                    class="w-full h-full object-cover transition-transform duration-700 ease-out {{ $isSoldOut ? 'grayscale opacity-50' : 'group-hover:scale-105' }}"
                    loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=f4f4f4&color=9ca3af&bold=true&size=400"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover"
                        loading="lazy">
                </div>
            @endif

            {{-- Badge descuento --}}
            @if($product->original_price)
                @php $pct = round((($product->original_price - $product->price) / $product->original_price) * 100); @endphp
                <span class="absolute top-2.5 left-2.5 text-[10px] font-bold text-gray-900 bg-white/80 backdrop-blur-md border border-white/50 px-2.5 py-1 rounded-full shadow-[0_4px_12px_rgba(0,0,0,0.08)]">
                    −{{ $pct }}%
                </span>
            @endif

            {{-- Badge stock --}}
            @if($isSoldOut)
                <span class="absolute top-2.5 right-2.5 text-[10px] font-bold tracking-wider text-white bg-gray-900/80 px-3 py-1.5 rounded-full backdrop-blur-sm">
                    AGOTADO
                </span>
            @elseif($totalStock === 1)
                <span class="absolute top-2.5 right-2.5 text-[10px] font-semibold text-red-600 bg-white/90 px-2.5 py-1 rounded-full backdrop-blur-sm shadow-sm">
                    Última unidad
                </span>
            @endif
        </div>

        {{-- Info --}}
        <div class="p-3 lg:p-3.5 flex flex-col flex-1">
            <p class="text-[9px] lg:text-[10px] font-semibold uppercase tracking-[0.12em] text-gray-400 mb-0.5">
                {{ $product->category->name ?? 'Outlet' }}
            </p>

            <h3 class="text-[13px] lg:text-[13px] font-bold text-gray-900 leading-snug line-clamp-2 mb-2 group-hover:text-amber-500 transition-colors duration-300">
                {{ $product->name }}
            </h3>

            {{-- Precio --}}
            <div class="mt-auto flex items-baseline gap-2">
                <p class="text-[14px] lg:text-[15px] font-black text-gray-900 leading-none">{{ number_format($product->price, 2) }} €</p>
                @if($product->original_price)
                    <p class="text-[11px] lg:text-[12px] text-gray-400 line-through font-medium">{{ number_format($product->original_price, 2) }} €</p>
                @endif
            </div>
        </div>
    </a>
@endforeach
