@php
    $genderTitle = match(request('genero')) {
        'hombre' => 'Hombre',
        'mujer' => 'Mujer',
        'ninos' => 'Niños',
        default => null
    };
    $categoryTitle = request('category') && request('category') !== 'Todo' ? request('category') : null;
    
    $titleParts = [];
    if ($categoryTitle) $titleParts[] = $categoryTitle;
    if ($genderTitle) $titleParts[] = $genderTitle;
    
    $pageTitle = !empty($titleParts) ? implode(' - ', $titleParts) : 'Selección Premium';
@endphp

<x-app-layout :title="$pageTitle">

    {{-- ===== HERO SECTION ===== --}}
    @if(!request('genero') && !request('search') && (!request('category') || request('category') === 'Todo'))
        <section class="hero-gradient px-5 sm:px-8 lg:px-10 pt-4 pb-2 md:pt-10 md:pb-12">
        <div class="max-w-7xl mx-auto text-center">
            <p
                class="inline-flex items-center gap-2 text-[10px] md:text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-400 mb-2 md:mb-5 bg-gray-100/80 px-4 py-2 rounded-full border border-gray-100">
                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse inline-block"></span>
                @if(!empty($settings['home_hero_badge']))
                    {{ $settings['home_hero_badge'] }}
                @elseif(!empty($settings['hero_badge']))
                    {{ $settings['hero_badge'] }}
                @else
                    Stock especial · Actualizado esta semana
                @endif
            </p>
            <h1 class="text-2xl sm:text-5xl md:text-6xl font-black tracking-tight text-gray-900 leading-[1.05] max-w-3xl mx-auto">
                @if(!empty($settings['home_hero_title']))
                    {{ $settings['home_hero_title'] }}
                @elseif(!empty($settings['hero_title']))
                    {{ $settings['hero_title'] }}
                @else
                    Selección Premium.
                @endif
            </h1>
            <p
                class="mt-2 md:mt-5 text-[13px] md:text-[15px] text-gray-400 leading-relaxed max-w-md mx-auto font-medium opacity-80">
                @php
                    $subtitle = !empty($settings['home_hero_subtitle']) 
                        ? $settings['home_hero_subtitle'] 
                        : ($settings['hero_description'] ?? 'Ropa urbana y zapatillas deportivas con descuentos irrepetibles. Stock muy limitado.');
                @endphp
                {!! nl2br(e($subtitle)) !!}
            </p>

            {{-- Stats row (Oculto temporalmente) --}}
            {{-- 
            <div class="hidden md:flex mt-10 items-center justify-center gap-8 md:gap-12">
                <div class="text-center">
                    <p class="text-2xl font-black text-gray-900">{{ $settings['discount_label'] ?? '-70%' }}</p>
                    <p class="text-[11px] text-gray-400 uppercase tracking-wider mt-0.5">Descuentos</p>
                </div>
                <div class="w-px h-8 bg-gray-200"></div>
                <div class="text-center">
                    <p class="text-2xl font-black text-gray-900">
                        {{ \App\Models\Product::where('is_active', true)->where('brand', 'Adidas')->count() }}</p>
                    <p class="text-[11px] text-gray-400 uppercase tracking-wider mt-0.5">Artículos</p>
                </div>
                <div class="w-px h-8 bg-gray-200"></div>
                <div class="text-center">
                    <p class="text-2xl font-black text-gray-900">{{ $settings['shipping_label'] ?? '4€' }}</p>
                    <p class="text-[11px] text-gray-400 uppercase tracking-wider mt-0.5">Envío</p>
                </div>
            </div>
            --}}
        </div>
    </section>
    @endif

    {{-- ===== BUSCADOR Y FILTROS ===== --}}
    @if(request('genero') || request('search') || (request('category') && request('category') !== 'Todo'))
    <section class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 pt-0 pb-1 md:pt-2 md:pb-6">
        <form action="{{ route('home') }}" method="GET"
            @submit.prevent="const params = new URLSearchParams(new FormData($el)); $store.appNav.load($el.action + '?' + params.toString())"
            class="flex flex-col md:flex-row items-center gap-4 lg:gap-8 w-full lg:max-w-5xl mx-auto">
            <input type="hidden" name="search" value="{{ request('search') }}" id="search-hidden-input">
            <input type="hidden" name="genero" value="{{ request('genero') }}">

            {{-- Filtro tipo Píldoras --}}
            <div x-data x-init="$nextTick(() => { const active = $el.querySelector('.bg-gray-900.text-white'); if(active) { const scrollLeft = active.offsetLeft - ($el.clientWidth / 2) + (active.clientWidth / 2); $el.scrollTo({ left: Math.max(0, scrollLeft), behavior: 'smooth' }); } })" 
                 class="flex items-center gap-2 overflow-x-auto py-3 md:py-2 scrollbar-hide flex-1 min-w-0 w-full justify-start overflow-y-hidden px-0.5">
                <button type="button" 
                    @click="$store.appNav.load('{{ route('home') }}?genero={{ request('genero') }}&search={{ request('search') }}&category=Todo')"
                    class="whitespace-nowrap px-4 py-3 md:py-2 text-[13px] font-bold rounded-full transition-all border {{ request('category', 'Todo') === 'Todo' ? 'bg-gray-900 text-white border-transparent shadow-lg transform scale-105' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-900 hover:text-gray-900 shadow-sm' }}">
                    Todo
                </button>
                @foreach($categories as $cat)
                    <button type="button"
                        @click="$store.appNav.load('{{ route('home') }}?genero={{ request('genero') }}&search={{ request('search') }}&category={{ $cat->name }}')"
                        class="whitespace-nowrap px-4 py-3 md:py-2 text-[13px] font-bold rounded-full transition-all border {{ request('category') === $cat->name ? 'bg-gray-900 text-white border-transparent shadow-lg transform scale-105' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-900 hover:text-gray-900 shadow-sm' }}">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </form>
    </section>
    @endif

    {{-- ===== NUESTRA SELECCIÓN (AUTO-CARRUSEL UNIVERSAL) ===== --}}
    @if($featuredProducts->isNotEmpty() && !request('genero') && !request('search') && (!request('category') || request('category') === 'Todo'))
        <section class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-10 pt-2 pb-2 md:pb-10">
            <div class="flex flex-col gap-0.5 mb-4 md:mb-8">
                <p class="text-[10px] md:text-[11px] font-bold uppercase tracking-[0.2em] text-amber-500">
                    {{ \App\Models\SiteSetting::getValue('home_featured_subtitle', 'Exclusividad Garantizada') }}
                </p>
                <h2 class="text-[18px] md:text-3xl font-black tracking-tight text-gray-900 leading-tight">
                    {{ \App\Models\SiteSetting::getValue('home_featured_title', 'Nuestra Selección') }}
                </h2>
            </div>

        {{-- Slider con Alpine.js (compatible móvil/iOS) --}}
            <div x-data="{ 
                    currentIndex: 0,
                    paused: false,
                    visible: true,
                    pauseTimeout: null,
                    isMobile: false,
                    init() {
                        this.isMobile = window.matchMedia('(hover: none)').matches;

                        // Solo auto-avanzar cuando el carrusel sea visible en pantalla
                        const observer = new IntersectionObserver((entries) => {
                            this.visible = entries[0].isIntersecting;
                        }, { threshold: 0.2 });
                        observer.observe(this.$el);

                        setInterval(() => {
                            if (this.paused || !this.visible) return;
                            this.goNext();
                        }, 5000);
                    },
                    goTo(index) {
                        const slider = this.$refs.slider;
                        if (!slider) return;
                        const items = slider.children;
                        if (!items.length) return;
                        const clampedIndex = Math.max(0, Math.min(index, items.length - 1));
                        this.currentIndex = clampedIndex;
                        // Posición exacta por índice: no depende de animaciones en curso
                        const cardWidth = items[0].getBoundingClientRect().width;
                        const gap = parseFloat(window.getComputedStyle(slider).columnGap) || 12;
                        slider.scrollTo({ left: clampedIndex * (cardWidth + gap), behavior: 'smooth' });
                    },
                    goNext() {
                        const total = this.$refs.slider ? this.$refs.slider.children.length : 0;
                        const nextIndex = this.currentIndex >= total - 1 ? 0 : this.currentIndex + 1;
                        this.goTo(nextIndex);
                    },
                    goPrev() {
                        const total = this.$refs.slider ? this.$refs.slider.children.length : 0;
                        const prevIndex = this.currentIndex <= 0 ? total - 1 : this.currentIndex - 1;
                        this.goTo(prevIndex);
                    },
                    pauseTemp() {
                        this.paused = true;
                        clearTimeout(this.pauseTimeout);
                        this.pauseTimeout = setTimeout(() => {
                            this.paused = false;
                        }, 8000);
                    }
                }"
                x-init="init()"
                @mouseenter="!isMobile && (paused = true)"
                @mouseleave="!isMobile && (paused = false)"
                @touchstart.passive="pauseTemp()"
                class="relative group">
                
                {{-- Botones Navegación (Solo Desktop) --}}
                <button @click="goPrev()" 
                    class="hidden md:flex absolute left-[-20px] top-1/2 -translate-y-1/2 z-20 w-12 h-12 items-center justify-center bg-white/95 rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:scale-110 active:scale-95 text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                
                <button @click="goNext()" 
                    class="hidden md:flex absolute right-[-20px] top-1/2 -translate-y-1/2 z-20 w-12 h-12 items-center justify-center bg-white/95 rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:scale-110 active:scale-95 text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>

                {{-- Contenedor del Slider --}}
                <div x-ref="slider" class="flex overflow-x-auto pb-4 md:pb-8 scrollbar-hide gap-3 sm:gap-4 md:gap-5 px-1" style="-webkit-overflow-scrolling: touch; scroll-snap-type: x mandatory; scroll-behavior: auto;">
                    @foreach($featuredProducts as $product)
                        {{-- Tarjeta Destacada --}}
                        <div class="w-[240px] sm:w-[260px] md:w-[270px] lg:w-[280px] shrink-0 snap-start flex flex-col">
                            <a href="{{ route('product.show', $product->slug) }}"
                                class="group/card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-[0_12px_36px_-10px_rgba(0,0,0,0.12)] transition-all duration-300 hover:-translate-y-1.5 flex flex-col border border-gray-100 h-full relative">
                                
                                {{-- Badge Premium --}}
                                <div class="absolute top-3 left-3 z-10">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-white/90 text-gray-900 text-[9px] md:text-[10px] font-bold uppercase tracking-[0.15em] border border-gray-200/60 shadow-sm backdrop-blur-md">
                                        <svg class="w-2.5 h-2.5 md:w-3 md:h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        {{ \App\Models\SiteSetting::getValue('product_badge_text', 'DESTACADO') }}
                                    </span>
                                </div>

                                {{-- Imagen --}}
                                <div class="relative w-full aspect-square bg-[#f4f4f4] overflow-hidden shrink-0">
                                    @php
                                        $firstImage = !empty($product->images) ? (is_array($product->images) ? $product->images[0] : $product->images) : null;
                                    @endphp

                                    @if($firstImage)
                                        <img src="{{ asset('storage/' . $firstImage) }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover/card:scale-105"
                                            loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=f4f4f4&color=9ca3af&bold=true&size=400"
                                                alt="{{ $product->name }}"
                                                class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover/card:scale-105"
                                                loading="lazy">
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="p-3.5 md:p-4 flex flex-col flex-1">
                                    <p class="text-[9px] md:text-[10px] font-semibold text-gray-400 uppercase tracking-[0.12em] mb-0.5">{{ $product->category->name ?? 'Exclusivo' }}</p>
                                    <h3 class="text-[13px] md:text-[14px] font-bold text-gray-900 leading-snug line-clamp-2 h-[2.4rem] md:h-[2.6rem] mb-2 group-hover/card:text-amber-500 transition-colors duration-300">
                                        {{ $product->name }}
                                    </h3>

                                    <div class="mt-auto flex items-baseline gap-2 pt-1 border-t border-gray-50">
                                        <span class="text-[15px] md:text-[16px] font-black text-gray-900 leading-none">{{ number_format($product->price, 2) }} €</span>
                                        @if($product->original_price)
                                            <span class="text-[11px] md:text-[12px] text-gray-400 line-through font-medium">{{ number_format($product->original_price, 2) }} €</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Dots indicadores (Solo móvil) --}}
                @if($featuredProducts->count() > 1)
                <div class="flex md:hidden justify-center gap-1.5 mt-3">
                    @foreach($featuredProducts as $i => $p)
                        <button @click="goTo({{ $i }}); pauseTemp()"
                            class="w-1.5 h-1.5 rounded-full transition-all duration-300"
                            :class="currentIndex === {{ $i }} ? 'bg-gray-900 w-4' : 'bg-gray-300'">
                        </button>
                    @endforeach
                </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ===== CATÁLOGO ===== --}}
    <section id="catalogo" class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-10 pb-20">

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

        {{-- Header catálogo --}}
        @if(!request('genero') && !request('search') && (!request('category') || request('category') === 'Todo'))
            <div class="flex flex-col gap-0.5 mb-4 md:mb-8">
                <p class="text-[10px] md:text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">Todo el stock</p>
                <h2 class="text-[18px] md:text-3xl font-black tracking-tight text-gray-900 leading-tight">Catálogo</h2>
            </div>
        @endif

        {{-- Grid productos con Load More --}}
        @if($products->isEmpty())
            <div class="text-center py-24 border border-dashed border-gray-200 rounded-3xl">
                <p class="text-gray-400 text-sm font-medium">No hay productos disponibles ahora mismo.</p>
            </div>
        @else
            <div
                x-data="{
                    loading: false,
                    hasMore: {{ $products->hasMorePages() ? 'true' : 'false' }},
                    nextPage: {{ $products->currentPage() + 1 }},
                    baseUrl: '{{ request()->url() }}',
                    params: {{ json_encode(request()->except('page')) }},

                    async loadMore() {
                        if (this.loading || !this.hasMore) return;
                        this.loading = true;

                        try {
                            const qs = new URLSearchParams({ ...this.params, page: this.nextPage }).toString();
                            const res = await fetch(`${this.baseUrl}?${qs}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            });

                            if (!res.ok) throw new Error('Error al cargar');

                            const data = await res.json();

                            const grid = this.$refs.productGrid;
                            const tmp = document.createElement('div');
                            tmp.innerHTML = data.html;
                            while (tmp.firstElementChild) {
                                grid.appendChild(tmp.firstElementChild);
                            }

                            this.hasMore = data.hasMore;
                            this.nextPage = data.nextPage;
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.loading = false;
                        }
                    }
                }"
            >
                {{-- Grid --}}
                <div x-ref="productGrid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4">
                    @include('frontend.partials.product-cards', ['products' => $products])
                </div>

                {{-- Botón Cargar más --}}
                <div class="mt-10 md:mt-14 flex flex-col items-center gap-3" x-show="hasMore || loading">
                    <p class="text-[12px] text-gray-400 font-medium" x-show="hasMore && !loading">
                        Mostrando
                        <span class="font-bold text-gray-700">{{ $products->count() }}</span>
                        de
                        <span class="font-bold text-gray-700">{{ $products->total() }}</span>
                        productos
                    </p>

                    <button
                        @click="loadMore()"
                        :disabled="loading"
                        x-show="hasMore"
                        class="relative inline-flex items-center gap-3 px-8 py-3.5 bg-gray-900 text-white text-[13px] font-bold rounded-full shadow-lg hover:bg-gray-800 active:scale-95 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <svg x-show="loading" class="animate-spin w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        <span x-text="loading ? 'Cargando...' : 'Cargar más productos'"></span>
                        <svg x-show="!loading" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                {{-- Todos los productos vistos --}}
                <div class="mt-10 flex flex-col items-center gap-1.5" x-show="!hasMore && !loading" x-cloak>
                    <div class="w-8 h-px bg-gray-200"></div>
                    <p class="text-[12px] text-gray-400 font-medium">Has visto todos los productos ({{ $products->total() }})</p>
                    <div class="w-8 h-px bg-gray-200"></div>
                </div>
            </div>
        @endif
    </section>

</x-app-layout>