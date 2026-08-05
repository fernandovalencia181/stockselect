@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? (!empty($settings['site_title']) ? $settings['site_title'] . ' | Selección Premium' : 'Stock Select | Selección Premium') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/stock_select_logo-transparente.png') }}">
    <meta name="description"
        content="{{ $description ?? ($settings['site_description'] ?? 'Outlet de ropa deportiva y moda urbana con descuentos de hasta el 70%. Stock limitado en zapatillas, sudaderas y más.') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? (!empty($settings['site_title']) ? $settings['site_title'] : 'Stock Select') }}">
    <meta property="og:description" content="{{ $description ?? ($settings['site_description'] ?? 'Outlet de ropa deportiva y moda urbana con descuentos de hasta el 70%.') }}">
    <meta property="og:image" content="{{ !empty($settings['seo_og_image']) ? asset('storage/'.$settings['seo_og_image']) : asset('images/stock_select_logo.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $title ?? (!empty($settings['site_title']) ? $settings['site_title'] : 'Stock Select') }}">
    <meta property="twitter:description" content="{{ $description ?? ($settings['site_description'] ?? 'Outlet de ropa deportiva y moda urbana con descuentos de hasta el 70%.') }}">
    <meta property="twitter:image" content="{{ !empty($settings['seo_og_image']) ? asset('storage/'.$settings['seo_og_image']) : asset('images/stock_select_logo.png') }}">

    @stack('meta')

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;0,14..32,900&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                    backdropBlur: { xs: '2px' },
                }
            }
        }
    </script>
    <style>
        *,
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .glass {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
        }

        .glass-card {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        [x-cloak] {
            display: none !important;
        }

        .nav-link {
            @apply text-[13px] font-medium text-gray-600 hover:text-gray-900 transition-colors duration-200 tracking-wide;
        }

        .hero-gradient {
            background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(200, 210, 230, 0.18) 0%, rgba(255, 255, 255, 0) 70%),
                radial-gradient(ellipse 60% 40% at 80% 80%, rgba(210, 220, 240, 0.12) 0%, rgba(255, 255, 255, 0) 60%),
                #ffffff;
        }

        /* Ocultar barra de scroll pero mantener funcionalidad */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
        }
    </style>
    
    {{-- Google Analytics / Pixel --}}
    @if(!empty($settings['analytics_id']))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings['analytics_id'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $settings['analytics_id'] }}');
        </script>
    @endif
</head>

<body class="bg-white min-h-screen flex flex-col text-gray-900" x-data>

    @if(($settings['maintenance_mode'] ?? '0') === '1')
        <div class="fixed inset-0 z-[100] bg-white flex flex-col items-center justify-center text-center p-10">
            <img src="{{ asset('images/stock_select_logo-largo.png') }}" class="h-10 md:h-12 mb-8" alt="{{ $settings['site_title'] ?? 'Stock Select' }}">
            <h1 class="text-2xl md:text-3xl font-black mb-4 tracking-tight">Estamos mejorando la tienda</h1>
            <p class="text-gray-500 max-w-sm text-sm">Volveremos pronto con la mejor selección de moda urbana. Gracias por tu paciencia.</p>
            <div class="mt-8 flex gap-4">
                @if(!empty($settings['social_instagram']))
                    <a href="{{ $settings['social_instagram'] }}" class="text-gray-400 hover:text-black transition-colors"><svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                @endif
            </div>
        </div>
    @endif

    @if(($settings['promo_bar_active'] ?? '0') === '1')
        <div class="bg-black py-2.5 px-5 border-b border-gray-800">
            <div class="max-w-7xl mx-auto text-center">
                <p class="text-white text-[10px] md:text-[11px] font-bold tracking-[0.2em] uppercase">
                    {{ $settings['promo_bar_text'] ?? 'Envíos gratis en pedidos superiores a 50€' }}
                </p>
            </div>
        </div>
    @endif

    <!-- ===== LOADING BAR (Apple Style) ===== -->
    <div x-show="$store.appNav.loading" 
         x-transition:enter="transition opacity-100"
         x-transition:leave="transition opacity-0 duration-500"
         class="fixed top-0 left-0 w-full h-[3px] z-[100] bg-gray-100 overflow-hidden" x-cloak>
        <div class="h-full bg-gray-900 animate-[loading_2s_ease-in-out_infinite]" style="width: 40%"></div>
    </div>
    <style>
        @keyframes loading {
            0% { transform: translateX(-100%); width: 30%; }
            50% { width: 70%; }
            100% { transform: translateX(400%); width: 30%; }
        }
    </style>

    <!-- ===== HEADER GLASSMORPHISM ===== -->
    <header class="glass sticky top-0 z-50 bg-white/80 border-b border-white/40 shadow-[0_1px_20px_0_rgba(0,0,0,0.04)]">
        <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
            <div class="flex items-center justify-between h-[60px]">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 group select-none transition-all hover:scale-[1.02] active:scale-95">
                    <img src="{{ asset('images/stock_select_logo-largo.png') }}" alt="{{ $settings['site_title'] ?? 'Stock Select' }}" class="h-8 md:h-10 w-auto mix-blend-multiply">
                </a>

                <!-- Nav Links – Desktop -->
                <nav id="app-header-nav" class="hidden md:flex items-center gap-7">
                    <a href="{{ route('home') }}" 
                       @click.prevent="$store.appNav.load($el.href)"
                       class="nav-link {{ !request('genero') ? 'text-gray-900 font-bold' : '' }}">Inicio</a>
                    <a href="{{ route('home') }}?genero=hombre" 
                       @click.prevent="$store.appNav.load($el.href)"
                       class="nav-link {{ request('genero') === 'hombre' ? 'text-gray-900 font-bold border-b-2 border-gray-900' : '' }}">Hombre</a>
                    <a href="{{ route('home') }}?genero=mujer" 
                       @click.prevent="$store.appNav.load($el.href)"
                       class="nav-link {{ request('genero') === 'mujer' ? 'text-gray-900 font-bold border-b-2 border-gray-900' : '' }}">Mujer</a>
                    <a href="{{ route('home') }}?genero=ninos" 
                       @click.prevent="$store.appNav.load($el.href)"
                       class="nav-link {{ request('genero') === 'ninos' ? 'text-gray-900 font-bold border-b-2 border-gray-900' : '' }}">Niños</a>
                </nav>

                <!-- Actions -->
                <div class="flex items-center gap-1">

                    @php $cartCount = collect(session()->get('cart', []))->sum('quantity'); @endphp

                    <!-- Search Component -->
                    <div class="relative flex items-center" x-data="{ localSearch: '' }">
                        <!-- Desktop Expandable Search -->
                        <div class="hidden md:flex items-center">
                            <input 
                                type="text" 
                                x-model="localSearch"
                                x-show="$store.appNav.searchOpen"
                                x-transition:enter="transition-all ease-out duration-300"
                                x-transition:enter-start="w-0 opacity-0"
                                x-transition:enter-end="w-64 opacity-100"
                                x-transition:leave="transition-all ease-in duration-200"
                                x-transition:leave-start="w-64 opacity-100"
                                x-transition:leave-end="w-0 opacity-0"
                                x-ref="searchInput"
                                @keydown.enter="$store.appNav.performSearch(localSearch)"
                                @keydown.escape="$store.appNav.searchOpen = false"
                                @click.outside="$store.appNav.searchOpen = false"
                                placeholder="Buscar productos..."
                                class="bg-gray-100/50 border-none focus:ring-1 focus:ring-black h-9 rounded-full text-[13px] px-4 mr-2 outline-none"
                            >
                            <button @click="$store.appNav.searchOpen = !$store.appNav.searchOpen; if($store.appNav.searchOpen) $nextTick(() => $refs.searchInput.focus())" 
                                class="flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100/80 transition-colors">
                                <svg x-show="!$store.appNav.searchOpen" class="w-5 h-5 stroke-gray-800" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <svg x-show="$store.appNav.searchOpen" x-cloak class="w-4 h-4 stroke-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Mobile Search Icon Only -->
                        <button @click="$store.appNav.searchOpen = true; $nextTick(() => $refs.mobileSearchInput.focus())" 
                            class="md:hidden flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100/80 transition-colors">
                            <svg class="w-5 h-5 stroke-gray-800" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Full-Width Overlay -->
                    <template x-if="$store.appNav.searchOpen">
                        <div class="fixed inset-0 z-[60] md:hidden bg-white px-5 h-[60px] flex items-center gap-3 shadow-md"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="flex-1 relative flex items-center">
                                <input 
                                    type="text" 
                                    x-model="localSearch"
                                    x-ref="mobileSearchInput"
                                    @keydown.enter="$store.appNav.performSearch(localSearch)"
                                    @keydown.escape="$store.appNav.searchOpen = false"
                                    placeholder="Buscar productos..."
                                    class="w-full bg-gray-100 border-none focus:ring-0 h-10 rounded-xl text-sm px-4 pr-10 outline-none"
                                >
                                <button x-show="localSearch.length > 0" @click="localSearch = ''" class="absolute right-3 text-gray-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <button @click="$store.appNav.searchOpen = false" class="text-[13px] font-bold text-gray-500 px-2">
                                Cancelar
                            </button>
                        </div>
                    </template>

                    <!-- Cart Icon -->
                    <a href="{{ route('cart.index') }}"
                        class="relative flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100/80 transition-colors duration-200">
                        <svg class="w-[22px] h-[22px] stroke-gray-800" fill="none" stroke="currentColor"
                            stroke-width="1.6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        @if($cartCount > 0)
                            <span
                                class="absolute top-1 right-1 bg-gray-900 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center leading-none">
                                {{ $cartCount > 9 ? '9+' : $cartCount }}
                            </span>
                        @endif
                    </a>

                    <!-- WhatsApp pill – Desktop -->
                    <a href="https://wa.me/{{ $settings['whatsapp_number'] ?? '34600000000' }}?text={{ urlencode($settings['whatsapp_default_message'] ?? '¡Hola!') }}" target="_blank" rel="noopener"
                        class="hidden sm:flex items-center gap-1.5 text-[12px] font-semibold tracking-wide text-white bg-gray-900 px-4 py-2 rounded-full ml-2 hover:bg-gray-700 transition-colors duration-200 shadow-sm">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        WhatsApp
                    </a>

                    <!-- Hamburger – Mobile -->
                    <button @click="$store.appNav.mobileOpen = !$store.appNav.mobileOpen"
                        class="md:hidden ml-1 w-10 h-10 flex items-center justify-center rounded-xl hover:bg-gray-100/80 transition-colors duration-200">
                        <svg x-show="!$store.appNav.mobileOpen" class="w-5 h-5 stroke-gray-800" fill="none" stroke="currentColor"
                            stroke-width="1.7" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="$store.appNav.mobileOpen" x-cloak class="w-5 h-5 stroke-gray-800" fill="none"
                            stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div x-show="$store.appNav.mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden glass bg-white/90 border-t border-gray-100/80 px-5 py-5">
            <nav id="app-mobile-nav" class="flex flex-col gap-1">
                <a href="{{ route('home') }}" 
                    @click.prevent="$store.appNav.load($el.href)"
                    class="flex items-center gap-3 px-3 py-3 text-[14px] font-bold {{ !request('genero') && !request('search') && (!request('category') || request('category') === 'Todo') ? 'text-white bg-gray-950 shadow-lg' : 'text-gray-700 hover:bg-gray-50/80' }} rounded-xl transition-all duration-300">
                    <svg class="w-4 h-4 stroke-current {{ !request('genero') && !request('search') && (!request('category') || request('category') === 'Todo') ? 'opacity-100' : 'opacity-60' }}" fill="none" stroke="currentColor" stroke-width="1.7"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Inicio
                </a>
                <a href="{{ route('home') }}?genero=hombre" 
                    @click.prevent="$store.appNav.load($el.href)"
                    class="flex items-center gap-3 px-3 py-3 text-[14px] font-bold {{ request('genero') === 'hombre' ? 'text-white bg-gray-950 shadow-lg' : 'text-gray-700 hover:bg-gray-50/80' }} rounded-xl transition-all duration-300">
                    Hombre
                </a>
                <a href="{{ route('home') }}?genero=mujer" 
                    @click.prevent="$store.appNav.load($el.href)"
                    class="flex items-center gap-3 px-3 py-3 text-[14px] font-bold {{ request('genero') === 'mujer' ? 'text-white bg-gray-950 shadow-lg' : 'text-gray-700 hover:bg-gray-50/80' }} rounded-xl transition-all duration-300">
                    Mujer
                </a>
                <a href="{{ route('home') }}?genero=ninos" 
                    @click.prevent="$store.appNav.load($el.href)"
                    class="flex items-center gap-3 px-3 py-3 text-[14px] font-bold {{ request('genero') === 'ninos' ? 'text-white bg-gray-950 shadow-lg' : 'text-gray-700 hover:bg-gray-50/80' }} rounded-xl transition-all duration-300">
                    Niños
                </a>

                <div class="my-1 border-t border-gray-100"></div>

                <a href="{{ route('cart.index') }}" @click="mobileOpen = false"
                    class="flex items-center justify-between px-3 py-3 text-[14px] font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50/80 rounded-xl transition-colors">
                    <span class="flex items-center gap-3">
                        <svg class="w-4 h-4 stroke-current opacity-60" fill="none" stroke="currentColor"
                            stroke-width="1.7" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Mi carrito
                    </span>
                    @if($cartCount > 0)
                        <span
                            class="text-[11px] font-bold bg-gray-900 text-white px-2 py-0.5 rounded-full">{{ $cartCount }}</span>
                    @endif
                </a>

                <a href="https://wa.me/{{ $settings['whatsapp_number'] ?? '34600000000' }}?text={{ urlencode($settings['whatsapp_default_message'] ?? '¡Hola!') }}" target="_blank" rel="noopener"
                    class="flex items-center gap-3 px-3 py-3 text-[14px] font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50/80 rounded-xl transition-colors">
                    <svg class="w-4 h-4 opacity-60 fill-gray-700" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                    WhatsApp
                </a>
            </nav>
        </div>
    </header>

    <!-- ===== MAIN ===== -->
    <main class="flex-1" :class="{ 'opacity-50 pointer-events-none transition-opacity duration-300': $store.appNav.loading }">
        <div id="app-content">
            {{ $slot }}
        </div>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('appNav', {
                loading: false,
                mobileOpen: false,
                searchOpen: false,
                performSearch(term) {
                    if (!term.trim()) return;
                    this.searchOpen = false;
                    this.load(`{{ route('home') }}?search=${encodeURIComponent(term)}`);
                },
                async load(url, pushState = true) {
                    if (this.loading) return;
                    this.loading = true;
                    this.mobileOpen = false; // Close menu on navigation
                    
                    try {
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Network response was not ok');
                        
                        const html = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Update Title
                        document.title = doc.title;
                        
                        // Update app-content
                        const newContent = doc.getElementById('app-content');
                        if (newContent) {
                            document.getElementById('app-content').innerHTML = newContent.innerHTML;
                        } else {
                            window.location.href = url;
                            return;
                        }

                        // Update Navigation Active States (Header & Mobile)
                        const newHeaderNav = doc.getElementById('app-header-nav');
                        const newMobileNav = doc.getElementById('app-mobile-nav');
                        if (newHeaderNav) document.getElementById('app-header-nav').innerHTML = newHeaderNav.innerHTML;
                        if (newMobileNav) document.getElementById('app-mobile-nav').innerHTML = newMobileNav.innerHTML;
                        
                        // Update URL
                        if (pushState) {
                            window.history.pushState({ url }, '', url);
                        }
                        
                        // Scroll to top
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        
                        // Close mobile menu if open
                        if (this.mobileOpen) this.mobileOpen = false;
                        
                    } catch (error) {
                        console.error('Navigation error:', error);
                        window.location.href = url;
                    } finally {
                        this.loading = false;
                    }
                }
            });
        });

        window.addEventListener('popstate', (event) => {
            Alpine.store('appNav').load(window.location.href, false);
        });
    </script>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-gray-950 text-gray-400 mt-12 md:mt-20 border-t border-gray-900">
        <div class="h-px bg-gradient-to-r from-transparent via-gray-700 to-transparent w-full"></div>

        <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 pt-10 pb-32 md:pt-20 md:pb-16">

            {{-- ===== MOBILE LAYOUT ===== --}}
            <div class="lg:hidden">
                {{-- Marca + Social --}}
                <div class="flex items-center justify-between mb-6">
                    <a href="{{ route('home') }}" class="inline-block">
                        <img src="{{ asset('images/stock_select_logo-largo.png') }}" alt="{{ $settings['site_title'] ?? 'Stock Select' }}" class="h-7 w-auto brightness-0 invert">
                    </a>
                    <div class="flex items-center gap-2">
                        <a href="{{ !empty($settings['social_instagram']) ? $settings['social_instagram'] : 'https://instagram.com/stockselect.es' }}" target="_blank" rel="noopener"
                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gray-500 hover:text-white border border-white/5 transition-colors"
                           title="Instagram">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        @if(!empty($settings['social_tiktok']))
                            <a href="{{ $settings['social_tiktok'] }}" target="_blank" rel="noopener"
                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gray-500 hover:text-white border border-white/5 transition-colors"
                               title="TikTok">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.9-.32-1.9-.39-2.81-.12-.9.24-1.72.88-2.12 1.71-.39.89-.39 1.9-.09 2.81.3.92 1.05 1.71 1.91 2.11.9.43 2 .39 2.89-.12.82-.48 1.35-1.34 1.5-2.28.14-1.1.13-2.2.14-3.3 0-5.18-.01-10.37.03-15.56z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>

                <p class="text-[13px] leading-relaxed text-gray-500 mb-6">
                    Selección premium de moda urbana y deportiva. Stock exclusivo con descuentos de hasta el 70%.
                </p>

                {{-- Trust badges --}}
                <div class="flex flex-col gap-2.5 mb-6 pb-6 border-b border-white/5">
                    <div class="flex items-center gap-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-widest">
                        <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Selección Premium
                    </div>
                    <div class="flex items-center gap-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-widest">
                        <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Entrega en Mano (Mollerussa)
                    </div>
                    <div class="flex items-center gap-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-widest">
                        <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Pago 100% Seguro
                    </div>
                </div>

                {{-- Acordeones Alpine --}}
                <div class="divide-y divide-white/5" x-data="{ open: null }">
                    {{-- Colecciones --}}
                    <div>
                        <button @click="open = open === 'col' ? null : 'col'" class="w-full flex items-center justify-between py-3.5 text-left">
                            <span class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-300">Colecciones</span>
                            <svg class="w-4 h-4 text-gray-600 transition-transform duration-200" :class="open === 'col' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open === 'col'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="pb-4 space-y-3" style="display:none">
                            <a href="{{ route('home') }}" class="block text-[13px] text-gray-500 hover:text-gray-200 transition-colors">Todos los productos</a>
                            @foreach(\App\Models\Category::all() as $cat)
                                <a href="{{ route('home') }}?category={{ urlencode($cat->name) }}" class="block text-[13px] text-gray-500 hover:text-gray-200 transition-colors">{{ $cat->name }}</a>
                            @endforeach
                        </div>
                    </div>
                    {{-- Atención --}}
                    <div>
                        <button @click="open = open === 'aten' ? null : 'aten'" class="w-full flex items-center justify-between py-3.5 text-left">
                            <span class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-300">Atención al Cliente</span>
                            <svg class="w-4 h-4 text-gray-600 transition-transform duration-200" :class="open === 'aten' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open === 'aten'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="pb-4 space-y-3" style="display:none">
                            <a href="{{ route('pages.tracking') }}" class="block text-[13px] text-gray-500 hover:text-gray-200 transition-colors">Seguimiento de pedido</a>
                            <a href="{{ route('pages.faq') }}" class="block text-[13px] text-gray-500 hover:text-gray-200 transition-colors">Preguntas frecuentes</a>
                            <a href="{{ route('pages.size-guide') }}" class="block text-[13px] text-gray-500 hover:text-gray-200 transition-colors">Guía de tallas</a>
                            <a href="mailto:{{ $settings['legal_email'] ?? 'info@stockselect.es' }}" class="flex items-center gap-2 text-[13px] text-gray-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                {{ $settings['legal_email'] ?? 'info@stockselect.es' }}
                            </a>
                            <a href="https://wa.me/{{ $settings['whatsapp_number'] ?? '34600000000' }}?text={{ urlencode($settings['whatsapp_default_message'] ?? '¡Hola!') }}"
                               target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2 text-[12px] font-bold text-white bg-[#25D366] px-3.5 py-2 rounded-xl mt-1">
                                <svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                WhatsApp Directo
                            </a>
                        </div>
                    </div>
                    {{-- Legal --}}
                    <div>
                        <button @click="open = open === 'leg' ? null : 'leg'" class="w-full flex items-center justify-between py-3.5 text-left">
                            <span class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-300">Legal</span>
                            <svg class="w-4 h-4 text-gray-600 transition-transform duration-200" :class="open === 'leg' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open === 'leg'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="pb-4 space-y-3" style="display:none">
                            <a href="{{ route('pages.privacy') }}" class="block text-[13px] text-gray-500 hover:text-gray-200 transition-colors">Privacidad</a>
                            <a href="{{ route('pages.terms') }}" class="block text-[13px] text-gray-500 hover:text-gray-200 transition-colors">Términos</a>
                            <a href="{{ route('pages.cookies') }}" class="block text-[13px] text-gray-500 hover:text-gray-200 transition-colors">Cookies</a>
                            <a href="{{ route('pages.legal') }}" class="block text-[13px] text-gray-500 hover:text-gray-200 transition-colors">Aviso Legal</a>
                        </div>
                    </div>
                </div>

                {{-- Bottom mobile --}}
                <div class="mt-6 pt-5 border-t border-white/5">
                    <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2.5 bg-white/[0.03] border border-white/5 rounded-2xl px-5 py-3 mb-5">
                        <img src="{{ asset('images/payments/visa.svg') }}?v=3" alt="Visa" class="h-4 w-auto opacity-50">
                        <img src="{{ asset('images/payments/mastercard.svg') }}?v=3" alt="Mastercard" class="h-4 w-auto opacity-50">
                        <img src="{{ asset('images/payments/paypal.svg') }}?v=3" alt="PayPal" class="h-4 w-auto opacity-50">
                        <img src="{{ asset('images/payments/amex.svg') }}?v=3" alt="American Express" class="h-4 w-auto opacity-50">
                        <img src="{{ asset('images/payments/applepay_gris.svg') }}?v=2" alt="Apple Pay" class="h-4 w-auto opacity-50">
                        <img src="{{ asset('images/payments/googlepay_gris.svg') }}?v=2" alt="Google Pay" class="h-4 w-auto opacity-50">
                        <img src="{{ asset('images/payments/klarna.svg') }}?v=3" alt="Klarna" class="h-3.5 w-auto opacity-50">
                        <img src="{{ asset('images/payments/bizum.svg') }}?v=3" alt="Bizum" class="h-4 w-auto opacity-50">
                        <img src="{{ asset('images/payments/scalipay.svg') }}" alt="Scalipay" class="h-4 w-auto opacity-50">
                        <img src="{{ asset('images/payments/revolut_gris.svg') }}" alt="Revolut" class="h-4 w-auto opacity-50">
                    </div>
                    <p class="text-center text-[11px] text-gray-700 leading-relaxed">
                        © {{ date('Y') }} <span class="text-gray-500 font-semibold">{{ $settings['site_title'] ?? 'STOCK SELECT' }}</span><br>
                        Todos los derechos reservados
                    </p>
                </div>
            </div>

            {{-- ===== DESKTOP LAYOUT ===== --}}
            <div class="hidden lg:grid lg:grid-cols-4 lg:gap-8">
                {{-- Columna 1: Marca --}}
                <div class="flex flex-col gap-5">
                    <a href="{{ route('home') }}" class="inline-block transition-opacity hover:opacity-80">
                        <img src="{{ asset('images/stock_select_logo-largo.png') }}" alt="{{ $settings['site_title'] ?? 'Stock Select' }}" class="h-9 w-auto brightness-0 invert">
                    </a>
                    <p class="text-[13px] leading-relaxed text-gray-500 max-w-xs">
                        Selección premium de moda urbana y deportiva. Stock exclusivo con descuentos de hasta el 70%.
                    </p>
                    <div class="flex flex-col gap-2.5">
                        <div class="flex items-center gap-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-widest">
                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Selección Premium
                        </div>
                        <div class="flex items-center gap-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-widest">
                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Entrega en Mano (Mollerussa)
                        </div>
                        <div class="flex items-center gap-2.5 text-[11px] font-semibold text-gray-500 uppercase tracking-widest">
                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Pago 100% Seguro
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 pt-1">
                        <a href="{{ !empty($settings['social_instagram']) ? $settings['social_instagram'] : 'https://instagram.com/stockselect.es' }}" target="_blank" rel="noopener"
                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-gray-500 hover:text-white transition-all duration-200 border border-white/5"
                           title="Instagram">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        @if(!empty($settings['social_tiktok']))
                            <a href="{{ $settings['social_tiktok'] }}" target="_blank" rel="noopener"
                               class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-gray-500 hover:text-white transition-all duration-200 border border-white/5"
                               title="TikTok">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.9-.32-1.9-.39-2.81-.12-.9.24-1.72.88-2.12 1.71-.39.89-.39 1.9-.09 2.81.3.92 1.05 1.71 1.91 2.11.9.43 2 .39 2.89-.12.82-.48 1.35-1.34 1.5-2.28.14-1.1.13-2.2.14-3.3 0-5.18-.01-10.37.03-15.56z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Columna 2: Colecciones --}}
                <div>
                    <h3 class="text-gray-200 text-[11px] font-black uppercase tracking-[0.25em] mb-5">Colecciones</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}" class="text-[13px] text-gray-500 hover:text-gray-200 transition-colors duration-200">Todos los productos</a></li>
                        @foreach(\App\Models\Category::all() as $cat)
                            <li><a href="{{ route('home') }}?category={{ urlencode($cat->name) }}" class="text-[13px] text-gray-500 hover:text-gray-200 transition-colors duration-200">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Columna 3: Soporte --}}
                <div>
                    <h3 class="text-gray-200 text-[11px] font-black uppercase tracking-[0.25em] mb-5">Atención al Cliente</h3>
                    <ul class="space-y-3 mb-6">
                        <li><a href="{{ route('pages.tracking') }}" class="text-[13px] text-gray-500 hover:text-gray-200 transition-colors duration-200">Seguimiento de pedido</a></li>
                        <li><a href="{{ route('pages.faq') }}" class="text-[13px] text-gray-500 hover:text-gray-200 transition-colors duration-200">Preguntas frecuentes</a></li>
                        <li><a href="{{ route('pages.size-guide') }}" class="text-[13px] text-gray-500 hover:text-gray-200 transition-colors duration-200">Guía de tallas</a></li>
                        <li>
                            <a href="mailto:{{ $settings['legal_email'] ?? 'info@stockselect.es' }}" class="flex items-center gap-2 text-[13px] text-gray-400 hover:text-white transition-colors duration-200">
                                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                {{ $settings['legal_email'] ?? 'info@stockselect.es' }}
                            </a>
                        </li>
                    </ul>
                    <a href="https://wa.me/{{ $settings['whatsapp_number'] ?? '34600000000' }}?text={{ urlencode($settings['whatsapp_default_message'] ?? '¡Hola! Me gustaría información sobre Stock Select.') }}"
                        target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 text-[13px] font-bold text-white bg-[#25D366] hover:bg-[#20bd5a] px-4 py-2.5 rounded-xl transition-all duration-200 shadow-lg shadow-green-900/20 active:scale-95">
                        <svg class="w-4 h-4 fill-white flex-shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp Directo
                    </a>
                </div>

                {{-- Columna 4: Legal --}}
                <div>
                    <h3 class="text-gray-200 text-[11px] font-black uppercase tracking-[0.25em] mb-5">Información Legal</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('pages.privacy') }}" class="text-[13px] text-gray-500 hover:text-gray-200 transition-colors duration-200">Políticas de Privacidad</a></li>
                        <li><a href="{{ route('pages.terms') }}" class="text-[13px] text-gray-500 hover:text-gray-200 transition-colors duration-200">Términos y Condiciones</a></li>
                        <li><a href="{{ route('pages.cookies') }}" class="text-[13px] text-gray-500 hover:text-gray-200 transition-colors duration-200">Políticas de Cookies</a></li>
                        <li><a href="{{ route('pages.legal') }}" class="text-[13px] text-gray-500 hover:text-gray-200 transition-colors duration-200">Aviso Legal</a></li>
                    </ul>
                </div>

                {{-- Bottom Bar Desktop --}}
                <div class="col-span-4 mt-10 pt-8 border-t border-white/5 flex justify-between items-center">
                    <div>
                        <p class="text-[12px] text-gray-600">© {{ date('Y') }} <span class="text-gray-400 font-semibold">{{ $settings['site_title'] ?? ($settings['legal_shop_name'] ?? 'STOCK SELECT') }}</span> · Todos los derechos reservados</p>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-gray-800 mt-1">Premium Curated Selection · Boutique Outlet</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2.5 bg-white/[0.03] border border-white/5 rounded-2xl px-5 py-3">
                        <img src="{{ asset('images/payments/visa.svg') }}?v=3" alt="Visa" class="h-4 w-auto opacity-50 hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/payments/mastercard.svg') }}?v=3" alt="Mastercard" class="h-4 w-auto opacity-50 hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/payments/paypal.svg') }}?v=3" alt="PayPal" class="h-4 w-auto opacity-50 hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/payments/amex.svg') }}?v=3" alt="American Express" class="h-4 w-auto opacity-50 hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/payments/applepay_gris.svg') }}?v=2" alt="Apple Pay" class="h-4 w-auto opacity-50 hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/payments/googlepay_gris.svg') }}?v=2" alt="Google Pay" class="h-4 w-auto opacity-50 hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/payments/klarna.svg') }}?v=3" alt="Klarna" class="h-3.5 w-auto opacity-50 hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/payments/bizum.svg') }}?v=3" alt="Bizum" class="h-4 w-auto opacity-50 hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/payments/scalipay.svg') }}" alt="Scalipay" class="h-4 w-auto opacity-50 hover:opacity-100 transition-opacity">
                        <img src="{{ asset('images/payments/revolut_gris.svg') }}" alt="Revolut" class="h-4 w-auto opacity-50 hover:opacity-100 transition-opacity">
                    </div>
                </div>
            </div>

        </div>
    </footer>
    <!-- ===== COOKIE BANNER ===== -->
    <div x-data="{ 
            showCookieBanner: !localStorage.getItem('cookie_consent'),
            acceptAll() {
                localStorage.setItem('cookie_consent', 'accepted');
                this.showCookieBanner = false;
            },
            rejectAll() {
                localStorage.setItem('cookie_consent', 'rejected');
                this.showCookieBanner = false;
            }
         }" 
         x-show="showCookieBanner" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed bottom-0 left-0 w-full z-[60] bg-black/95 backdrop-blur-md border-t border-gray-800 p-4 md:p-6"
         x-cloak>
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex-1 text-center md:text-left">
                <p class="text-gray-300 text-sm leading-relaxed">
                    Utilizamos cookies propias (estrictamente necesarias para el carrito y sesión) y de terceros (para análisis estadístico). 
                    Puedes elegir qué cookies permitir. Consulta nuestra 
                    <a href="{{ route('pages.cookies') }}" class="text-white underline hover:text-gray-200 transition-colors">Política de Cookies</a>.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                <button @click="rejectAll()" class="order-3 sm:order-1 text-gray-400 text-xs underline hover:text-white transition-colors py-2">
                    Configurar
                </button>
                <button @click="rejectAll()" class="order-2 sm:order-2 w-full sm:w-auto border border-gray-600 text-white px-6 py-2.5 rounded-xl hover:bg-gray-800 transition font-medium text-sm">
                    Rechazar Todo
                </button>
                <button @click="acceptAll()" class="order-1 sm:order-3 w-full sm:w-auto bg-white text-black px-6 py-2.5 rounded-xl hover:bg-gray-200 transition font-bold text-sm">
                    Aceptar Todo
                </button>
            </div>
        </div>
    </div>
</body>


</html>