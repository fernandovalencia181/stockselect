<x-app-layout :title="$product->name" :description="str($product->description)->limit(160)">
    @push('meta')
        @php
            $mainImage = null;
            if (!empty($product->images)) {
                $mainImage = asset('storage/' . (is_array($product->images) ? $product->images[0] : $product->images));
            }
        @endphp
        @if($mainImage)
            <meta property="og:image" content="{{ $mainImage }}">
            <meta property="twitter:image" content="{{ $mainImage }}">
            {{-- Preload crucial LCP Image --}}
            <link rel="preload" as="image" href="{{ $mainImage }}" fetchpriority="high">
        @endif
        <meta property="og:type" content="product">
        
        <style>
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-boutique-in {
                animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            .anim-delay-100 { animation-delay: 0.1s; }
            .anim-delay-200 { animation-delay: 0.2s; }
            .anim-delay-300 { animation-delay: 0.3s; }
        </style>
    @endpush
    <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 py-6 md:py-16" x-data="{ openSizeGuide: false }">
                {{-- Breadcrumb --}}
        <nav class="mb-3 md:mb-8 w-full" aria-label="Breadcrumb">
            <ol class="flex items-center gap-1.5 overflow-x-auto whitespace-nowrap pb-2 -mb-2 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                <li class="flex-shrink-0">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-1 text-[11px] md:text-[12px] font-semibold text-gray-400 hover:text-gray-700 transition-colors">
                        <svg class="w-3 h-3 md:w-3.5 md:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Inicio
                    </a>
                </li>
                <li class="flex-shrink-0"><svg class="w-3 h-3 md:w-3.5 md:h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
                <li class="flex-shrink-0">
                    <a href="{{ route('home') }}?genero={{ $product->gender ?? 'Todo' }}&category={{ urlencode($product->category->name ?? '') }}"
                       class="inline-flex items-center text-[11px] md:text-[12px] font-semibold text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-2.5 md:px-3 py-1 md:py-1.5 rounded-full transition-all">
                        {{ $product->category->name ?? 'Outlet' }}
                    </a>
                </li>
                <li class="flex-shrink-0"><svg class="w-3 h-3 md:w-3.5 md:h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
                <li class="flex-shrink-0">
                    <span class="text-[11px] md:text-[12px] font-bold text-gray-700 block" aria-current="page">
                        {{ $product->name }}
                    </span>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-20">
            {{-- Galería de imágenes (Izquierda) --}}
            <div class="flex flex-col gap-4" x-data="{
                currentImage: 0,
                isLightboxOpen: false,
                isZoomed: false,
                images: {{ Js::from(is_array($product->images) ? $product->images : (empty($product->images) ? [] : [$product->images])) }},
                next() {
                    this.currentImage = (this.currentImage === this.images.length - 1) ? 0 : this.currentImage + 1;
                },
                prev() {
                    this.currentImage = (this.currentImage === 0) ? this.images.length - 1 : this.currentImage - 1;
                },
                initGestures(el) {
                    const self = this;

                    // Local JS vars — NOT Alpine reactive — for max performance on iOS
                    let scale = 1, panX = 0, panY = 0;
                    let pinchDist = 0;
                    let swipeStartX = 0, lastTX = 0, lastTY = 0;
                    let isPanning = false, touchMoved = false;

                    function getImg() { return el.querySelector('img'); }

                    function applyTransform(smooth) {
                        const img = getImg();
                        if (!img) return;
                        img.style.transition = smooth ? 'transform 0.3s cubic-bezier(0.25,0.46,0.45,0.94)' : 'none';
                        img.style.transform = scale <= 1
                            ? 'translate(0px,0px) scale(1)'
                            : `translate(${panX}px,${panY}px) scale(${scale})`;
                    }

                    function resetAll(smooth) {
                        scale = 1; panX = 0; panY = 0;
                        applyTransform(smooth);
                        self.isZoomed = false;
                    }

                    el.addEventListener('touchstart', (e) => {
                        touchMoved = false;
                        if (e.touches.length === 2) {
                            pinchDist = Math.hypot(
                                e.touches[0].clientX - e.touches[1].clientX,
                                e.touches[0].clientY - e.touches[1].clientY
                            );
                            isPanning = false;
                        } else if (e.touches.length === 1) {
                            swipeStartX = e.touches[0].clientX;
                            lastTX = e.touches[0].clientX;
                            lastTY = e.touches[0].clientY;
                            isPanning = scale > 1;
                        }
                    }, { passive: true });

                    el.addEventListener('touchmove', (e) => {
                        e.preventDefault();
                        touchMoved = true;
                        if (e.touches.length === 2) {
                            const dist = Math.hypot(
                                e.touches[0].clientX - e.touches[1].clientX,
                                e.touches[0].clientY - e.touches[1].clientY
                            );
                            scale = Math.min(Math.max(scale * (dist / pinchDist), 0.8), 5);
                            pinchDist = dist;
                            applyTransform(false);
                        } else if (e.touches.length === 1 && isPanning) {
                            panX += e.touches[0].clientX - lastTX;
                            panY += e.touches[0].clientY - lastTY;
                            lastTX = e.touches[0].clientX;
                            lastTY = e.touches[0].clientY;
                            applyTransform(false);
                        }
                    }, { passive: false }); // passive:false is REQUIRED to call preventDefault on iOS

                    el.addEventListener('touchend', (e) => {
                        isPanning = false;

                        if (scale < 1.15) {
                            // Snap back to normal
                            resetAll(true);
                            // Swipe to next/prev only when not zoomed
                            if (touchMoved && e.touches.length === 0) {
                                const dx = e.changedTouches[0].clientX - swipeStartX;
                                if (dx > 60) self.prev();
                                else if (dx < -60) self.next();
                            }
                        } else {
                            self.isZoomed = true;
                        }

                        // Single tap = toggle zoom
                        if (!touchMoved && e.touches.length === 0 && e.changedTouches.length === 1) {
                            if (scale <= 1) {
                                scale = 2.5;
                                applyTransform(true);
                                self.isZoomed = true;
                            } else {
                                resetAll(true);
                            }
                        }
                    }, { passive: true });

                    // When the lightbox closes, reset everything
                    self.$watch('isLightboxOpen', (val) => { if (!val) resetAll(false); });
                    // When image changes, reset zoom
                    self.$watch('currentImage', () => resetAll(false));
                }
            }" x-init="$nextTick(() => { /* gestures attached per-element */ })">
                
                @if(!empty($product->images) && is_array($product->images))
                    {{-- Imagen Principal Swipeable --}}
                    <div class="relative w-full aspect-square bg-[#f4f4f4] rounded-[2rem] overflow-hidden cursor-zoom-in group flex items-center justify-center touch-pan-y" 
                         x-data="{ touchStartX: 0, touchEndX: 0 }"
                         @touchstart="touchStartX = $event.changedTouches[0].screenX"
                         @touchend="touchEndX = $event.changedTouches[0].screenX; if(touchStartX - touchEndX > 50) { next(); } else if(touchEndX - touchStartX > 50) { prev(); }">
                        
                        <template x-for="(img, index) in images" :key="index">
                            <div x-show="currentImage == index" 
                                 x-transition:enter="transition ease-out duration-500"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-300"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-105"
                                 class="absolute inset-0 flex items-center justify-center"
                                 @click="isLightboxOpen = true">
                                <picture class="w-full h-full flex items-center justify-center">
                                    <img :src="'{{ asset('storage') }}/' + img"   
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-contain mix-blend-multiply transition-transform duration-700 group-hover:scale-110 pointer-events-none"
                                         :loading="index === 0 ? 'eager' : 'lazy'" 
                                         :fetchpriority="index === 0 ? 'high' : 'auto'"
                                         decoding="async">
                                </picture>
                            </div>
                        </template>

                        {{-- Navegación Interna (Desktop - Alto Contraste) --}}
                        @if(count($product->images) > 1)
                            <div class="hidden md:flex absolute inset-x-4 top-1/2 -translate-y-1/2 justify-between pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <button type="button" @click.stop="prev()" class="pointer-events-auto bg-black/20 hover:bg-black/60 backdrop-blur-md text-white rounded-full p-3 shadow-lg transition-all active:scale-90 border border-white/10">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                                </button>
                                <button type="button" @click.stop="next()" class="pointer-events-auto bg-black/20 hover:bg-black/60 backdrop-blur-md text-white rounded-full p-3 shadow-lg transition-all active:scale-90 border border-white/10">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </div>
                        @endif

                        {{-- Botón de Lupa flotante --}}
                        <div class="absolute bottom-6 right-6 bg-white/80 backdrop-blur-md rounded-2xl p-3 shadow-sm text-gray-900 pointer-events-none opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-300 transform translate-y-0 group-hover:-translate-y-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Miniaturas (Thumbnails) --}}
                    @if(count($product->images) > 1)
                        <div class="flex gap-3 overflow-x-auto scrollbar-hide py-2 px-1 snap-x" style="scrollbar-width: none;">
                            <template x-for="(img, index) in images" :key="index">
                                <button type="button" @click="currentImage = index"
                                        :class="currentImage == index ? 'ring-2 ring-gray-900 border-transparent scale-95' : 'border-gray-200 opacity-60 hover:opacity-100'"
                                        class="snap-center w-20 h-20 rounded-2xl overflow-hidden shrink-0 transition-all duration-300 bg-transparent flex items-center justify-center focus:outline-none">
                                    <img :src="'{{ asset('storage') }}/' + img" :alt="'Miniatura ' + index" class="w-full h-full object-cover rounded-xl mix-blend-multiply" loading="lazy">
                                </button>
                            </template>
                        </div>
                    @endif

                    {{-- Lightbox (Modal Pantalla Completa Premium) --}}
                    <div x-show="isLightboxOpen" 
                         x-cloak
                         style="display: none;" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         role="dialog"
                         aria-modal="true"
                         @keydown.escape.window="isLightboxOpen = false"
                         @keydown.right.window="next()"
                         @keydown.left.window="prev()"
                         class="fixed inset-0 z-[100] bg-neutral-950/98 backdrop-blur-2xl flex flex-col items-center justify-center touch-none">
                        
                        {{-- Header con contador y cierre --}}
                        <div class="absolute top-0 left-0 right-0 p-6 flex justify-between items-center z-10 pointer-events-none">
                            <div class="flex items-center justify-center bg-black/40 backdrop-blur-xl px-5 py-2.5 rounded-full border border-white/10 shadow-xl pointer-events-auto">
                                <span class="text-white text-sm font-black tracking-[0.2em] uppercase" x-text="(currentImage + 1) + ' / ' + images.length"></span>
                            </div>
                            <button @click="isLightboxOpen = false" 
                                    class="pointer-events-auto group bg-black/40 hover:bg-black/60 text-white rounded-full p-3 backdrop-blur-xl transition-all duration-300 active:scale-90 border border-white/10 shadow-xl">
                                <svg class="w-7 h-7 transform group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Área de Imagen — gestos manejados por initGestures() con DOM directo --}}
                        <div class="w-full h-full flex items-center justify-center overflow-hidden"
                             style="touch-action: none; user-select: none;"
                             x-init="initGestures($el)"
                             :class="isZoomed ? 'cursor-grab' : 'cursor-zoom-in'">
                            <img :src="'{{ asset('storage') }}/' + images[currentImage]"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="max-w-[92vw] max-h-[80vh] object-contain pointer-events-none"
                                 style="transform-origin: center center; will-change: transform;"
                                 alt="Product image">
                        </div>

                        {{-- Controles de Navegación --}}
                        @if(count($product->images) > 1)
                            {{-- Escritorio: Flechas laterales (Alto Contraste) --}}
                            <div class="hidden md:flex absolute inset-x-5 lg:inset-x-10 top-1/2 -translate-y-1/2 justify-between pointer-events-none">
                                <button type="button" @click="prev()" 
                                        class="pointer-events-auto group bg-black/40 hover:bg-black/70 text-white rounded-full p-5 backdrop-blur-xl transition-all duration-300 active:scale-90 border border-white/10 shadow-2xl">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button type="button" @click="next()" 
                                        class="pointer-events-auto group bg-black/40 hover:bg-black/70 text-white rounded-full p-5 backdrop-blur-xl transition-all duration-300 active:scale-90 border border-white/10 shadow-2xl">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Móvil & Tablet: Controles en la base (Alto Contraste) --}}
                            <div class="absolute bottom-8 md:bottom-12 flex flex-col items-center gap-6 z-10 w-full px-6">
                                {{-- Controles de navegación móvil --}}
                                <div class="flex md:hidden items-center justify-between w-full max-w-[280px] bg-black/40 backdrop-blur-xl p-2 rounded-full border border-white/10 shadow-xl">
                                    <button @click="prev()" class="p-3 text-white active:scale-90 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                                    </button>
                                    
                                    {{-- Mini Dots integrados --}}
                                    <div class="flex gap-2">
                                        <template x-for="(img, index) in images" :key="index">
                                            <div class="h-1 rounded-full transition-all duration-300" :class="currentImage == index ? 'w-4 bg-white' : 'w-1 bg-white/20'"></div>
                                        </template>
                                    </div>

                                    <button @click="next()" class="p-3 text-white active:scale-90 transition-transform">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                    </button>
                                </div>

                                {{-- Dots para escritorio (quedan limpios abajo) --}}
                                <div class="hidden md:flex items-center gap-3">
                                    <template x-for="(img, index) in images" :key="index">
                                        <button type="button" @click="currentImage = index" 
                                                class="h-1.5 rounded-full transition-all duration-300 shadow-sm"
                                                :class="currentImage == index ? 'w-10 bg-white' : 'w-2.5 bg-white/30 hover:bg-white/60'">
                                        </button>
                                    </template>
                                </div>
                            </div>
                        @endif
                    </div>

                @else
                    {{-- Fallback: 1 sola imagen --}}
                    <div class="relative w-full aspect-square bg-[#f4f4f4] rounded-[2rem] overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=F5F5F7&color=374151&bold=true&size=800"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-contain mix-blend-multiply">
                    </div>
                @endif
            </div>



            {{-- Información del producto (Derecha - Sticky on Desktop) --}}
            <div class="flex flex-col pt-0 lg:pt-8 lg:sticky lg:top-28 h-fit">
                <div class="mb-8 block">
                    <div class="flex items-center gap-2 mb-3">
                        <p class="text-[12px] font-bold uppercase tracking-[0.2em] text-gray-400 animate-boutique-in opacity-0">{{ $product->category->name ?? 'Outlet' }}</p>
                        @if($product->has_replacement_box)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-amber-50 text-amber-900 border border-amber-200/80">
                                <span>📦</span> Caja Genérica
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-4xl md:text-5xl font-black tracking-tight text-gray-900 leading-[1.1] mb-5 text-balance break-words animate-boutique-in opacity-0 anim-delay-100">
                        {{ $product->name }}
                    </h1>
                    
                    <div class="flex items-end gap-4 mb-6 animate-boutique-in opacity-0 anim-delay-200">
                        @php $isProductSoldOut = $product->variants->sum('stock') === 0; @endphp
                        @if($product->original_price)
                            <div class="flex flex-col">
                                <span class="text-4xl md:text-6xl font-black text-gray-900 tracking-tighter {{ $isProductSoldOut ? 'opacity-30' : '' }}">
                                    {{ number_format($product->price, 2) }}<span class="text-2xl md:text-3xl ml-1">€</span>
                                </span>
                            </div>
                            <div class="flex flex-col mb-1">
                                <span class="text-[14px] md:text-[16px] text-gray-300 line-through font-bold tracking-tight">{{ number_format($product->original_price, 2) }} €</span>
                                @php $pct = round((($product->original_price - $product->price) / $product->original_price) * 100); @endphp
                                <span class="text-[10px] font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md uppercase tracking-widest mt-0.5 border border-amber-100/50">−{{ $pct }}% OUTLET</span>
                            </div>
                        @else
                            <span class="text-4xl md:text-6xl font-black text-gray-900 tracking-tighter {{ $isProductSoldOut ? 'opacity-30' : '' }}">
                                {{ number_format($product->price, 2) }}<span class="text-2xl md:text-3xl ml-1">€</span>
                            </span>
                        @endif

                        @if($isProductSoldOut)
                            <span class="ml-auto bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-full shadow-lg">AGOTADO</span>
                        @endif
                    </div>

                    <p class="text-[15px] sm:text-[16px] text-gray-500 leading-relaxed font-normal mb-5 md:mb-6">
                        {{ $product->description }}
                    </p>

                    @if($product->has_replacement_box)
                        <div class="mb-6 p-4 rounded-2xl bg-amber-50/60 border border-amber-200/70 flex items-start gap-3.5 shadow-sm">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 text-sm font-bold mt-0.5">
                                📦
                            </div>
                            <div class="text-[13px] leading-relaxed text-gray-700">
                                <span class="font-bold text-gray-900 block text-[13px] mb-0.5 tracking-tight">Nota sobre el embalaje</span>
                                {{ $product->packaging_notice ?: 'Producto 100% original y a estrenar. Se entrega con caja neutra de sustitución (sin la caja original de la marca).' }}
                            </div>
                        </div>
                    @endif
                </div>
                
                {{-- Formulario para añadir al carrito --}}
                <form action="{{ route('cart.add') }}" method="POST" class="mt-auto" 
                      x-data="{ variantSelected: false, showError: false, showSticky: false }" 
                      @scroll.window="showSticky = $refs.mainBtn ? $refs.mainBtn.getBoundingClientRect().bottom < 0 : false"
                      @submit.prevent="if(!variantSelected) { showError = true; setTimeout(() => showError = false, 3000); } else { $el.submit(); }">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    {{-- Selector de Color --}}
                    @if(isset($colorProducts) && $colorProducts->count() > 0)
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-[13px] font-bold uppercase tracking-wider text-gray-900">Color</h3>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                {{-- Color actual --}}
                                @php
                                    if (!function_exists('getProductColorStyle')) {
                                        function getProductColorStyle($prod) {
                                            // 1. Prioridad: color_hex (del producto)
                                            if (!empty($prod->color_hex)) {
                                                return "background-color: {$prod->color_hex};";
                                            }

                                            // 2. Fallback: Detección por nombre
                                            $search = strtolower($prod->name);
                                            if (str_contains($search, 'negr') || str_contains($search, 'black')) return 'background-color: #111827;'; // gray-900
                                            if (str_contains($search, 'blan') || str_contains($search, 'white')) return 'background-color: #ffffff;';
                                            if (str_contains($search, 'verd') || str_contains($search, 'green')) return 'background-color: #16a34a;'; // green-600
                                            if (str_contains($search, 'roj') || str_contains($search, 'red')) return 'background-color: #dc2626;'; // red-600
                                            if (str_contains($search, 'azul') || str_contains($search, 'blue')) return 'background-color: #2563eb;'; // blue-600
                                            if (str_contains($search, 'amarill') || str_contains($search, 'yellow')) return 'background-color: #facc15;'; // yellow-400
                                            if (str_contains($search, 'naranj') || str_contains($search, 'orange')) return 'background-color: #f97316;'; // orange-500
                                            if (str_contains($search, 'ros') || str_contains($search, 'pink')) return 'background-color: #f472b6;'; // pink-400
                                            if (str_contains($search, 'morad') || str_contains($search, 'purp')) return 'background-color: #9333ea;'; // purple-600
                                            if (str_contains($search, 'gris') || str_contains($search, 'gray')) return 'background-color: #6b7280;'; // gray-500
                                            if (str_contains($search, 'marron') || str_contains($search, 'brown')) return 'background-color: #92400e;'; // amber-800
                                            
                                            return 'background-color: #e5e7eb;'; // gray-200
                                        }
                                    }
                                    
                                    $currStyle = getProductColorStyle($product);
                                @endphp
                                <div class="w-10 h-10 rounded-full border-2 border-gray-900 flex items-center justify-center p-0.5" title="{{ $product->color_name ?? $product->name }}">
                                    <div class="w-full h-full rounded-full border border-gray-200" style="{{ $currStyle }}"></div>
                                </div>
                                
                                {{-- Otros colores --}}
                                @foreach($colorProducts as $colorProduct)
                                    <a href="{{ route('product.show', $colorProduct->slug) }}" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center p-0.5 hover:border-gray-400 transition-colors" title="{{ $colorProduct->color_name ?? $colorProduct->name }}">
                                        @php
                                            $otherStyle = getProductColorStyle($colorProduct);
                                        @endphp
                                        <div class="w-full h-full rounded-full border border-gray-200" style="{{ $otherStyle }}"></div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Selector de Tallas --}}
                    <div class="mb-6 md:mb-10">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-[13px] font-bold uppercase tracking-wider text-gray-900">Seleccionar Talla</h3>
                            <button type="button" @click="openSizeGuide = true" class="text-[12px] font-medium text-gray-400 hover:text-gray-900 underline underline-offset-4 transition-colors">Guía de tallas</button>
                        </div>
                        
                        {{-- Consejo de Ajuste / Tallaje --}}
                        @if($product->fit_type || $product->fit_advice)
                            <div class="mb-4 p-3 rounded-2xl bg-neutral-50 border border-neutral-200/80 flex items-center gap-2.5 text-[12px] text-gray-700">
                                <span class="w-6 h-6 rounded-lg bg-neutral-900 text-white flex items-center justify-center text-[11px] shrink-0 font-bold">📏</span>
                                <div>
                                    <span class="font-bold text-gray-900">Ajuste:</span>
                                    <span class="text-gray-600">{{ $product->getFitAdviceText() }}</span>
                                </div>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                            @if($product->variants)
                                @foreach($product->getSortedVariants() as $variant)
                                    <label class="cursor-pointer relative group">
                                        <input type="radio" name="variant_id" value="{{ $variant->id }}" class="peer sr-only" @change="variantSelected = true" @disabled($variant->stock <= 0)>
                                        
                                        <div class="flex items-center justify-center py-3 sm:py-3.5 border-[1.5px] border-gray-200 rounded-2xl bg-white text-[15px] font-medium text-gray-900 tracking-wide transition-all duration-200 ease-out
                                                    peer-checked:border-gray-900 peer-checked:bg-gray-900 peer-checked:text-white peer-checked:shadow-sm
                                                    peer-disabled:opacity-30 peer-disabled:cursor-not-allowed peer-disabled:bg-gray-50
                                                    group-hover:border-gray-300 peer-disabled:group-hover:border-gray-200">
                                            {{ $variant->size }}
                                            
                                            @if($variant->stock <= 0)
                                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                    <div class="w-full h-px bg-gray-300 rotate-12 absolute scale-110"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            @endif
                        </div>
                        @error('variant_id')
                            <p class="mt-2 text-[13px] text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botón CTA (Principal) --}}
                    <div class="block" x-ref="mainBtn">
                        <div x-show="showError" x-transition style="display: none;" class="mb-4 text-red-500 text-[13px] font-medium flex items-center gap-2 bg-red-50 p-3.5 rounded-xl border border-red-100">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Por favor, selecciona una talla antes de continuar.
                        </div>
                        <button type="submit" 
                                @disabled($isProductSoldOut)
                                class="w-full {{ $isProductSoldOut ? 'bg-gray-400 cursor-not-allowed' : 'bg-gray-900 hover:bg-gray-800 shadow-[0_8px_30px_rgb(0,0,0,0.12)] hover:shadow-[0_8px_40px_rgb(0,0,0,0.16)]' }} text-white font-medium text-[16px] py-4 rounded-2xl transition-all duration-300 active:scale-[0.98] flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span>{{ $isProductSoldOut ? 'PRODUCTO AGOTADO' : 'Añadir al Carrito' }}</span>
                        </button>
                    </div>

                    {{-- Menú CTA Flotante (Mobile) --}}
                    <div class="md:hidden fixed bottom-4 left-4 right-4 z-50 flex flex-col gap-2"
                         x-show="showSticky"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="translate-y-full opacity-0"
                         x-transition:enter-end="translate-y-0 opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="translate-y-0 opacity-100"
                         x-transition:leave-end="translate-y-full opacity-0"
                         x-cloak>
                        {{-- Error (Mobile) --}}
                        <div x-show="showError" x-transition style="display: none;" class="bg-red-50/95 backdrop-blur-md text-red-600 text-[13px] font-medium p-3.5 rounded-2xl border border-red-100 shadow-lg text-center flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Selecciona una talla primero.
                        </div>
                        
                        <div class="bg-white/95 backdrop-blur-3xl border border-gray-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-[2rem] p-2.5 flex items-center justify-between gap-2 touch-none">

                            <div class="flex-1 min-w-0 flex flex-col items-center justify-center -translate-y-0.5">
                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-0.5">Total</p>
                                <p class="text-[18px] font-bold text-gray-900 leading-none tracking-tight">{{ number_format($product->price, 2) }} €</p>
                            </div>

                            <button type="submit"
                                    @disabled($isProductSoldOut)
                                    class="{{ $isProductSoldOut ? 'bg-gray-400 cursor-not-allowed' : 'bg-gray-900 active:bg-gray-800' }} text-white font-medium text-[15px] tracking-wide h-[52px] px-6 rounded-2xl transition-transform duration-300 shadow-[0_4px_14px_0_rgb(0,0,0,0.15)] active:scale-95 flex-shrink-0 flex items-center justify-center gap-2">
                                <span>{{ $isProductSoldOut ? 'AGOTADO' : 'Añadir' }}</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap md:flex-nowrap mt-5 items-center justify-center gap-3 md:gap-6">
                        <div class="flex items-center gap-1.5 md:gap-2 text-[11px] md:text-[12px] font-medium text-gray-500">
                            <svg class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            Envío 3-5 días
                        </div>
                        <div class="flex items-center gap-1.5 md:gap-2 text-[11px] md:text-[12px] font-medium text-gray-500">
                            <svg class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Devoluciones en 14 días
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Botón Flotante de WhatsApp (Mobile) --}}
        @php
            $waMessage = $isProductSoldOut 
                ? "¡Hola! He visto que el producto {$product->name} está agotado. ¿Habrá reposición pronto?"
                : ($settings['whatsapp_default_message'] ?? '¡Hola! Tengo una duda sobre este producto.').' ('.$product->name.')';
        @endphp
        <a href="https://wa.me/{{ $settings['whatsapp_number'] ?? '34600000000' }}?text={{ urlencode($waMessage) }}"
           target="_blank" rel="noopener"
           class="{{ $isProductSoldOut ? 'animate-bounce' : '' }} md:hidden fixed bottom-24 right-4 z-40 w-14 h-14 flex items-center justify-center bg-white shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 rounded-full text-[#25D366] transition-transform active:scale-95">
            <svg class="w-7 h-7 fill-current drop-shadow-sm" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>

        {{-- Modal Guía de Tallas (Dynamic) --}}
        <div x-show="openSizeGuide" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 touch-none">
            <div x-show="openSizeGuide" x-transition.opacity class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="openSizeGuide = false"></div>
            
            <div x-show="openSizeGuide" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                 class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-sm sm:max-w-md overflow-hidden flex flex-col max-h-full">
                 
                {{-- Header --}}
                <div class="flex items-center justify-between p-6 border-b border-gray-50 bg-[#F5F5F7]">
                    <h3 class="text-[17px] font-bold text-gray-900 tracking-tight">Guía de Tallas</h3>
                    <button type="button" @click="openSizeGuide = false" class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-full transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 overflow-y-auto">
                    @php
                        $catName = strtolower($product->category->name ?? '');
                    @endphp

                    @if(str_contains($catName, 'zapatilla') || str_contains($catName, 'sneaker') || str_contains($catName, 'calzado'))
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m15 10.42 4.8-5.07"/><path d="M19 18h3"/><path d="M9.5 22 21.414 9.415A2 2 0 0 0 21.2 6.4l-5.61-4.208A1 1 0 0 0 14 3v2a2 2 0 0 1-1.394 1.906L8.677 8.053A1 1 0 0 0 8 9c-.155 6.393-2.082 9-4 9a2 2 0 0 0 0 4h14"/>
                                </svg>
                            </div>
                            <p class="text-[13px] font-bold text-gray-900 uppercase tracking-wider">Calzado</p>
                        </div>
                        <p class="text-[13px] text-gray-500 mb-6 leading-relaxed">Guía de tallas para calzado. Medidas aproximadas del largo del pie.</p>
                        <div class="bg-white border border-gray-100 rounded-2xl overflow-x-auto shadow-sm scrollbar-hide">
                            <table class="w-full text-left text-[14px] min-w-[280px]">
                                <thead>
                                    <tr class="border-b border-gray-100 bg-gray-50/50">
                                        <th class="py-3 px-4 font-bold text-gray-900 text-[11px] uppercase tracking-wider">Talla (EU)</th>
                                        <th class="py-3 px-4 font-bold text-gray-900 text-[11px] uppercase tracking-wider text-right">Largo (CM)</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-900 font-medium">
                                    @foreach(['36' => '22.1', '36 2/3' => '22.5', '37 1/3' => '22.9', '38' => '23.3', '38 2/3' => '23.8', '39 1/3' => '24.2', '40' => '24.6', '40 2/3' => '25.0', '41 1/3' => '25.5', '42' => '25.9', '42 2/3' => '26.3', '43 1/3' => '26.7', '44' => '27.1', '44 2/3' => '27.6', '45 1/3' => '28.0', '46' => '28.4', '46 2/3' => '28.8', '47 1/3' => '29.3', '48' => '29.7'] as $eu => $cm)
                                        <tr class="border-b border-gray-50 last:border-0"><td class="py-3 px-4 font-bold">{{ $eu }}</td><td class="py-3 px-4 text-right text-gray-500">{{ $cm }} cm</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(str_contains($catName, 'pantalon') || str_contains($catName, 'pantalón') || str_contains($catName, 'jeans') || str_contains($catName, 'short') || str_contains($catName, 'bermuda') || str_contains($catName, 'chándal'))
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 6h16"/><path d="M6 22a2 2 0 0 1-2-2V3c0-.6.4-1 1-1h14c.6 0 1 .4 1 1v17a2 2 0 0 1-2 2h-3l-3-10-3 10Z"/><path d="m6 11-2 1"/><path d="M9 8.5V6"/><path d="M15 6v2.5"/><path d="m20 12-2-1"/><path d="M4 18h6"/><path d="M14 18h6"/>
                                </svg>
                            </div>
                            <p class="text-[13px] font-bold text-gray-900 uppercase tracking-wider">Parte Inferior</p>
                        </div>
                        <p class="text-[13px] text-gray-500 mb-6 leading-relaxed">Guía de tallas para partes inferiores. Medidas del cuerpo recomendadas.</p>
                        <div class="bg-white border border-gray-100 rounded-2xl overflow-x-auto shadow-sm scrollbar-hide">
                            <table class="w-full text-left text-[14px] min-w-[320px]">
                                <thead>
                                    <tr class="border-b border-gray-100 bg-gray-50/50">
                                        <th class="py-3 px-4 font-bold text-gray-900 text-[11px] uppercase tracking-wider">Filtro</th>
                                        <th class="py-3 px-4 font-bold text-gray-900 text-[11px] uppercase tracking-wider">EU (Num)</th>
                                        <th class="py-3 px-4 font-bold text-gray-900 text-[11px] uppercase tracking-wider">Cintura</th>
                                        <th class="py-3 px-4 font-bold text-gray-900 text-[11px] uppercase tracking-wider text-right">Cadera</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-900 font-medium">
                                    @foreach([['alpha' => 'XXS', 'numeric' => '32', 'waist' => '57-60', 'hip' => '82-85'], ['alpha' => 'XS', 'numeric' => '34', 'waist' => '61-66', 'hip' => '86-91'], ['alpha' => 'S', 'numeric' => '36', 'waist' => '67-72', 'hip' => '92-97'], ['alpha' => 'M', 'numeric' => '38', 'waist' => '73-78', 'hip' => '98-103'], ['alpha' => 'L', 'numeric' => '40', 'waist' => '79-85', 'hip' => '104-110'], ['alpha' => 'XL', 'numeric' => '42', 'waist' => '86-94', 'hip' => '111-117'], ['alpha' => '2XL', 'numeric' => '44', 'waist' => '95-104', 'hip' => '118-125'], ['alpha' => '3XL', 'numeric' => '46', 'waist' => '105-114', 'hip' => '126-135']] as $s)
                                        <tr class="border-b border-gray-50 last:border-0"><td class="py-3 px-4 font-bold">{{ $s['alpha'] }}</td><td class="py-3 px-4 text-gray-400 font-bold">{{ $s['numeric'] }}</td><td class="py-3 px-4 text-gray-500">{{ $s['waist'] }}</td><td class="py-3 px-4 text-right text-gray-500">{{ $s['hip'] }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(str_contains($catName, 'accesorio'))
                        <div class="bg-gray-900 rounded-2xl p-6 border border-gray-100 shadow-lg">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-10 h-10 bg-gray-800 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="7"/><polyline points="12 9 12 12 13.5 13.5"/><path d="M16.51 17.35l-.35 3.83a2 2 0 0 1-2 1.82H9.84a2 2 0 0 1-2-1.82l-.35-3.83m.01-10.7l.35-3.83A2 2 0 0 1 9.84 1H14.16a2 2 0 0 1 2 1.82l.35 3.83"/>
                                    </svg>
                                </div>
                                <h3 class="text-white font-bold">Talla Única (One Size)</h3>
                            </div>
                            <p class="text-[13px] text-gray-400 leading-relaxed">
                                Este accesorio está diseñado para adaptarse a la mayoría de usuarios.
                                @if(str_contains(strtolower($product->name), 'gorra')) Las gorras incluyen cierre ajustable trasero. @endif
                                @if(str_contains(strtolower($product->name), 'calcetin')) Los calcetines están fabricados con materiales elásticos de alta calidad. @endif
                            </p>
                        </div>
                    @else
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/>
                                </svg>
                            </div>
                            <p class="text-[13px] font-bold text-gray-900 uppercase tracking-wider">Ropa Superior</p>
                        </div>
                        <p class="text-[13px] text-gray-500 mb-6 leading-relaxed">Guía de tallas para partes superiores. Medidas del cuerpo recomendadas.</p>
                        <div class="bg-white border border-gray-100 rounded-2xl overflow-x-auto shadow-sm scrollbar-hide">
                            <table class="w-full text-left text-[14px] min-w-[320px]">
                                <thead>
                                    <tr class="border-b border-gray-100 bg-gray-50/50">
                                        <th class="py-3 px-4 font-bold text-gray-900 text-[11px] uppercase tracking-wider">Filtro</th>
                                        <th class="py-3 px-4 font-bold text-gray-900 text-[11px] uppercase tracking-wider">EU (Num)</th>
                                        <th class="py-3 px-4 font-bold text-gray-900 text-[11px] uppercase tracking-wider">Pecho</th>
                                        <th class="py-3 px-4 font-bold text-gray-900 text-[11px] uppercase tracking-wider text-right">Cintura</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-900 font-medium">
                                    @foreach([['alpha' => 'XXS', 'numeric' => '32', 'chest' => '73-76', 'waist' => '57-60'], ['alpha' => 'XS', 'numeric' => '34', 'chest' => '77-82', 'waist' => '61-66'], ['alpha' => 'S', 'numeric' => '36', 'chest' => '83-88', 'waist' => '67-72'], ['alpha' => 'M', 'numeric' => '38', 'chest' => '89-94', 'waist' => '73-78'], ['alpha' => 'L', 'numeric' => '40', 'chest' => '95-101', 'waist' => '79-85'], ['alpha' => 'XL', 'numeric' => '42', 'chest' => '102-109', 'waist' => '86-94'], ['alpha' => '2XL', 'numeric' => '44', 'chest' => '110-118', 'waist' => '95-104'], ['alpha' => '3XL', 'numeric' => '46', 'chest' => '119-127', 'waist' => '105-114']] as $s)
                                        <tr class="border-b border-gray-50 last:border-0"><td class="py-3 px-4 font-bold">{{ $s['alpha'] }}</td><td class="py-3 px-4 text-gray-400 font-bold">{{ $s['numeric'] }}</td><td class="py-3 px-4 text-gray-500">{{ $s['chest'] }}</td><td class="py-3 px-4 text-right text-gray-500">{{ $s['waist'] }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- ===== SECCIÓN VENTA CRUZADA (RELACIONADOS) ===== --}}
        @if($relatedProducts && $relatedProducts->count() > 0)
            <div class="mt-12 md:mt-24 pt-12 border-t border-gray-100">
                <h2 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900 mb-8 md:mb-12">Completa tu look.</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
                    @include('frontend.partials.product-cards', ['products' => $relatedProducts])
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

